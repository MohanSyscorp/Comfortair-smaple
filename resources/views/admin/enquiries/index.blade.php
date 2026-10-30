@extends('layouts.admin')

@section('title', 'Enquiry Management')
@section('page_title', 'Enquiry Management')

@section('content')
<div class="enquiries-container">

    <!-- Action Header -->
    <div class="action-header">
        <div class="action-header-left">
            <h3>Enquiry Management</h3>
            <p>View, search, and update follow-up statuses for customer enquiries submitted from the website.</p>
        </div>
        <div class="action-header-right" style="display: flex; align-items: center; gap: 10px;">
            <div style="background: #FFFFFF; border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 8px 16px; display: inline-flex; align-items: center; gap: 8px; box-shadow: var(--shadow-xs);">
                <span style="font-size: 18px; font-weight: 800; color: var(--accent); font-family: 'JetBrains Mono', monospace;">{{ $statusCounts['All'] ?? 0 }}</span>
                <span style="font-size: 13.5px; font-weight: 700; color: var(--text-muted);">Total Enquiries</span>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="status-tabs">
        <a href="{{ route('admin.enquiries.index', array_merge(request()->except('status', 'page'), ['status' => 'All'])) }}" 
           class="tab-badge {{ request('status', 'All') === 'All' ? 'active' : '' }}">
            <span>All Enquiries</span>
            <span class="count-pill">{{ $statusCounts['All'] ?? 0 }}</span>
        </a>

        @foreach($statuses as $st)
            <a href="{{ route('admin.enquiries.index', array_merge(request()->except('status', 'page'), ['status' => $st])) }}" 
               class="tab-badge {{ request('status') === $st ? 'active' : '' }}">
                <span>{{ $st }}</span>
                <span class="count-pill">{{ $statusCounts[$st] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <!-- Filter Toolbar -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.enquiries.index') }}" class="filter-form">
            @if(request('status') && request('status') !== 'All')
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="filter-search-box">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="filter-search-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" class="filter-input" placeholder="Search customer name, phone, email, or location..." value="{{ request('search') }}">
            </div>

            <select name="service" class="filter-select">
                <option value="">All Services</option>
                @foreach($services as $srv)
                    <option value="{{ $srv }}" {{ request('service') === $srv ? 'selected' : '' }}>
                        {{ $srv }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary" style="padding: 9px 18px;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span>Filter</span>
            </button>

            @if(request('search') || request('service') || (request('status') && request('status') !== 'All'))
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-secondary" style="padding: 9px 16px;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Enquiries Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="sno-cell">#</th>
                        <th style="min-width: 240px;">Customer Name & Mobile</th>
                        <th style="min-width: 170px;">Service Required</th>
                        <th style="min-width: 140px;">Status</th>
                        <th style="min-width: 130px;">Received Date</th>
                        <th style="text-align: right; min-width: 110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enquiries as $lead)
                        <tr>
                            <td class="sno-cell">
                                <span class="sno-num">#{{ str_pad($loop->iteration + ($enquiries->currentPage() - 1) * $enquiries->perPage(), 2, '0', STR_PAD_LEFT) }}</span>
                            </td>

                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        {{ strtoupper(substr($lead->name, 0, 1)) }}
                                    </div>
                                    <div class="customer-meta">
                                        <div class="customer-name" style="font-size: 15px;">{{ $lead->name }}</div>
                                        <div class="customer-contacts">
                                            <a href="tel:{{ $lead->mobile_number }}" class="phone-link" style="font-size: 13.5px;">
                                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                <span>{{ $lead->mobile_number }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span style="font-weight: 700; color: var(--carrier-blue); font-size: 14.5px;">{{ $lead->service_required }}</span>
                            </td>

                            <td>
                                <form method="POST" action="{{ route('admin.enquiries.status', $lead) }}" class="status-select-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="badge-status badge-{{ strtolower(str_replace(' ', '_', $lead->status)) }}" title="Change status">
                                        @foreach($statuses as $st)
                                            <option value="{{ $st }}" {{ $lead->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>

                            <td>
                                <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">
                                    {{ $lead->created_at->format('d M Y, h:i A') }}
                                </span>
                            </td>

                            <td style="text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <!-- View Details (Icon Only) -->
                                    <button type="button" class="btn-action-icon view" onclick="openLeadModal({{ $lead->id }})" title="View Complete Enquiry Dossier">
                                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <!-- Delete (Icon Only) -->
                                    <button type="button" 
                                            class="btn-action-icon delete" 
                                            data-delete-url="{{ route('admin.enquiries.destroy', $lead) }}" 
                                            data-delete-name="Lead from {{ $lead->name }} ({{ $lead->mobile_number }})" 
                                            data-delete-type="Customer Enquiry" 
                                            onclick="triggerDeleteModal(this)" 
                                            title="Delete Lead">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 60px 20px;">
                                <div style="width: 56px; height: 56px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; margin: 0 auto 12px;">
                                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <p style="font-size: 15px; font-weight: 700; color: var(--text-heading); margin-bottom: 4px;">No customer enquiries found in this view</p>
                                <span style="font-size: 13px; color: var(--text-muted);">New customer enquiries will appear here automatically when submitted from the public portal.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enquiries->total() > 0)
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-color); background: #F8FAFC;">
                {{ $enquiries->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Customer Enquiry Details Modal -->
<div class="modal-overlay" id="leadModal">
    <div class="modal-box" style="max-width: 620px;">
        <div class="modal-header-banner"></div>
        <div class="modal-header">
            <div class="modal-header-title-wrap">
                <div class="modal-header-icon" id="leadModalIcon">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="leadModalTitle">Customer Enquiry Details</h3>
                    <p id="leadModalSubtitle">Details submitted via public website enquiry form</p>
                </div>
            </div>
            <button type="button" class="modal-close" onclick="closeLeadModal()" aria-label="Close modal">&times;</button>
        </div>
        <div class="modal-body" id="leadModalContent">
            <div style="text-align:center; padding: 30px; color: var(--text-muted);">
                <div class="pulse-dot" style="margin: 0 auto 10px;"></div>
                <span>Loading enquiry details...</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openLeadModal(leadId) {
        const modal = document.getElementById('leadModal');
        const container = document.getElementById('leadModalContent');
        const modalTitle = document.getElementById('leadModalTitle');
        const modalSubtitle = document.getElementById('leadModalSubtitle');

        modalTitle.textContent = "Customer Enquiry Details";
        modalSubtitle.textContent = "Details submitted via public website enquiry form";

        modal.classList.add('active');
        container.innerHTML = `
            <div style="text-align:center; padding: 40px; color: var(--text-muted);">
                <div style="display:inline-block; width: 32px; height: 32px; border: 3px solid #e2e8f0; border-top-color: var(--accent); border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                <p style="margin-top: 12px; font-weight: 600; font-size: 13.5px;">Retrieving enquiry details...</p>
            </div>
        `;

        fetch(`/admin/enquiries/${leadId}`)
            .then(res => res.json())
            .then(data => {
                modalTitle.textContent = data.name;
                modalSubtitle.textContent = `Enquiry #${String(data.id).padStart(3, '0')} • Received ${data.created_at}`;

                let cleanPhone = data.mobile_number.replace(/[^0-9]/g, '');
                if (cleanPhone.length === 10) {
                    cleanPhone = '91' + cleanPhone;
                }
                const waUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent("Hello " + data.name + ", thank you for contacting Comfort Aircon regarding " + data.service_required + ". How can we assist you today?")}`;

                container.innerHTML = `
                    <div class="lead-outreach-banner">
                        <div style="flex: 1;">
                            <strong style="color: var(--text-heading); font-size: 13.5px; display: block;">Direct Outreach</strong>
                            <span style="font-size: 12px; color: var(--text-muted);">Contact customer directly via phone call or WhatsApp</span>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <a href="tel:${data.mobile_number}" class="btn btn-primary" style="padding: 8px 14px; font-size: 12.5px;">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span>Call</span>
                            </a>
                            <a href="${waUrl}" target="_blank" class="btn" style="background: #059669; color: #fff; padding: 8px 14px; font-size: 12.5px;">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.587 1.838.887 2.791.887 3.18 0 5.767-2.587 5.767-5.766.001-3.18-2.585-5.767-5.767-5.767zm3.391 8.163c-.144.405-.837.774-1.17.825-.335.05-.769.072-2.203-.497-1.435-.57-2.336-2.029-2.406-2.124-.07-.095-.583-.775-.583-1.479s.367-1.05.5-1.196c.133-.146.29-.182.387-.182.097 0 .193.001.277.006.09.004.21.034.321.302.115.277.393.957.427 1.027.034.07.057.152.011.243-.046.092-.07.148-.139.227-.07.079-.146.177-.208.238-.07.07-.143.146-.062.285.081.139.36 1.012 1.34 1.475.297.14.542.195.706.223.164.028.261.024.36-.089.099-.113.424-.494.537-.663.113-.169.227-.141.381-.084.154.057.978.461 1.147.546.169.085.282.127.323.197.041.07.041.409-.103.814z"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 18px; box-shadow: var(--shadow-xs);">
                        <table style="width: 100%; border-collapse: collapse; font-size: 14.5px;">
                            <tbody>
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted); width: 35%;">Name</td>
                                    <td style="padding: 12px 18px; font-weight: 700; color: var(--text-heading);">${data.name}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted);">Mobile Number</td>
                                    <td style="padding: 12px 18px; font-weight: 700;">
                                        <a href="tel:${data.mobile_number}" style="color: var(--accent); text-decoration: none; font-family: 'JetBrains Mono', monospace;">${data.mobile_number}</a>
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted);">Email</td>
                                    <td style="padding: 12px 18px; font-weight: 600;">${data.email ? `<a href="mailto:${data.email}" style="color: var(--accent); text-decoration: none;">${data.email}</a>` : '<span style="color: var(--text-muted);">Not provided</span>'}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted);">Service Required</td>
                                    <td style="padding: 12px 18px; font-weight: 700; color: var(--carrier-blue);">${data.service_required}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted);">Product Interested In</td>
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-heading);">${data.product_interested_in || 'General AC Service'}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted);">Location</td>
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-heading);">${data.location}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border-light);">
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted);">Status</td>
                                    <td style="padding: 12px 18px;">
                                        <span class="badge-status badge-${data.status.toLowerCase().replace(/ /g, '_')}">${data.status}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 18px; font-weight: 600; color: var(--text-muted);">Received Date</td>
                                    <td style="padding: 12px 18px; font-size: 13.5px; color: var(--text-muted); font-weight: 600; font-family: 'JetBrains Mono', monospace;">${data.created_at}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-bottom: 18px;">
                        <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 6px; letter-spacing: 0.5px;">Message</span>
                        <div class="dossier-message-box" style="font-size: 14.5px;">
                            ${data.message || 'No additional message provided.'}
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" class="btn btn-secondary" onclick="closeLeadModal()">Close Details</button>
                        <form method="POST" action="/admin/enquiries/${data.id}/status" style="display: flex; align-items: center; gap: 8px;">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                            <input type="hidden" name="_method" value="PATCH">
                            <span style="font-size: 13px; font-weight: 700; color: var(--text-muted);">Status:</span>
                            <select name="status" onchange="this.form.submit()" class="badge-status badge-${data.status.toLowerCase().replace(/ /g, '_')}" style="cursor: pointer; padding: 6px 12px; font-size: 12px;">
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}">${ '{{ $st }}' }</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                `;

                // Set selected option in quick status
                const modalSelect = container.querySelector('select[name="status"]');
                if (modalSelect) {
                    modalSelect.value = data.status;
                }
            })
            .catch(err => {
                container.innerHTML = '<div style="color: var(--danger); text-align: center; padding: 24px; font-weight: 600;">Failed to load enquiry details. Please try again.</div>';
            });
    }

    function closeLeadModal() {
        document.getElementById('leadModal').classList.remove('active');
    }

    document.getElementById('leadModal').addEventListener('click', function(e) {
        if (e.target === this) closeLeadModal();
    });
</script>
<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection
