<?php

namespace App\Livewire\Admin\Tables;

use App\Models\FileSubmission;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class GroupFileSubmissions extends Component implements Tables\Contracts\HasTable, Forms\Contracts\HasForms, Actions\Contracts\HasActions
{
    use Tables\Concerns\InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;
    use Actions\Concerns\InteractsWithActions;

    public $topic_id = null;

    public function mount($topic_id = null, $record = null): void
    {
        if (is_numeric($topic_id)) {
            $this->topic_id = (int) $topic_id;
            return;
        }

        $this->topic_id = $record?->getKey();
    }

    protected function getTableQuery()
    {
        return FileSubmission::query()
            ->where('topic_id', $this->topic_id)
            ->with(['phase', 'submittedBy']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('phase.name')
                ->label('Giai đoạn'),

            Tables\Columns\TextColumn::make('original_name')
                ->label('Tên file')
                ->limit(40),

            Tables\Columns\TextColumn::make('file_type')
                ->label('Loại')
                ->formatStateUsing(fn (string $state): string => match($state) {
                    'word' => 'Word',
                    'pdf' => 'PDF',
                    'ppt' => 'PowerPoint',
                    default => $state,
                }),

            Tables\Columns\TextColumn::make('status')
                ->label('Trạng thái')
                ->formatStateUsing(fn (string $state): string => match($state) {
                    'pending' => 'Đang chờ duyệt',
                    'approved' => 'Đã duyệt',
                    'rejected' => 'Bị từ chối',
                    're_submitted' => 'Đã nộp lại',
                    default => $state,
                })
                ->badge()
                ->color(fn (string $state): string => match($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    're_submitted' => 'info',
                    default => 'gray',
                }),

            Tables\Columns\TextColumn::make('submittedBy.full_name')
                ->label('Người nộp'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Ngày nộp')
                ->dateTime('d/m/Y H:i'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label('Tải xuống')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn (FileSubmission $record) => Storage::disk('local')->download($record->file_path, $record->original_name)),

            Actions\Action::make('approve')
                ->label('Duyệt')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (FileSubmission $record) => in_array($record->status, ['pending', 're_submitted', 'rejected'], true))
                ->requiresConfirmation()
                ->modalHeading('Duyệt file')
                ->modalDescription('Bạn có chắc muốn duyệt file này?')
                ->action(function (FileSubmission $record) {
                    $record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'rejection_reason' => null,
                    ]);
                }),

            Actions\Action::make('reject')
                ->label('Từ chối')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn (FileSubmission $record) => in_array($record->status, ['pending', 're_submitted', 'approved'], true))
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Lý do từ chối')
                        ->required(),
                ])
                ->action(function (FileSubmission $record, array $data) {
                    $record->update([
                        'status' => 'rejected',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'rejection_reason' => $data['rejection_reason'],
                    ]);
                }),
        ];
    }

    public function render()
    {
        return view('livewire.admin.tables.group-file-submissions');
    }

    public function makeFilamentTranslatableContentDriver(): ?\Filament\Support\Contracts\TranslatableContentDriver
    {
        return null;
    }
}
