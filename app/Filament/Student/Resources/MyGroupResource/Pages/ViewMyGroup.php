<?php

namespace App\Filament\Student\Resources\MyGroupResource\Pages;

use App\Filament\Student\Resources\MyGroupResource;
use App\Models\Student;
use App\Models\ThesisGroup;
use Filament\Resources\Pages\Page;

class ViewMyGroup extends Page
{
    protected static string $resource = MyGroupResource::class;

    protected string $view = 'filament.student.resources.my-group-resource.pages.view-my-group';

    protected static ?string $title = 'Thông tin nhóm của tôi';

    public ?ThesisGroup $group = null;

    public ?Student $student = null;

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

        $this->group = $groupMember->group()->with(['topic', 'topic.gvhd', 'topic.gvhd.user', 'topic.gvpb', 'topic.gvpb.user', 'members.student', 'academicYear'])->first();
    }
}
