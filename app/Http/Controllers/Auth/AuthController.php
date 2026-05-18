<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->getRedirectRoute());
        }
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:3',
        ]);

        $login = $credentials['username'];
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Check if user exists and is active
        $user = User::where($loginField, $login)->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'Thông tin đăng nhập không chính xác.',
            ])->withInput($request->only('username'));
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'username' => 'Tài khoản đã bị khóa, vui lòng liên hệ Admin.',
            ])->withInput($request->only('username'));
        }

        // Attempt login
        if (Auth::attempt([
            $loginField => $login,
            'password' => $credentials['password'],
        ])) {
            $request->session()->regenerate();

            // Update last login
            $user->update(['last_login_at' => now()]);

            return redirect($this->getRedirectRoute());
        }

        return back()->withErrors([
            'username' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput($request->only('username'));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Get redirect route based on role
     */
    private function getRedirectRoute(): string
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => '/admin/dashboard',
            'lecturer', 'gvhd', 'gvpb' => '/lecturer',
            'student' => '/student/dashboard',
            default => '/',
        };
    }
}
