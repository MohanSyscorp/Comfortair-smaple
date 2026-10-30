@extends('layouts.admin')

@section('title', 'Edit User — ' . $user->name)
@section('page_title', 'Roles & Permissions')

@section('content')
<div class="form-container">

    <a href="{{ route('admin.users.index') }}" class="back-nav">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Back to Roles & Permissions</span>
    </a>

    <div class="form-card">
        <div class="form-header">
            <div class="form-header-left">
                <div class="form-header-icon">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="form-header-text">
                    <h3>Edit User: {{ $user->name }}</h3>
                    <p>{{ $user->email }} &bull; Role: {{ $user->role === 'super_admin' ? 'Super Admin' : ($user->role === 'content_manager' ? 'Content Manager' : 'Admin') }}</p>
                </div>
            </div>
            <span class="badge-status {{ $user->isActive() ? 'badge-live' : 'badge-draft' }}">
                {{ $user->isActive() ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Section 1 -->
            <div class="section-divider">1. User Credentials & Role</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email / Username <span style="color: var(--danger);">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Reset Password (Optional)</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    <small style="color: var(--text-muted); font-size: 11.5px; margin-top: 4px; display: block;">Only enter if you wish to reset this user's password (min 8 characters).</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role <span style="color: var(--danger);">*</span></label>
                    <select id="role" name="role" class="form-control" required onchange="handleRoleChange(this.value)">
                        <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin (Full System Access)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Products, Gallery, Enquiries & Contact)</option>
                        <option value="content_manager" {{ old('role', $user->role) === 'content_manager' ? 'selected' : '' }}>Content Manager (Products, Gallery & Contact)</option>
                    </select>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="section-divider">2. Module Permissions</div>

            <div class="form-group">
                <label class="form-label">Module Permissions <span style="color: var(--danger);">*</span></label>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 6px;">Select which modules this user has authority to access.</p>
                <div class="checkbox-grid">
                    @php
                        $userPerms = is_array($user->permissions) ? $user->permissions : [];
                        $activePerms = old('permissions', $userPerms);
                    @endphp
                    @foreach($modules as $key => $label)
                        <label class="checkbox-item" id="perm_wrapper_{{ $key }}">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" id="perm_{{ $key }}"
                                {{ in_array($key, $activePerms) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Section 3 -->
            <div class="section-divider">3. Account Status</div>

            <div class="form-group">
                <label class="form-label">Account Status <span style="color: var(--danger);">*</span></label>
                <div class="radio-card-group">
                    <label class="radio-card">
                        <input type="radio" name="status" value="active" {{ old('status', $user->status) === 'active' ? 'checked' : '' }}>
                        <div>
                            <span class="radio-card-title" style="color: #059669;">Active (Can log in)</span>
                            <span class="radio-card-desc">User can log in and manage authorized modules.</span>
                        </div>
                    </label>

                    <label class="radio-card">
                        <input type="radio" name="status" value="inactive" {{ old('status', $user->status) === 'inactive' ? 'checked' : '' }}>
                        <div>
                            <span class="radio-card-title" style="color: #DC2626;">Inactive (Cannot log in)</span>
                            <span class="radio-card-desc">Account is locked and cannot log in to the admin panel.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-accent">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleRoleChange(role) {
        const usersPerm = document.getElementById('perm_users');
        const usersWrapper = document.getElementById('perm_wrapper_users');
        const enquiriesPerm = document.getElementById('perm_enquiries');
        const productsPerm = document.getElementById('perm_products');
        const galleryPerm = document.getElementById('perm_gallery');
        const contactPerm = document.getElementById('perm_contact');

        if (role === 'super_admin') {
            usersPerm.checked = true;
            usersPerm.disabled = false;
            usersWrapper.style.opacity = '1';
            enquiriesPerm.checked = true;
            productsPerm.checked = true;
            galleryPerm.checked = true;
            contactPerm.checked = true;
        } else if (role === 'admin') {
            usersPerm.checked = false;
            usersPerm.disabled = true;
            usersWrapper.style.opacity = '0.5';
            enquiriesPerm.checked = true;
            productsPerm.checked = true;
            galleryPerm.checked = true;
            contactPerm.checked = true;
        } else if (role === 'content_manager') {
            usersPerm.checked = false;
            usersPerm.disabled = true;
            usersWrapper.style.opacity = '0.5';
            enquiriesPerm.checked = false;
            productsPerm.checked = true;
            galleryPerm.checked = true;
            contactPerm.checked = true;
        }
    }
</script>
@endsection
