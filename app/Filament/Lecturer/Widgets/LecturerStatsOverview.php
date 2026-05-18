<?php

namespace App\Filament\Lecturer\Widgets;

use App\Models\FileSubmission;
use App\Models\Lecturer;
use App\Models\Review;
use App\Models\ThesisGroup;
use App\Models\ThesisTopic;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LecturerStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $lecturerId = Lecturer::where('user_id', auth()->id())->value('id');

        if (!$lecturerId) {
            return [
                Stat::make('Đề tài hướng dẫn', 0),
                Stat::make('Đề tài phản biện', 0),
                Stat::make('File đã nộp', 0),
                Stat::make('Nhận xét đã nhập', 0),
            ];
        }

        $guidedTopicIds = ThesisTopic::where('gvhd_id', $lecturerId)->pluck('id');
        $reviewTopicIds = ThesisTopic::where('gvpb_id', $lecturerId)->pluck('id');
        $topicIds = $guidedTopicIds->merge($reviewTopicIds)->unique();

        $directGroupIds = ThesisTopic::query()
            ->whereIn('id', $topicIds)
            ->pluck('group_id')
            ->filter();
        $legacyGroupIds = ThesisGroup::whereIn('topic_id', $topicIds)->pluck('id');
        $groupIds = $directGroupIds->merge($legacyGroupIds)->unique();

        return [
            Stat::make('Đề tài hướng dẫn', $guidedTopicIds->count())
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),
            Stat::make('Đề tài phản biện', $reviewTopicIds->count())
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning'),
            Stat::make('File đã nộp', FileSubmission::whereIn('topic_id', $topicIds)->count())
                ->icon('heroicon-o-document-arrow-up')
                ->color('success'),
            Stat::make('Nhận xét đã nhập', Review::where('reviewer_id', $lecturerId)->whereIn('group_id', $groupIds)->count())
                ->icon('heroicon-o-pencil-square')
                ->color('info'),
        ];
    }
}
