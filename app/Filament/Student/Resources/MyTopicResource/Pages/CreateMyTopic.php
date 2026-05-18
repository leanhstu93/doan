<?php

namespace App\Filament\Student\Resources\MyTopicResource\Pages;

use App\Filament\Student\Resources\MyTopicResource;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\ThesisGroup;
use App\Models\ThesisGroupMember;
use App\Models\ThesisTopic;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class CreateMyTopic extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = MyTopicResource::class;

    protected string $view = 'filament.student.resources.my-topic-resource.pages.create-topic';

    protected static ?string $title = 'Nhập đề tài';

    public ?Student $student = null;

    public ?ThesisGroup $group = null;

    public bool $canSubmit = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();

        if (!$this->student) {
            redirect(url('/student'))->with('error', 'Không tìm thấy thông tin sinh viên.');
            return;
        }

        $groupMember = $this->student->thesisGroupMembers->first();
        $this->group = $groupMember?->group;

        // Check if group already has a topic
        if ($this->group?->topic_id) {
            redirect(MyTopicResource::getUrl('edit'));
            return;
        }

        $this->canSubmit = true;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Textarea::make('ten_de_tai_tv')
                    ->label('Tên đề tài tiếng Việt')
                    ->required()
                    ->rows(2),
                Textarea::make('ten_de_tai_ta')
                    ->label('Tên đề tài tiếng Anh')
                    ->nullable()
                    ->rows(2),
                Select::make('gvhd_id')
                    ->label('Giảng viên hướng dẫn')
                    ->options(function () {
                        return Lecturer::with('user')
                            ->get()
                            ->mapWithKeys(function ($lecturer) {
                                return [$lecturer->id => $lecturer->display_name];
                            });
                    })
                    ->searchable()
                    ->nullable()
                    ->native(false),
            ]);
    }

    public function submit(): void
    {
        if (!$this->canSubmit) {
            return;
        }

        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            if (!$this->group) {
                $this->group = ThesisGroup::create([
                    'group_code' => $this->makeIndividualGroupCode(),
                    'topic_id' => null,
                    'academic_year_id' => $this->student->academic_year_id,
                ]);

                ThesisGroupMember::create([
                    'group_id' => $this->group->id,
                    'student_id' => $this->student->id,
                    'is_leader' => true,
                ]);
            }

            // Create the topic
            $topic = ThesisTopic::create([
                'ten_de_tai_tv' => $data['ten_de_tai_tv'],
                'ten_de_tai_ta' => $data['ten_de_tai_ta'] ?? null,
                'gvhd_id' => $data['gvhd_id'],
                'academic_year_id' => $this->group->academic_year_id,
                'group_id' => $this->group->id,
                'submitted_by' => auth()->id(),
                'status' => 'pending',
            ]);

            // Update group with topic_id
            $this->group->update(['topic_id' => $topic->id]);

            \Filament\Notifications\Notification::make()
                ->success()
                ->title('Thành công')
                ->body('Đề tài đã được gửi để duyệt.')
                ->send();
        });

        redirect(MyTopicResource::getUrl('edit'));
    }

    protected function makeIndividualGroupCode(): string
    {
        $baseCode = 'CN-' . $this->student->mssv;
        $groupCode = $baseCode;
        $counter = 1;

        while (ThesisGroup::where('group_code', $groupCode)->exists()) {
            $counter++;
            $groupCode = $baseCode . '-' . $counter;
        }

        return $groupCode;
    }
}
