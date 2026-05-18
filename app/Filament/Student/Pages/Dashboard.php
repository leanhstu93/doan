<?php

namespace App\Filament\Student\Pages;

use App\Models\FileSubmission;
use App\Models\Student;
use App\Models\SubmissionPhase;
use App\Models\ThesisGroup;
use App\Models\ThesisTopic;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected string $view = 'filament.student.pages.dashboard';

    protected static ?string $title = 'Trang chủ';

    protected static ?string $navigationLabel = 'Trang chủ';

    protected static ?string $slug = '/';

    public ?Student $student = null;

    public ?ThesisGroup $group = null;

    public ?ThesisTopic $topic = null;

    public array $submissionPhases = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->student = Student::where('user_id', $user->id)->first();

        if ($this->student) {
            $groupMember = $this->student->thesisGroupMembers->first();
            if ($groupMember) {
                $this->group = $groupMember->group;
                if ($this->group && $this->group->topic_id) {
                    $this->topic = $this->group->topic;
                }
            }
        }

        // Load submission phases with status
        $this->loadSubmissionPhases();
    }

    /**
     * Load all submission phases with current group's submission status
     */
    protected function loadSubmissionPhases(): void
    {
        $phases = SubmissionPhase::ordered()->get();

        $this->submissionPhases = $phases->map(function ($phase) {
            $submission = null;

            if ($this->group && $this->group->topic_id) {
                $submission = FileSubmission::where('topic_id', $this->group->topic_id)
                    ->where('phase_id', $phase->id)
                    ->first();
            }

            return [
                'id' => $phase->id,
                'name' => $phase->name,
                'code' => $phase->code,
                'description' => $phase->description,
                'is_active' => $phase->is_active,
                'is_open' => $phase->isOpen(),
                'is_not_yet_open' => $phase->isNotYetOpen(),
                'is_closed' => $phase->isClosed(),
                'open_date' => $phase->open_date?->format('d/m/Y'),
                'close_date' => $phase->close_date?->format('d/m/Y'),
                'submission' => $submission,
                'status' => $submission?->status,
                'status_label' => $submission?->status_label ?? 'Chưa nộp',
                'status_color' => $submission?->status_color ?? 'gray',
            ];
        })->toArray();
    }

    public function getHeading(): string
    {
        return 'Xin chào, ' . auth()->user()->full_name;
    }
}
