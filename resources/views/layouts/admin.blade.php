<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Comfort Aircon (Carrier Authorized Dealer)</title>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ file_exists(public_path('css/admin.css')) ? filemtime(public_path('css/admin.css')) : time() }}">
    @yield('styles')
</head>

<body>

    <!-- Advanced Cockpit Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <!-- Brand Header Card -->
        <div class="sidebar-brand-wrapper">
            <a href="{{ route('admin.dashboard') }}" class="brand-logo-card" title="Comfort Aircon Admin">
                <img src="{{ asset('images/comfortair-logo.png') }}" alt="Comfort Aircon" class="brand-logo-img">
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="sidebar-menu">
            <div class="menu-category-label">Main Workspace</div>

            <a href="{{ route('admin.dashboard') }}"
                class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.home') ? 'active' : '' }}">
                <div class="nav-item-content">
                    <div class="nav-icon-box">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span>Dashboard</span>
                </div>
            </a>

            @if (Auth::user()->hasModuleAccess('products'))
                <a href="{{ route('admin.products.index') }}"
                    class="nav-item {{ request()->is('admin/products*') ? 'active' : '' }}">
                    <div class="nav-item-content">
                        <div class="nav-icon-box">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span>Product Management</span>
                    </div>
                </a>
            @endif

            <div class="menu-category-label">Website Content & Leads</div>

            @if (Auth::user()->hasModuleAccess('gallery'))
                <a href="{{ route('admin.gallery.index') }}"
                    class="nav-item {{ request()->is('admin/gallery*') ? 'active' : '' }}">
                    <div class="nav-item-content">
                        <div class="nav-icon-box">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span>Gallery Management</span>
                    </div>
                </a>
            @endif

            @if (Auth::user()->hasModuleAccess('enquiries'))
                <a href="{{ route('admin.enquiries.index') }}"
                    class="nav-item {{ request()->is('admin/enquiries*') ? 'active' : '' }}">
                    <div class="nav-item-content">
                        <div class="nav-icon-box">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span>Enquiry Management</span>
                    </div>
                    @if (isset($newEnquiryCount) && $newEnquiryCount > 0)
                        <span class="badge-counter">{{ $newEnquiryCount }}</span>
                    @endif
                </a>
            @endif

            @if (Auth::user()->hasModuleAccess('contact'))
                <a href="{{ route('admin.contact.index') }}"
                    class="nav-item {{ request()->is('admin/contact*') ? 'active' : '' }}">
                    <div class="nav-item-content">
                        <div class="nav-icon-box">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span>Contact Management</span>
                    </div>
                </a>
            @endif

            @if (Auth::user()->isSuperAdmin())
                <div class="menu-category-label">System Administration</div>
                <a href="{{ route('admin.users.index') }}"
                    class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <div class="nav-item-content">
                        <div class="nav-icon-box">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span>Roles & Permissions</span>
                    </div>
                </a>
            @endif
        </nav>

        <!-- User Profile Footer Dock -->
        <div class="sidebar-footer">
            <div class="user-quick-profile">
                <div class="user-avatar-wrap">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="avatar-online-dot"></span>
                </div>
                <div class="user-meta">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <span class="role-pill role-{{ Auth::user()->role }}">
                        {{ ucwords(str_replace('_', ' ', Auth::user()->role)) }}
                    </span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Sticky Header -->
        <header class="admin-header">
            <div class="header-left">
                <button class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="header-title-box">
                    <div class="header-breadcrumbs">
                        <a href="{{ route('admin.dashboard') }}">Comfort Aircon</a>
                        <span>/</span>
                        <span>@yield('page_title', 'Administrative Overview')</span>
                    </div>
                    <div class="header-title">
                        <h2>@yield('page_title', 'Administrative Overview')</h2>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <div class="carrier-partner-badge">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Carrier Authorized Dealer</span>
                </div>

                <button type="button" class="btn-header" onclick="openPasswordModal()">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Password</span>
                </button>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn-header btn-logout">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages & Page Content -->
        <main class="admin-content">
            <noscript>
                @if (session('success'))
                    <div class="alert alert-success">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('info') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </noscript>

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            style="flex-shrink: 0; margin-top: 2px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <strong>Please resolve the following:</strong>
                            <ul style="margin-left: 20px; margin-top: 6px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()"
                        style="background:none; border:none; cursor:pointer; color: inherit; font-size: 20px;">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Global Toast Container -->
    <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>

    <!-- Password Modal -->
    <div class="modal-overlay" id="passwordModal">
        <div class="modal-box">
            <div class="modal-header-banner"></div>
            <div class="modal-header">
                <div class="modal-header-title-wrap">
                    <div class="modal-header-icon">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3>Change Password</h3>
                        <p>Enhance security with a unique, strong password</p>
                    </div>
                </div>
                <button type="button" class="modal-close" onclick="closePasswordModal()"
                    aria-label="Close modal">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.password.change') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required
                            placeholder="Enter your current password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password (minimum 8 characters)</label>
                        <input type="password" name="password" class="form-control" required
                            placeholder="Enter new strong password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required
                            placeholder="Re-enter new password">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary"
                            onclick="closePasswordModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Update Password</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Global Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteConfirmModal">
        <div class="modal-box" style="max-width: 460px;">
            <div class="modal-header-banner danger"></div>
            <div class="modal-header">
                <div class="modal-header-title-wrap">
                    <div class="modal-header-icon danger">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 style="color: #991B1B;">Confirm Deletion</h3>
                        <p>This action cannot be undone</p>
                    </div>
                </div>
                <button type="button" class="modal-close" onclick="closeDeleteModal()"
                    aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size: 14px; color: var(--text-main); margin-bottom: 8px;">
                    Are you sure you want to permanently delete this <strong id="deleteModalItemType">record</strong>?
                </p>
                <div id="deleteModalItemName"
                    style="background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%); border: 1px solid #FECACA; color: #991B1B; padding: 12px 16px; border-radius: var(--radius-md); font-weight: 700; font-size: 13.5px; margin: 14px 0; word-break: break-word;">
                </div>
                <div
                    style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #DC2626; background: rgba(220, 38, 38, 0.06); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px dashed rgba(220, 38, 38, 0.2);">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"
                        style="flex-shrink: 0;">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Permanent action: All linked data will be deleted immediately.</span>
                </div>
                <div class="form-actions" style="margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteSubmitBtn"
                        onclick="submitGlobalDelete()">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Yes, Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Global Delete Form -->
    <form id="globalDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').classList.add('active');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.remove('active');
        }

        function triggerDeleteModal(btn) {
            const url = btn.getAttribute('data-delete-url');
            const name = btn.getAttribute('data-delete-name');
            const type = btn.getAttribute('data-delete-type') || 'item';
            openDeleteModal(url, name, type);
        }

        function openDeleteModal(actionUrl, itemName, itemType = 'item') {
            const form = document.getElementById('globalDeleteForm');
            form.action = actionUrl;
            document.getElementById('deleteModalItemName').textContent = itemName;
            document.getElementById('deleteModalItemType').textContent = itemType;
            const submitBtn = document.getElementById('confirmDeleteSubmitBtn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Yes, Delete</span>
            `;
            document.getElementById('deleteConfirmModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').classList.remove('active');
        }

        function submitGlobalDelete() {
            const btn = document.getElementById('confirmDeleteSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = `<span>Deleting...</span>`;
            document.getElementById('globalDeleteForm').submit();
        }

        // Close modals when clicking outside
        document.getElementById('passwordModal').addEventListener('click', function(e) {
            if (e.target === this) closePasswordModal();
        });

        document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closePasswordModal();
            }
        });

        // Toast Notification Engine
        (function() {
            const icons = {
                success: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M5 13l4 4L19 7"/></svg>`,
                error: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M6 18L18 6M6 6l12 12"/></svg>`,
                warning: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
                info: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            };

            const defaultTitles = {
                success: 'Success',
                error: 'Action Failed',
                warning: 'Attention',
                info: 'Notice'
            };

            window.showToast = function(options, maybeType = 'success', maybeTitle = null, maybeDuration = 4500) {
                let message = '';
                let type = 'success';
                let title = null;
                let duration = 4500;

                if (typeof options === 'object' && options !== null) {
                    message = options.message || '';
                    type = options.type || 'success';
                    title = options.title || defaultTitles[type] || 'Notice';
                    duration = options.duration !== undefined ? options.duration : 4500;
                } else {
                    message = String(options || '');
                    type = maybeType || 'success';
                    title = maybeTitle || defaultTitles[type] || 'Notice';
                    duration = maybeDuration !== undefined ? maybeDuration : 4500;
                }

                if (!message) return null;

                let container = document.getElementById('toastContainer');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toastContainer';
                    container.className = 'toast-container';
                    container.setAttribute('aria-live', 'polite');
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.className = `toast-card toast-${type}`;
                toast.setAttribute('role', 'status');

                toast.innerHTML = `
                    <div class="toast-icon-wrap">
                        ${icons[type] || icons.info}
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                    <button type="button" class="toast-close-btn" aria-label="Close notification">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="toast-progress">
                        <div class="toast-progress-fill"></div>
                    </div>
                `;

                container.appendChild(toast);

                const progressBar = toast.querySelector('.toast-progress-fill');
                const closeBtn = toast.querySelector('.toast-close-btn');

                let startTime = Date.now();
                let remainingTime = duration;
                let timerId = null;
                let isPaused = false;

                function dismiss() {
                    if (toast.classList.contains('toast-hiding')) return;
                    toast.classList.add('toast-hiding');
                    setTimeout(() => {
                        toast.remove();
                    }, 320);
                }

                closeBtn.addEventListener('click', dismiss);

                if (duration > 0) {
                    progressBar.style.transition = `transform ${duration}ms linear`;
                    requestAnimationFrame(() => {
                        progressBar.style.transform = 'scaleX(0)';
                    });

                    function startTimer() {
                        startTime = Date.now();
                        timerId = setTimeout(dismiss, remainingTime);
                    }

                    function pauseTimer() {
                        clearTimeout(timerId);
                        const elapsed = Date.now() - startTime;
                        remainingTime = Math.max(0, remainingTime - elapsed);
                        const progressRatio = duration > 0 ? (remainingTime / duration) : 0;
                        progressBar.style.transition = 'none';
                        progressBar.style.transform = `scaleX(${progressRatio})`;
                    }

                    function resumeTimer() {
                        if (remainingTime <= 0) {
                            dismiss();
                            return;
                        }
                        startTime = Date.now();
                        progressBar.style.transition = `transform ${remainingTime}ms linear`;
                        progressBar.style.transform = 'scaleX(0)';
                        timerId = setTimeout(dismiss, remainingTime);
                    }

                    toast.addEventListener('mouseenter', () => {
                        isPaused = true;
                        pauseTimer();
                    });

                    toast.addEventListener('mouseleave', () => {
                        if (isPaused) {
                            isPaused = false;
                            resumeTimer();
                        }
                    });

                    startTimer();
                } else {
                    progressBar.style.display = 'none';
                }

                return toast;
            };

            // Trigger flash messages on page load
            @if (session('success'))
                window.addEventListener('DOMContentLoaded', () => {
                    showToast(@json(session('success')), 'success');
                });
            @endif
            @if (session('info'))
                window.addEventListener('DOMContentLoaded', () => {
                    showToast(@json(session('info')), 'info');
                });
            @endif
            @if (session('error'))
                window.addEventListener('DOMContentLoaded', () => {
                    showToast(@json(session('error')), 'error');
                });
            @endif
            @if (isset($errors) && $errors->any())
                window.addEventListener('DOMContentLoaded', () => {
                    showToast('Please check the form below for validation errors.', 'error', 'Validation Notice');
                });
            @endif
        })();
    </script>
    @yield('scripts')
</body>

</html>
