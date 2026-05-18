<?php

namespace App\Filament\Student\Resources\FileSubmissionResource\Pages;

use App\Filament\Student\Resources\FileSubmissionResource;
use App\Models\FileSubmission;
use App\Models\Student;
use App\Models\SubmissionPhase;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Storage;

class CreateFileSubmission extends CreateRecord
{
    protected static string $resource = FileSubmissionResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Nộp file mới';

    protected ?Student $student = null;

    protected ?object $group = null;

    protected ?int $preselectedPhaseId = null;

    protected ?SubmissionPhase $preselectedPhase = null;

    protected function getRedirectUrl(): string
    {
        return $this->record
            ? $this->getResource()::getUrl('view', ['record' => $this->record])
            : url('/student');
    }

    public function mount(): void
    {
        parent::mount();

        // Kiểm tra điều kiện nộp file
        if (!FileSubmissionResource::canCreate()) {
            Notification::make()
                ->danger()
                ->title('Không thể nộp file')
                ->body('Bạn chỉ có thể nộp file khi đề tài đã được duyệt và đang trong giai đoạn nộp.')
                ->send();

            redirect(url('/student'));
            return;
        }

        // Lấy phase_id từ URL parameter
        $this->preselectedPhaseId = request()->query('phase_id');

        // Validate phase nếu có
        if ($this->preselectedPhaseId) {
            $this->preselectedPhase = SubmissionPhase::find($this->preselectedPhaseId);

            if (!$this->preselectedPhase || !$this->preselectedPhase->isOpen()) {
                Notification::make()
                    ->danger()
                    ->title('Lỗi')
                    ->body('Giai đoạn nộp file không hợp lệ.')
                    ->send();

                redirect(url('/student'));
                return;
            }

            // Pre-fill form với phase_id
            $this->form->fill([
                'phase_id' => (int) $this->preselectedPhaseId,
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $this->student = Student::where('user_id', $user->id)->first();
        $groupMember = $this->student?->thesisGroupMembers?->first();
        $this->group = $groupMember?->group;

        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Nộp file mới cho nhóm: ' . ($this->group?->group_code ?? 'N/A'))
                    ->schema([
                        Select::make('phase_id')
                            ->label('Giai đoạn nộp')
                            ->required()
                            ->options(function () {
                                // Nếu có preselected phase, chỉ hiển thị phase đó
                                if ($this->preselectedPhase) {
                                    return [$this->preselectedPhase->id => $this->preselectedPhase->name];
                                }
                                return $this->getAvailablePhases();
                            })
                            ->searchable()
                            ->native(false)
                            ->dehydrated()
                            ->helperText($this->preselectedPhase
                                ? 'Bạn đang nộp file cho giai đoạn: ' . $this->preselectedPhase->name
                                : (empty($this->getAvailablePhases()) ? 'Bạn đã nộp file cho tất cả các giai đoạn hoặc không có giai đoạn nào mở.' : 'Chọn giai đoạn bạn muốn nộp file. Mỗi giai đoạn chỉ được nộp 1 file.')
                            )
                            ->disabled(fn () => $this->preselectedPhase || empty($this->getAvailablePhases())),

                        Select::make('file_type')
                            ->label('Loại file')
                            ->required()
                            ->options([
                                'word' => 'Word (.doc, .docx)',
                                'pdf' => 'PDF (.pdf)',
                                'ppt' => 'PowerPoint (.ppt, .pptx)',
                            ])
                            ->default('word')
                            ->native(false),

                        FileUpload::make('file')
                            ->label('File đính kèm')
                            ->required()
                            ->disk('local')
                            ->directory('thesis-submissions')
                            ->acceptedFileTypes([
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/pdf',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            ])
                            ->maxSize(10240)
                            ->helperText('Chấp nhận file Word, PDF, PowerPoint. Tối đa 10MB.'),

                        Textarea::make('note')
                            ->label('Ghi chú')
                            ->placeholder('Ghi chú cho file nộp (nếu có)')
                            ->rows(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        $groupMember = $student?->thesisGroupMembers?->first();
        $group = $groupMember?->group;

        // Validate: Check if submission already exists for this phase
        $existingSubmission = FileSubmission::where('topic_id', $group->topic_id)
            ->where('phase_id', $data['phase_id'])
            ->first();

        if ($existingSubmission) {
            Notification::make()
                ->danger()
                ->title('Lỗi')
                ->body('Bạn đã nộp file cho giai đoạn này rồi. Mỗi giai đoạn chỉ được nộp 1 file.')
                ->send();
            $this->halt();
        }

        $data['topic_id'] = $group->topic_id;
        $data['submitted_by'] = $user->id;
        $data['status'] = 'pending';

        // Handle file upload
        if (!empty($data['file'])) {
            $fileKey = is_array($data['file']) ? reset($data['file']) : $data['file'];

            $originalName = basename($fileKey);
            $phase = SubmissionPhase::find($data['phase_id']);
            $groupCode = $group?->group_code ?? 'unknown';
            $phaseCode = $phase?->code ?? 'unknown';

            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $storedFileName = sprintf(
                '%s_%s_%s_%s.%s',
                $groupCode,
                $phaseCode,
                $user->id,
                now()->format('Ymd_His'),
                $extension
            );

            $storagePath = "thesis-submissions/{$groupCode}/{$phaseCode}";
            $fullPath = "{$storagePath}/{$storedFileName}";

            if (Storage::disk('local')->exists($fileKey)) {
                Storage::disk('local')->makeDirectory($storagePath);
                Storage::disk('local')->move($fileKey, $fullPath);
            }

            $data['file_path'] = $fullPath;
            $data['original_name'] = $originalName;
        }

        unset($data['file']);

        return $data;
    }

    /**
     * Get available phases for dropdown
     */
    protected function getAvailablePhases(): array
    {
        if (!$this->group || !$this->group->topic_id) {
            return [];
        }

        $submittedPhaseIds = FileSubmission::where('topic_id', $this->group->topic_id)
            ->pluck('phase_id')
            ->toArray();

        return SubmissionPhase::active()
            ->where(function ($query) {
                $query->whereNull('open_date')
                    ->orWhere('open_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('close_date')
                    ->orWhere('close_date', '>=', now());
            })
            ->whereNotIn('id', $submittedPhaseIds)
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}
