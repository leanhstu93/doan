<?php

namespace App\Filament\Student\Resources\MyTopicResource\Pages;

use App\Filament\Student\Resources\MyTopicResource;
use App\Models\Student;
use Filament\Resources\Pages\ListRecords;

class ListMyTopics extends ListRecords
{
    protected static string $resource = MyTopicResource::class;

    public function mount(): void
    {
        // Redirect to create or edit based on whether student has a topic
        $student = Student::where('user_id', auth()->id())->first();

        if ($student) {
            $groupMember = $student->thesisGroupMembers->first();
            if ($groupMember && $groupMember->group && $groupMember->group->topic_id) {
                $this->redirect(MyTopicResource::getUrl('edit'));
                return;
            }
        }

        $this->redirect(MyTopicResource::getUrl('create'));
    }
}
