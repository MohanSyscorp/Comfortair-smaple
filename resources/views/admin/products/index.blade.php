@extends('layouts.admin')

@section('title', 'Product Management')
@section('page_title', 'Product Management')

@section('content')
    <div class="products-container">

        <!-- Action Header -->
        <div class="action-header">
            <div class="action-header-left">
                <h3>Product Management</h3>
                {{-- <p>Add, edit, delete, and publish Carrier AC products shown on the public website catalogue.</p> --}}
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-accent">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Product</span>
            </a>
        </div>

        <!-- Quick Metrics Bar -->
        <div class="catalogue-metrics-bar">
            <div class="metric-pill-card">
                <div class="metric-icon-wrap">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="metric-info">
                    <div class="val">{{ $products->total() }}</div>
                    <div class="lbl">Total Products</div>
                </div>
            </div>

            <div class="metric-pill-card">
                <div class="metric-icon-wrap success">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="metric-info">
                    <div class="val">{{ $metrics['published'] ?? 0 }}</div>
                    <div class="lbl">Published (Visible on Web)</div>
                </div>
            </div>

            <div class="metric-pill-card">
                <div class="metric-icon-wrap warning">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div class="metric-info">
                    <div class="val">{{ $metrics['unpublished'] ?? 0 }}</div>
                    <div class="lbl">Unpublished (Hidden)</div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.products.index') }}" class="filter-form">
                <div class="filter-search-box">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="filter-search-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" class="filter-input"
                        placeholder="Search by product name or model...">
                </div>

                <select name="status" class="filter-select">
                    <option value="">All Products</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published (Visible on
                        website)</option>
                    <option value="unpublished" {{ request('status') == 'unpublished' ? 'selected' : '' }}>Unpublished
                        (Hidden from website)</option>
                </select>

                <button type="submit" class="btn btn-primary" style="padding: 9px 18px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filter</span>
                </button>

                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"
                        style="padding: 9px 16px;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Products Table Card -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="sno-cell">S.No</th>
                            <th style="min-width: 260px;">Product Name</th>
                            <th style="min-width: 140px;">Status</th>
                            <th style="min-width: 130px;">Added Date</th>
                            <th style="text-align: right; min-width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="sno-cell">
                                    <span
                                        class="sno-badge">{{ str_pad($loop->iteration + ($products->currentPage() - 1) * $products->perPage(), 2, '0', STR_PAD_LEFT) }}</span>
                                </td>

                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumb-wrap">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                class="product-thumb"
                                                onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\'><rect width=\'24\' height=\'24\' fill=\'%23f1f5f9\'/><path d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'/></svg>'">
                                        </div>
                                        <div class="product-meta">
                                            <div class="product-name">{{ $product->name }}</div>
                                            <span class="product-model-code">Model: {{ $product->model }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <form method="POST" action="{{ route('admin.products.publish', $product) }}"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn-status-toggle {{ $product->status === 'published' ? 'live' : 'draft' }}"
                                            title="Click to toggle publish status">
                                            <span class="status-dot"></span>
                                            <span>{{ $product->status === 'published' ? 'Published' : 'Unpublished' }}</span>
                                        </button>
                                    </form>
                                </td>

                                <td>
                                    <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;">
                                        {{ $product->created_at->format('d M Y') }}
                                    </span>
                                </td>

                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        <!-- View Details (Show Page) -->
                                        <a href="{{ route('admin.products.show', $product) }}"
                                            class="btn-action-icon view" title="View Product Details">
                                            <svg width="17" height="17" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Edit (Icon Only) -->
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="btn-action-icon edit" title="Edit Product">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <!-- Delete (Icon Only) -->
                                        <button type="button" class="btn-action-icon delete"
                                            data-delete-url="{{ route('admin.products.destroy', $product) }}"
                                            data-delete-name="{{ $product->name }} (Model: {{ $product->model }})"
                                            data-delete-type="Carrier AC Product" onclick="triggerDeleteModal(this)"
                                            title="Delete Product">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 60px 20px;">
                                    <div
                                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
                                        <div
                                            style="width: 56px; height: 56px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8;">
                                            <svg width="28" height="28" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <p
                                            style="font-weight: 700; color: var(--text-heading); font-size: 15px; margin: 0;">
                                            No Carrier AC Products Found</p>
                                        <span style="font-size: 13px; color: var(--text-muted); max-width: 320px;">Adjust
                                            search criteria or click "Add New Product" to populate the catalogue.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->total() > 0)
                <div style="padding: 16px 24px; border-top: 1px solid var(--border-color); background: #F8FAFC;">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
