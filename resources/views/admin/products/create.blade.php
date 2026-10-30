@extends('layouts.admin')

@section('title', 'Add Product')
@section('page_title', 'Add Product')

@section('content')
<div class="form-container">

    <a href="{{ route('admin.products.index') }}" class="back-nav">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Back to Product Management</span>
    </a>

    <div class="form-card">
        <div class="form-header">
            <div class="form-header-icon">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="form-header-text">
                <h3>Add New Product</h3>
                <p>Fields defined below appear on the public website product catalogue card and enquiry form.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Section 1 -->
            <div class="section-divider">1. Product Name & Model</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Product Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Carrier Ester Neo 1.5 Ton 3 Star Inverter Split AC">
                </div>

                <div class="form-group">
                    <label class="form-label" for="model">Model <span style="color: var(--danger);">*</span></label>
                    <input type="text" id="model" name="model" class="form-control" value="{{ old('model') }}" required placeholder="e.g. CAI18ER3R30F0">
                </div>
            </div>

            <!-- Section 2 -->
            <div class="section-divider">2. Specifications & Features</div>

            <div class="form-group">
                <label class="form-label" for="specifications">Specifications <span style="color: var(--danger);">*</span></label>
                <textarea id="specifications" name="specifications" class="form-control" rows="4" required placeholder="Capacity: 1.5 Ton&#10;Energy Rating: 3 Star&#10;Cooling Capacity: 5050 W&#10;Refrigerant: R32 Green Gas&#10;Condenser: 100% Copper">{{ old('specifications') }}</textarea>
                <small style="color: var(--text-muted); font-size: 12.5px; margin-top: 5px; display: block;">Enter each technical specification on a new line for clean display.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="features">Features <span style="color: var(--danger);">*</span></label>
                <textarea id="features" name="features" class="form-control" rows="4" required placeholder="PM 2.5 Dual Filtration Filter&#10;Fast Cool Turbo Mode&#10;Auto Cleanser Technology&#10;Stabilizer Free Operation&#10;Hydrophilic Blue Fins">{{ old('features') }}</textarea>
                <small style="color: var(--text-muted); font-size: 12.5px; margin-top: 5px; display: block;">Key features shown on product cards and details.</small>
            </div>

            <!-- Section 3 -->
            <div class="section-divider">3. Product Image & Status</div>

            <div class="form-group">
                <label class="form-label" for="image">Product Image <span style="color: var(--danger);">*</span></label>
                <div class="dropzone-container" onclick="document.getElementById('image').click()">
                    <div class="dropzone-icon">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="dropzone-title">Click to select product photo</div>
                    <div class="dropzone-sub">Supported formats: JPEG, PNG, WEBP (Max: 5MB)</div>
                    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" required style="display: none;" onchange="previewImage(this)">
                </div>

                <div class="preview-box" id="imagePreviewContainer">
                    <img id="imagePreview" src="#" alt="Preview" class="preview-thumb">
                    <div>
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);" id="previewFileName">Selected Image</div>
                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-top: 2px;">Ready for upload</div>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 24px;">
                <label class="form-label">Publish Status <span style="color: var(--danger);">*</span></label>
                <div class="radio-card-group">
                    <label class="radio-card">
                        <input type="radio" name="status" value="published" {{ old('status', 'published') === 'published' ? 'checked' : '' }}>
                        <div>
                            <span class="radio-card-title" style="color: #059669;">Published (Visible on website)</span>
                            <span class="radio-card-desc">Immediately visible in public catalogue and customer enquiry forms.</span>
                        </div>
                    </label>

                    <label class="radio-card">
                        <input type="radio" name="status" value="unpublished" {{ old('status') === 'unpublished' ? 'checked' : '' }}>
                        <div>
                            <span class="radio-card-title" style="color: #64748B;">Unpublished (Hidden from website)</span>
                            <span class="radio-card-desc">Saved internally in admin dashboard, hidden from public website.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-accent">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Save Product</span>
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
