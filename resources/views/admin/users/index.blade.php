@extends('layouts.admin')

@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')

@section('content')
<div class="users-container">

    <!-- Top Action Header -->
    <div class="action-header">
        <div class="action-header-left">
            <h3>Roles & Permissions</h3>
            <p>Create and manage user accounts, assign roles and module permissions, and control account activation.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-accent">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Add New User</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
            <div class="filter-search-box">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="filter-search-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Search users by name or email address...">
            </div>

            <select name="role" class="filter-select">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="content_manager" {{ request('role') == 'content_manager' ? 'selected' : '' }}>Content Manager</option>
            </select>

            <select name="status" class="filter-select">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="btn btn-primary" style="padding: 9px 18px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="padding: 9px 16px;">Reset</a>
            @endif
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="sno-cell">#</th>
                        <th style="min-width: 240px;">User Name & Email</th>
                        <th style="min-width: 140px;">Role</th>
                        <th style="min-width: 240px;">Module Permissions</th>
                        <th style="min-width: 140px;">Status</th>
                        <th style="min-width: 140px;">Last Login</th>
                        <th style="text-align: right; min-width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="sno-cell">
                                <span class="sno-badge">#{{ str_pad($loop->iteration + ($users->currentPage() - 1) * $users->perPage(), 2, '0', STR_PAD_LEFT) }}</span>
                            </td>

                            <td>
                                <div class="user-info-cell">
                                    <div class="avatar-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="user-name-title">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === Auth::id())
                                                <span class="self-pill">You</span>
                                            @endif
                                        </div>
                                        <div class="user-email-subtitle">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);">
                                    {{ $user->role === 'super_admin' ? 'Super Admin' : ($user->role === 'content_manager' ? 'Content Manager' : 'Admin') }}
                                </span>
                            </td>

                            <td>
                                @if($user->isSuperAdmin())
                                    <span style="font-weight: 700; color: #0284C7; font-size: 13px;">Full System Access</span>
                                @else
                                    @php
                                        $moduleLabels = [
                                            'products' => 'Products',
                                            'gallery' => 'Gallery',
                                            'enquiries' => 'Enquiries',
                                            'contact' => 'Contact',
                                            'users' => 'Roles & Permissions',
                                        ];
                                    @endphp
                                    @if(!empty($user->permissions))
                                        <span style="font-size: 13px; color: var(--text-body); font-weight: 500;">
                                            {{ collect($user->permissions)->map(fn($p) => $moduleLabels[$p] ?? ucfirst($p))->implode(', ') }}
                                        </span>
                                    @else
                                        <span style="font-size: 13px; color: var(--text-muted); font-style: italic;">No modules assigned</span>
                                    @endif
                                @endif
                            </td>

                            <td>
                                @if($user->id === Auth::id())
                                    <span class="badge-status badge-live">Active (You)</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.status', $user) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-status-toggle {{ $user->isActive() ? 'btn-status-active' : 'btn-status-inactive' }}" title="Click to toggle account activation">
                                            <span>{{ $user->isActive() ? '● Active' : '○ Inactive' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted); white-space: nowrap;">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}
                                </span>
                            </td>

                            <td style="text-align: right;">
                                <div class="action-btn-group">
                                    <!-- Edit (Icon Only) -->
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-action-icon edit" title="Edit User">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>

                                    @if($user->id !== Auth::id())
                                        <!-- Delete (Icon Only) -->
                                        <button type="button" 
                                                class="btn-action-icon delete" 
                                                data-delete-url="{{ route('admin.users.destroy', $user) }}" 
                                                data-delete-name="{{ $user->name }} ({{ $user->email }})" 
                                                data-delete-type="User Account" 
                                                onclick="triggerDeleteModal(this)" 
                                                title="Delete User Account">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 60px 20px;">
                                <div style="width: 56px; height: 56px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; margin: 0 auto 12px;">
                                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <p style="font-size: 15px; font-weight: 700; color: var(--text-heading); margin-bottom: 4px;">No users found</p>
                                <span style="font-size: 13px; color: var(--text-muted);">No user accounts matched your filter criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->total() > 0)
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-color); background: #F8FAFC;">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
