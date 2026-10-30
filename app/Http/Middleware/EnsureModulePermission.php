<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureModulePermission
{
    /**
     * Handle an incoming request checking for module-level permission.
     * Usage: middleware('permission:products') or middleware('permission:users')
     */
    public function handle(Request $request, Closure $next, string $module): Response
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

        if (!$user->hasModuleAccess($module)) {
            abort(403, "Access Denied: You do not have permission to access the '{$module}' module.");
        }

        return $next($request);
    }
}
