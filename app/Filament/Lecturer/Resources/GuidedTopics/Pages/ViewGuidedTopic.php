<?php

namespace App\Filament\Lecturer\Resources\GuidedTopics\Pages;

use App\Filament\Lecturer\Resources\GuidedTopics\GuidedTopicResource;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

class ViewGuidedTopic extends ViewRecord
{
    protected static string $resource = GuidedTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('review')
                ->label('Cập nhật nhận xét hướng dẫn')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->fillForm(function (): array {
                    $review = GuidedTopicResource::review($this->record);

                    return [
                        'content' => $review?->content,
                    ];
                })
                ->form([
                    Textarea::make('content')
                        ->label('Nhận xét')
                        ->rows(6)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $group = GuidedTopicResource::group($this->record);
                    $lecturerId = GuidedTopicResource::currentLecturerId();

                    if (!$group || !$lecturerId) {
                        return;
                    }

                    Review::updateOrCreate([
                        'group_id' => $group->id,
                        'reviewer_id' => $lecturerId,
                        'reviewer_type' => 'gvhd',
                    ], [
                        'content' => $data['content'],
                        'review_date' => now()->toDateString(),
                    ]);
                })
                ->successNotificationTitle('Đã cập nhật nhận xét hướng dẫn'),
        ];
    }
}
