<?php

namespace App\Filament\Student\Resources\MyTopicResource\Pages;

use App\Filament\Student\Resources\MyTopicResource;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\ThesisGroup;
use App\Models\ThesisTopic;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class EditMyTopic extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = MyTopicResource::class;

    protected string $view = 'filament.student.resources.my-topic-resource.pages.edit-topic';

    protected static ?string $title = 'Chỉnh sửa đề tài';

    public ?Student $student = null;

    public ?ThesisGroup $group = null;

    public ?ThesisTopic $topic = null;

    public bool $canEdit = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();

        if (!$this->student) {
            redirect(url('/student'))->with('error', 'Không tìm thấy thông tin sinh viên.');
            return;
        }

        $groupMember = $this->student->thesisGroupMembers->first();

        if (!$groupMember) {
            redirect(url('/student'))->with('error', 'Bạn chưa thuộc nhóm nào.');
            return;
        }

        $this->group = $groupMember->group;

        if (!$this->group || !$this->group->topic_id) {
            redirect(MyTopicResource::getUrl('create'));
            return;
        }

        $this->topic = $this->group->topic;

        // Only allow edit if status is pending
        $this->canEdit = ($this->topic->status === 'pending');

        $this->form->fill([
            'ten_de_tai_tv' => $this->topic->ten_de_tai_tv,
            'ten_de_tai_ta' => $this->topic->ten_de_tai_ta,
            'gvhd_id' => $this->topic->gvhd_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Textarea::make('ten_de_tai_tv')
                    ->label('Tên đề tài tiếng Việt')
                    ->required()
                    ->rows(2)
                    ->disabled(!$this->canEdit),
                Textarea::make('ten_de_tai_ta')
                    ->label('Tên đề tài tiếng Anh')
                    ->nullable()
                    ->rows(2)
                    ->disabled(!$this->canEdit),
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
                    ->native(false)
                    ->disabled(!$this->canEdit),
            ]);
    }

    public function submit(): void
    {
        if (!$this->canEdit || !$this->topic) {
            return;
        }

        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            $this->topic->update([
                'ten_de_tai_tv' => $data['ten_de_tai_tv'],
                'ten_de_tai_ta' => $data['ten_de_tai_ta'] ?? null,
                'gvhd_id' => $data['gvhd_id'],
            ]);

            \Filament\Notifications\Notification::make()
                ->success()
                ->title('Thành công')
                ->body('Đề tài đã được cập nhật.')
                ->send();
        });
    }
}
