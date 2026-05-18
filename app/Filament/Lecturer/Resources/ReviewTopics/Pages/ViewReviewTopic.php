<?php

namespace App\Filament\Lecturer\Resources\ReviewTopics\Pages;

use App\Filament\Lecturer\Resources\ReviewTopics\ReviewTopicResource;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

class ViewReviewTopic extends ViewRecord
{
    protected static string $resource = ReviewTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('review')
                ->label('Cập nhật nhận xét phản biện')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->fillForm(function (): array {
                    $review = ReviewTopicResource::review($this->record);

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
                    $group = ReviewTopicResource::group($this->record);
                    $lecturerId = ReviewTopicResource::currentLecturerId();

                    if (!$group || !$lecturerId) {
                        return;
                    }

                    Review::updateOrCreate([
                        'group_id' => $group->id,
                        'reviewer_id' => $lecturerId,
                        'reviewer_type' => 'gvpb',
                    ], [
                        'content' => $data['content'],
                        'review_date' => now()->toDateString(),
                    ]);
                })
                ->successNotificationTitle('Đã cập nhật nhận xét phản biện'),
        ];
    }
}
