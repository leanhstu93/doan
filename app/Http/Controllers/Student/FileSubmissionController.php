<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FileSubmission;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileSubmissionController extends Controller
{
    /**
     * View file inline (for PDF)
     */
    public function view($id)
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            abort(403, 'Unauthorized');
        }

        $submission = FileSubmission::findOrFail($id);

        // Check if student belongs to the group that owns this submission
        $groupMember = $student->thesisGroupMembers->first();
        $group = $groupMember?->group;

        if (!$group || $group->topic_id !== $submission->topic_id) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::disk('local')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('local')->path($submission->file_path);
        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $submission->original_name . '"',
        ]);
    }

    /**
     * Download file
     */
    public function download($id)
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            abort(403, 'Unauthorized');
        }

        $submission = FileSubmission::findOrFail($id);

        // Check if student belongs to the group that owns this submission
        $groupMember = $student->thesisGroupMembers->first();
        $group = $groupMember?->group;

        if (!$group || $group->topic_id !== $submission->topic_id) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::disk('local')->exists($submission->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($submission->file_path, $submission->original_name);
    }
}
