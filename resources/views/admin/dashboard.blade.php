@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Operational Dashboard')

@section('content')
<div class="dashboard-container">

    <!-- Welcome Hero Banner -->
    <div class="welcome-banner">
        <div class="welcome-content">
            <div class="welcome-pill">
                <span>✦ Carrier Authorized Dealer Hub &bull; Pondicherry</span>
            </div>
            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
            <p>Comfort Aircon Website & Admin Panel Management</p>
        </div>
        <div class="welcome-actions">
            <div class="status-indicator">
                <span class="pulse-dot"></span>
                <span>System Operational</span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        @if(Auth::user()->hasModuleAccess('enquiries'))
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon icon-blue">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                @if($stats['new_enquiries_today'] > 0)
                    <span class="stat-badge badge-green">+{{ $stats['new_enquiries_today'] }} Today</span>
                @else
                    <span class="stat-badge badge-blue">Enquiries</span>
                @endif
            </div>
            <div class="stat-value">{{ $stats['total_enquiries'] }}</div>
            <div class="stat-label">Total Enquiries</div>
        </div>
        @endif

        @if(Auth::user()->hasModuleAccess('products'))
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon icon-cyan">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="stat-badge badge-green">{{ $stats['published_products'] }} Published</span>
            </div>
            <div class="stat-value">{{ $stats['products_count'] }}</div>
            <div class="stat-label">Products (Catalogue)</div>
        </div>
        @endif

        @if(Auth::user()->hasModuleAccess('gallery'))
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon icon-green">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="stat-badge badge-green">{{ $stats['published_gallery'] }} Published</span>
            </div>
            <div class="stat-value">{{ $stats['gallery_count'] }}</div>
            <div class="stat-label">Gallery Photos</div>
        </div>
        @endif

        @if(Auth::user()->isSuperAdmin())
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon icon-amber">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="stat-badge badge-blue">Roles</span>
            </div>
            <div class="stat-value">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Roles & Permissions (Users)</div>
        </div>
        @endif
    </div>

    <!-- Main Sections Grid -->
    <div class="dashboard-grid">

        <!-- Enquiries Column -->
        @if(Auth::user()->hasModuleAccess('enquiries'))
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>Recent Customer Enquiries</h3>
                    <span style="font-size: 13.5px; color: var(--text-muted); display: block; margin-top: 2px;">Incoming website customer enquiries</span>
                </div>
                <a href="{{ route('admin.enquiries.index') }}" class="card-header-link">
                    <span>View All Enquiries</span>
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="table-responsive">
                @if($stats['recent_enquiries']->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="sno-cell">#</th>
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Mobile Number</th>
                                <th>Service Required</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_enquiries'] as $lead)
                                <tr>
                                    <td class="sno-cell">
                                        <span class="sno-badge">#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td style="color: var(--text-muted); font-size: 13.5px; white-space: nowrap;">{{ $lead->created_at->format('d M, h:i A') }}</td>
                                    <td>
                                        <div class="lead-customer-cell">
                                            <div class="lead-avatar">
                                                {{ strtoupper(substr($lead->name, 0, 1)) }}
                                            </div>
                                            <strong>{{ $lead->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $lead->mobile_number }}" style="color: var(--accent); text-decoration: none; font-weight: 700; font-family: 'JetBrains Mono', monospace; font-size: 13.5px;">
                                            {{ $lead->mobile_number }}
                                        </a>
                                    </td>
                                    <td><span style="color: var(--text-body); font-weight: 500;">{{ $lead->service_required }}</span></td>
                                    <td>{{ $lead->location }}</td>
                                    <td>
                                        <span class="badge-status badge-{{ strtolower(str_replace(' ', '_', $lead->status)) }}">
                                            {{ $lead->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p>No customer enquiries received yet.</p>
                        <small>Enquiries submitted from the public website will appear here in real time.</small>
                    </div>
                @endif
            </div>
        </div>
        @else
        <!-- Information for Content Managers -->
        <div class="card">
            <div class="card-header">
                <h3>Content Management</h3>
            </div>
            <div class="card-body">
                <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.6;">
                    You are logged in as a <strong>Content Manager</strong>. You have permissions to update <strong>Products</strong>, upload portfolio photos in <strong>Gallery Management</strong>, and keep business contact details fresh in <strong>Contact Management</strong>.
                </p>
                <div style="margin-top: 18px; display: flex; gap: 10px;">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Manage Products</a>
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Upload Photos</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Contact Information Sync Card -->
        <div class="card">
            <div class="card-header">
                <h3>Public Website Contact Details</h3>
                @if ($contact && Auth::user()->hasModuleAccess('contact'))
                    <a href="{{ route('admin.contact.index') }}" class="card-header-link">Edit Details &rarr;</a>
                @endif
            </div>
            <div class="card-body">
                @if ($contact)
                    <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.45;">
                        Values maintained here automatically update the public website Header, Footer, and Contact Us page.
                    </p>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <div class="label">Dealership Phone</div>
                            <div class="val">
                                <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon" style="color: #059669; background: #ECFDF5; border-color: #A7F3D0;">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <div class="label">WhatsApp Direct</div>
                            <div class="val">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->whatsapp) }}" target="_blank">{{ $contact->whatsapp }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <div class="label">Lead Notification Email</div>
                            <div class="val">
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="contact-info">
                            <div class="label">Showroom Address</div>
                            <div class="val">{{ $contact->address }}</div>
                        </div>
                    </div>

                    @if (Auth::user()->hasModuleAccess('contact'))
                        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border-light); text-align: right;">
                            <a href="{{ route('admin.contact.index') }}" class="card-header-link">
                                <span>Edit Contact Details &rarr;</span>
                            </a>
                        </div>
                    @endif
                @else
                    <div style="text-align: center; padding: 26px 16px;">
                        <div style="width: 52px; height: 52px; margin: 0 auto 14px; border-radius: 50%; background: #F0F9FF; border: 1px solid #BAE6FD; display: flex; align-items: center; justify-content: center; color: #0284C7;">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h4 style="font-size: 15px; font-weight: 700; color: var(--text-heading); margin-bottom: 6px;">No Contact Details Configured</h4>
                        <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 18px; line-height: 1.45;">
                            Add your phone number, WhatsApp, email, and showroom location to synchronize with the public website Header, Footer, and Contact Us page.
                        </p>
                        @if (Auth::user()->hasModuleAccess('contact'))
                            <a href="{{ route('admin.contact.create') }}" class="btn btn-accent btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Create Contact Details</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
