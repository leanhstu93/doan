<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'lecturers' => Lecturer::count(),
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
