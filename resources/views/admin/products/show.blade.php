@extends('layouts.admin')

@section('title', $product->name . ' — Product Details')
@section('page_title', 'Product Details')

@section('content')
    <div class="product-show-container">

        <!-- Top Navigation & Action Bar -->
        <div class="show-top-bar">
            <a href="{{ route('admin.products.index') }}" class="back-nav">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Products</span>
            </a>

            <div class="show-actions">
                <form method="POST" action="{{ route('admin.products.publish', $product) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="action-btn btn-secondary">
                        <span class="status-dot"
                            style="background: {{ $product->status === 'published' ? '#10b981' : '#94a3b8' }};"></span>
                        <span>{{ $product->status === 'published' ? 'Switch to Unpublished' : 'Publish to Website' }}</span>
                    </button>
                </form>

                <a href="{{ route('admin.products.edit', $product) }}" class="action-btn btn-accent">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Edit Product</span>
                </a>

                <button type="button" class="action-btn btn-danger"
                    data-delete-url="{{ route('admin.products.destroy', $product) }}"
                    data-delete-name="{{ $product->name }} (Model: {{ $product->model }})"
                    data-delete-type="Carrier AC Product" onclick="triggerDeleteModal(this)" title="Delete product">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Delete</span>
                </button>
            </div>
        </div>

        <!-- Main Dossier Layout Grid -->
        <div class="product-dossier-grid">

            <!-- Left Column: Photo & Quick Specs -->
            <div class="product-image-card">
                <div class="product-hero-photo-wrap">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-hero-photo"
                        onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'120\' height=\'120\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\'><rect width=\'24\' height=\'24\' fill=\'%23f1f5f9\'/><path d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'/></svg>'">
                </div>

                <div class="product-header-info">
                    <h2 class="product-title">{{ $product->name }}</h2>
                    <span class="model-badge">MODEL: {{ $product->model }}</span>
                </div>

                <div class="product-quick-meta">
                    <div class="meta-row">
                        <span class="meta-label">Visibility</span>
                        <span
                            class="status-pill {{ $product->status === 'published' ? 'status-published' : 'status-unpublished' }}">
                            <span class="status-dot"></span>
                            {{ $product->status === 'published' ? 'Published' : 'Unpublished' }}
                        </span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Created On</span>
                        <span class="meta-value">{{ $product->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Last Modified</span>
                        <span class="meta-value">{{ $product->updated_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Single Unified Product Details Card -->
            <div class="product-details-card">

                <!-- Section 1: Technical Specifications -->
                <div class="detail-section">
                    <div class="detail-section-header">
                        <div class="header-icon-box header-icon-blue">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="detail-section-header-text">
                            <h3>Technical Specifications</h3>
                            <p>Model specifications, capacity, and engineering ratings</p>
                        </div>
                    </div>

                    <div class="spec-items-grid">
                        @php
                            $specLines = array_filter(array_map('trim', explode("\n", $product->specifications)));
                        @endphp
                        @forelse($specLines as $line)
                            @php
                                $parts = explode(':', $line, 2);
                                $hasKey = count($parts) > 1;
                            @endphp
                            <div class="spec-item-card">
                                @if ($hasKey)
                                    <span class="spec-key">{{ trim($parts[0]) }}</span>
                                    <span class="spec-value">{{ trim($parts[1]) }}</span>
                                @else
                                    <span class="spec-key">Specification</span>
                                    <span class="spec-value">{{ $line }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="empty-detail-state">No technical specifications recorded.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card-section-divider"></div>

                <!-- Section 2: Key Features & Technologies -->
                <div class="detail-section">
                    <div class="detail-section-header">
                        <div class="header-icon-box header-icon-green">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="detail-section-header-text">
                            <h3>Key Features & Technologies</h3>
                            <p>Customer-facing highlights and inverter capabilities</p>
                        </div>
                    </div>

                    <div class="features-list">
                        @php
                            $featureLines = array_filter(array_map('trim', explode("\n", $product->features)));
                        @endphp
                        @forelse($featureLines as $feat)
                            <div class="feature-item-row">
                                <div class="feature-check-icon">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="feature-text">{{ $feat }}</span>
                            </div>
                        @empty
                            <div class="empty-detail-state">No key features listed.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card-section-divider"></div>



            </div>

        </div>

    </div>
@endsection
