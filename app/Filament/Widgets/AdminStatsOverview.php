<?php

namespace App\Filament\Widgets;

use App\Models\FileSubmission;
use App\Models\Lecturer;
use App\Models\Review;
use App\Models\Student;
use App\Models\ThesisGroupMember;
use App\Models\ThesisTopic;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $studentCount = Student::count();
        $studentsWithTopic = ThesisGroupMember::query()
            ->whereHas('group.topic')
            ->distinct('student_id')
            ->count('student_id');

        $pendingTopics = ThesisTopic::where('status', ThesisTopic::STATUS_PENDING)->count();
        $approvedTopics = ThesisTopic::where('status', ThesisTopic::STATUS_APPROVED)->count();
        $rejectedTopics = ThesisTopic::where('status', ThesisTopic::STATUS_REJECTED)->count();

        $pendingFiles = FileSubmission::whereIn('status', ['pending', 're_submitted'])->count();
        $approvedFiles = FileSubmission::where('status', 'approved')->count();
        $rejectedFiles = FileSubmission::where('status', 'rejected')->count();

        return [
            Stat::make('Sinh viên', $studentCount)
                ->description('Tổng số sinh viên')
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),
            Stat::make('Đã có đề tài', $studentsWithTopic)
                ->description('Chưa có: ' . max(0, $studentCount - $studentsWithTopic))
                ->icon('heroicon-o-document-check')
                ->color('success'),
            Stat::make('Đề tài chờ duyệt', $pendingTopics)
                ->description("Đã duyệt: {$approvedTopics} | Từ chối: {$rejectedTopics}")
                ->icon('heroicon-o-clipboard-document-check')
                ->color($pendingTopics > 0 ? 'warning' : 'success'),
            Stat::make('File chờ duyệt', $pendingFiles)
                ->description("Đã duyệt: {$approvedFiles} | Từ chối: {$rejectedFiles}")
                ->icon('heroicon-o-document-arrow-up')
                ->color($pendingFiles > 0 ? 'warning' : 'success'),
            Stat::make('Giảng viên', Lecturer::count())
                ->description('Tài khoản giảng viên')
                ->icon('heroicon-o-user-group')
                ->color('info'),
            Stat::make('Nhận xét', Review::count())
                ->description('GVHD/GVPB đã nhập')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('gray'),
        ];
    }
}
