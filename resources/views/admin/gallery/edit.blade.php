@extends('layouts.admin')

@section('title', 'Edit Gallery Photo')
@section('page_title', 'Edit Gallery Photo')

@section('content')
<div class="form-container">

    <a href="{{ route('admin.gallery.index') }}" class="back-nav">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Back to Gallery Management</span>
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
                    <h3>Edit Gallery Photo</h3>
                    <p>{{ $gallery->category }} &bull; {{ $gallery->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <span class="badge-status {{ $gallery->status === 'published' ? 'badge-live' : 'badge-draft' }}">
                {{ $gallery->status === 'published' ? '● Published' : '○ Unpublished' }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.gallery.update', $gallery) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Section 1 -->
            <div class="section-divider">1. Image & Category</div>

            <div class="form-group">
                <label class="form-label">Current Photo</label>
                <div class="current-image-card">
                    <a href="{{ $gallery->image_url }}" target="_blank" rel="noopener noreferrer" title="Click to view full photo in new tab">
                        <img src="{{ $gallery->image_url }}" alt="Current Gallery Photo" class="current-thumb">
                    </a>
                    <div>
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);">Active Image on Gallery</div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Upload a replacement below only if you wish to change this photo.</div>
                        <a href="{{ $gallery->image_url }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: var(--carrier-blue); margin-top: 6px; font-weight: 600; text-decoration: none;">
                            <span>View Full Size</span>
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <label class="form-label" for="image">Replace Photo (Optional)</label>
                <div class="dropzone-container" onclick="document.getElementById('image').click()">
                    <div style="font-weight: 700; font-size: 13.5px; color: var(--carrier-blue);">Click to browse and replace photo</div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 3px;">Formats: JPEG, PNG, WEBP (Max 8MB)</div>
                    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" style="display: none;" onchange="previewImage(this)">
                </div>

                <div class="preview-box" id="imagePreviewContainer">
                    <img id="imagePreview" src="#" alt="New Preview" class="preview-thumb">
                    <div>
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);" id="previewFileName">New Replacement Image</div>
                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-top: 2px;">Will replace current photo on submit</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="category">Category <span style="color: var(--danger);">*</span></label>
                <select id="category" name="category" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ old('category', $gallery->category) === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); font-size: 12.5px; margin-top: 5px; display: block;">Select one of the 4 approved gallery categories.</small>
            </div>

            <!-- Section 2 -->
            <div class="section-divider">2. Caption & Status</div>

            <div class="form-group">
                <label class="form-label" for="caption">Caption <small style="color: var(--text-muted); font-weight: normal;">(Optional)</small></label>
                <input type="text" id="caption" name="caption" class="form-control" value="{{ old('caption', $gallery->caption) }}" placeholder="e.g. Carrier Inverter AC Installation at Heritage Villa, Pondicherry">
            </div>

            <div class="form-group" style="margin-top: 24px;">
                <label class="form-label">Publish Status <span style="color: var(--danger);">*</span></label>
                <div class="radio-card-group">
                    <label class="radio-card">
                        <input type="radio" name="status" value="published" {{ old('status', $gallery->status) === 'published' ? 'checked' : '' }}>
                        <div>
                            <span class="radio-card-title" style="color: #059669;">Published (Visible on Gallery page)</span>
                            <span class="radio-card-desc">Visible on public gallery page immediately.</span>
                        </div>
                    </label>

                    <label class="radio-card">
                        <input type="radio" name="status" value="unpublished" {{ old('status', $gallery->status) === 'unpublished' ? 'checked' : '' }}>
                        <div>
                            <span class="radio-card-title" style="color: #64748B;">Unpublished (Hidden from Gallery page)</span>
                            <span class="radio-card-desc">Saved internally in admin, hidden from public website.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-accent">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Update Photo</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('previewFileName').textContent = input.files[0].name;
                document.getElementById('imagePreviewContainer').style.display = 'flex';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
