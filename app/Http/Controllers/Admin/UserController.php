<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * All available system modules according to Section 9.1.
     */
    public const AVAILABLE_MODULES = [
        'products' => 'Products',
        'gallery' => 'Gallery',
        'enquiries' => 'Enquiries',
        'contact' => 'Contact',
        'users' => 'Roles & Permissions',
    ];

    /**
     * Display a listing of admin users.
     */
    public function index(Request $request): View
    {
        $query = User::query()->latest();

        // Search by name or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $modules = self::AVAILABLE_MODULES;
        return view('admin.users.create', compact('modules'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)],
            'role' => ['required', Rule::in(['super_admin', 'admin', 'content_manager'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in(array_keys(self::AVAILABLE_MODULES))],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // Auto-assign permissions based on role if none explicitly checked
        $permissions = $validated['permissions'] ?? [];
        if ($validated['role'] === 'super_admin') {
            $permissions = array_keys(self::AVAILABLE_MODULES);
        } elseif (empty($permissions)) {
            if ($validated['role'] === 'admin') {
                $permissions = ['products', 'gallery', 'enquiries', 'contact'];
            } elseif ($validated['role'] === 'content_manager') {
                $permissions = ['products', 'gallery', 'contact'];
            }
        }

        // Disallow non-super_admin from having 'users' permission
        if ($validated['role'] !== 'super_admin') {
            $permissions = array_values(array_diff($permissions, ['users']));
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'permissions' => $permissions,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Staff member '{$validated['name']}' has been created successfully.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $modules = self::AVAILABLE_MODULES;
        return view('admin.users.edit', compact('user', 'modules'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(8)],
            'role' => ['required', Rule::in(['super_admin', 'admin', 'content_manager'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in(array_keys(self::AVAILABLE_MODULES))],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // Safeguard: Do not allow current user to deactivate or demote themselves
        if ($user->id === Auth::id()) {
            if ($validated['status'] === 'inactive') {
                return back()->withErrors(['status' => 'You cannot deactivate your own account.']);
            }
            if ($validated['role'] !== 'super_admin' && $user->isSuperAdmin()) {
                return back()->withErrors(['role' => 'You cannot change your own Super Admin role.']);
            }
        }

        $permissions = $validated['permissions'] ?? [];
        if ($validated['role'] === 'super_admin') {
            $permissions = array_keys(self::AVAILABLE_MODULES);
        }

        // Disallow non-super_admin from having 'users' permission
        if ($validated['role'] !== 'super_admin') {
            $permissions = array_values(array_diff($permissions, ['users']));
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'permissions' => $permissions,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', "Account for '{$user->name}' was updated successfully.");
    }

    /**
     * Toggle active/inactive status of a user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own active session.']);
        }

        $newStatus = $user->isActive() ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "User '{$user->name}' has been {$statusLabel}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Staff member '{$userName}' has been deleted.");
    }
}
