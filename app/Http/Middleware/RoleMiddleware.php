<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            // Redirect to appropriate dashboard based on role
            return redirect($this->getRedirectRoute($user->role));
        }

        return $next($request);
    }

    /**
     * Get redirect route based on role
     */
    private function getRedirectRoute(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'lecturer', 'gvhd', 'gvpb' => '/lecturer',
            'student' => '/student/dashboard',
            default => '/',
        };
    }
}
