<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request checking for user roles.
     * Usage: middleware('role:super_admin') or middleware('role:super_admin,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Your account has been deactivated.',
            ]);
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Unauthorized: Your role does not grant access to this module.');
        }

        return $next($request);
    }
}
