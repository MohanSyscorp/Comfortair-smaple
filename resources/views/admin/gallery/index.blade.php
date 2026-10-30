@extends('layouts.admin')

@section('title', 'Gallery Management')
@section('page_title', 'Gallery Management')

@section('content')
<div class="gallery-container">

    <!-- Action Header -->
    <div class="action-header">
        <div class="action-header-left">
            <h3>Gallery Management</h3>
            <p>Upload and manage portfolio photos shown on the public Gallery page.</p>
        </div>
        <a href="{{ route('admin.gallery.create') }}" class="btn btn-accent">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Upload Photo</span>
        </a>
    </div>

    <!-- Category Filter Tabs -->
    <div class="category-tabs-bar">
        <a href="{{ route('admin.gallery.index') }}" class="tab-item {{ !request('category') ? 'active' : '' }}">
            <span>All Categories ({{ $totalCount ?? 0 }})</span>
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.gallery.index', ['category' => $cat]) }}" class="tab-item {{ request('category') === $cat ? 'active' : '' }}">
                <span>{{ $cat }} ({{ $categoryCounts[$cat] ?? 0 }})</span>
            </a>
        @endforeach
    </div>

    <!-- Gallery Grid -->
    @if($items->count() > 0)
        <div class="gallery-grid">
            @foreach($items as $item)
                <div class="gallery-card">
                    <div class="gallery-card-img-wrap">
                        <img src="{{ $item->image_url }}" alt="{{ $item->caption ?? 'Gallery Image' }}" class="gallery-card-img"
                            onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'290\' height=\'200\' fill=\'%230b1e38\'><text x=\'50%\' y=\'50%\' fill=\'%2364748b\' text-anchor=\'middle\' font-size=\'14\'>Carrier Gallery</text></svg>'">
                        <span class="card-category-badge">{{ $item->category }}</span>

                        <div class="card-publish-badge">
                            <form method="POST" action="{{ route('admin.gallery.publish', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-toggle-sm {{ $item->status === 'published' ? 'btn-status-active' : 'btn-status-inactive' }}" title="Toggle website publish status">
                                    {{ $item->status === 'published' ? '● Published' : '○ Unpublished' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="gallery-card-body">
                        <div class="card-caption">
                            {{ $item->caption ?: 'Portfolio Photograph (' . $item->category . ')' }}
                        </div>

                        <div class="card-footer-actions">
                            <span style="font-size: 12.5px; color: var(--text-muted); font-weight: 500;">
                                {{ $item->created_at->format('d M Y') }}
                            </span>

                            <div style="display: flex; gap: 8px;">
                                <!-- Edit (Icon Only) -->
                                <a href="{{ route('admin.gallery.edit', $item) }}" class="btn-action-icon edit" title="Edit Photo Details">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>

                                <!-- Delete (Icon Only) -->
                                <button type="button" 
                                        class="btn-action-icon delete" 
                                        data-delete-url="{{ route('admin.gallery.destroy', $item) }}" 
                                        data-delete-name="{{ $item->caption ?: 'Gallery Photo (' . $item->category . ')' }}" 
                                        data-delete-type="Gallery Photo" 
                                        onclick="triggerDeleteModal(this)" 
                                        title="Delete Photo">
                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($items->total() > 0)
            <div style="margin-top: 24px; padding: 14px 20px; background: #FFFFFF; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <div class="table-card" style="padding: 60px 24px; text-align: center; color: var(--text-muted);">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; margin: 0 auto 12px;">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p style="font-size: 15px; font-weight: 700; color: var(--text-heading); margin-bottom: 4px;">No gallery photos found in this category</p>
            <span style="font-size: 13px; color: var(--text-muted);">Upload installation, completed project, or showroom photos to showcase your work.</span>
        </div>
    @endif

</div>
@endsection
