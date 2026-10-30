@extends('layouts.admin')

@section('title', 'Upload Gallery Photo')
@section('page_title', 'Upload Gallery Photo')

@section('content')
    <div class="form-container">

        <a href="{{ route('admin.gallery.index') }}" class="back-nav">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Gallery Management</span>
        </a>

        <div class="form-card">
            <div class="form-header">
                <div class="form-header-icon">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="form-header-text">
                    <h3>Upload Gallery Photo</h3>
                    <p>Photos uploaded here appear on the public website Gallery page under the selected category.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Section 1 -->
                <div class="section-divider">1. Image & Category</div>

                <div class="form-group">
                    <label class="form-label" for="image">Image <span
                            style="color: var(--danger);">*</span></label>
                    <div class="dropzone-container" onclick="document.getElementById('image').click()">
                        <div class="dropzone-icon">
                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="dropzone-title">Click to choose photo from computer</div>
                        <div class="dropzone-sub">Supported formats: JPEG, PNG, WEBP (Max: 8MB)</div>
                        <input type="file" id="image" name="image" class="form-control"
                            accept="image/jpeg,image/png,image/webp" required style="display: none;"
                            onchange="previewImage(this)">
                    </div>

                    <div class="preview-box" id="imagePreviewContainer">
                        <img id="imagePreview" src="#" alt="Preview" class="preview-thumb">
                        <div>
                            <div style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);"
                                id="previewFileName">Selected Photograph</div>
                            <div style="font-size: 12px; color: #059669; font-weight: 600; margin-top: 2px;">Ready for upload</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Category <span
                            style="color: var(--danger);">*</span></label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select an approved category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color: var(--text-muted); font-size: 12.5px; margin-top: 5px; display: block;">Select one of the 4 approved gallery categories.</small>
                </div>

                <!-- Section 2 -->
                <div class="section-divider">2. Caption & Status</div>

                <div class="form-group">
                    <label class="form-label" for="caption">Caption <small
                            style="color: var(--text-muted); font-weight: normal;">(Optional)</small></label>
                    <input type="text" id="caption" name="caption" class="form-control" value="{{ old('caption') }}"
                        placeholder="e.g. Carrier Inverter AC Installation at Heritage Villa, Pondicherry">
                </div>

                <div class="form-group" style="margin-top: 24px;">
                    <label class="form-label">Publish Status <span
                            style="color: var(--danger);">*</span></label>
                    <div class="radio-card-group">
                        <label class="radio-card">
                            <input type="radio" name="status" value="published"
                                {{ old('status', 'published') === 'published' ? 'checked' : '' }}>
                            <div>
                                <span class="radio-card-title" style="color: #059669;">Published (Visible on Gallery page)</span>
                                <span class="radio-card-desc">Visible on the public website Gallery page immediately.</span>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="status" value="unpublished"
                                {{ old('status') === 'unpublished' ? 'checked' : '' }}>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Upload Photo</span>
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
