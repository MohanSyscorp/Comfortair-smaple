@extends('layouts.admin')

@section('title', 'Add New User')
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
            <div class="form-header-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div class="form-header-text">
                <h3>Add New User</h3>
                <p>Enter account credentials, assign role, and select module permissions.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <!-- Section 1 -->
            <div class="section-divider">1. User Credentials & Role</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Ramesh Kumar">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email / Username <span style="color: var(--danger);">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="admin@comfortaircon.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Login Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role <span style="color: var(--danger);">*</span></label>
                    <select id="role" name="role" class="form-control" required onchange="handleRoleChange(this.value)">
                        <option value="">Select a Role</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin (Full System Access)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Products, Gallery, Enquiries & Contact)</option>
                        <option value="content_manager" {{ old('role', 'content_manager') == 'content_manager' ? 'selected' : '' }}>Content Manager (Products, Gallery & Contact)</option>
                    </select>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="section-divider">2. Module Permissions</div>

            <div class="form-group">
                <label class="form-label">Module Permissions <span style="color: var(--danger);">*</span></label>
                <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 6px;">Select which modules this user has authority to access.</p>
                <div class="checkbox-grid">
                    @foreach($modules as $key => $label)
                        <label class="checkbox-item" id="perm_wrapper_{{ $key }}">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" id="perm_{{ $key }}"
                                {{ (is_array(old('permissions')) && in_array($key, old('permissions'))) || ($key !== 'users' && !old('permissions')) ? 'checked' : '' }}>
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
                        <input type="radio" name="status" value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                        <div>
                            <span class="radio-card-title" style="color: #059669;">Active (Can log in)</span>
                            <span class="radio-card-desc">User can immediately log in and manage authorized modules.</span>
                        </div>
                    </label>

                    <label class="radio-card">
                        <input type="radio" name="status" value="inactive" {{ old('status') == 'inactive' ? 'checked' : '' }}>
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
                    <span>Create User</span>
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

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        if (roleSelect && roleSelect.value) {
            handleRoleChange(roleSelect.value);
        }
    });
</script>
@endsection
