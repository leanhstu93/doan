<?php

namespace App\Filament\Student\Resources\FileSubmissionResource\Pages;

use App\Filament\Student\Resources\FileSubmissionResource;
use App\Models\Student;
use App\Models\ThesisGroup;
use Filament\Resources\Pages\ListRecords;

class ListFileSubmissions extends ListRecords
{
    protected static string $resource = FileSubmissionResource::class;

    public ?ThesisGroup $group = null;

    public function mount(): void
    {
        parent::mount();

        $student = Student::where('user_id', auth()->id())->first();

        if ($student) {
            $groupMember = $student->thesisGroupMembers->first();
            if ($groupMember) {
                $this->group = $groupMember->group;
            }
        }
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Thêm thông báo nếu không thể nộp file
        if (!FileSubmissionResource::canCreate()) {
            $actions[] = \Filament\Actions\Action::make('notice')
                ->label('Thông báo')
                ->icon('heroicon-o-information-circle')
                ->color('warning')
                ->disabled()
                ->extraAttributes(['class' => 'opacity-100 cursor-not-allowed'])
                ->tooltip($this->getCannotCreateMessage());
        }

        $actions[] = \Filament\Actions\CreateAction::make()
            ->label('Nộp file mới')
            ->icon('heroicon-o-plus');

        return $actions;
    }

    /**
     * Get message why student cannot create submission
     */
    protected function getCannotCreateMessage(): string
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return 'Bạn chưa được đăng ký là sinh viên trong hệ thống.';
        }

        $groupMember = $student->thesisGroupMembers->first();
        if (!$groupMember) {
            return 'Bạn chưa được phân nhóm. Vui lòng liên hệ giáo vụ.';
        }

        $group = $groupMember->group;
        if (!$group || !$group->topic_id) {
            return 'Nhóm của bạn chưa đăng ký đề tài.';
        }

        $topic = $group->topic;
        if (!$topic) {
            return 'Không tìm thấy thông tin đề tài.';
        }

        if ($topic->status === 'pending') {
            return 'Đề tài của bạn đang chờ duyệt. Vui lòng chờ giảng viên phê duyệt.';
        }

        if ($topic->status === 'rejected') {
            return 'Đề tài của bạn đã bị từ chối. Vui lòng đăng ký đề tài khác.';
        }

        // Kiểm tra giai đoạn nộp
        $hasActivePhase = \App\Models\SubmissionPhase::active()
            ->where(function ($query) {
                $query->whereNull('open_date')
                    ->orWhere('open_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('close_date')
                    ->orWhere('close_date', '>=', now());
            })
            ->exists();

        if (!$hasActivePhase) {
            return 'Hiện không có giai đoạn nộp file nào đang mở.';
        }

        return 'Không thể nộp file vào thời điểm này.';
    }
}
