@extends('layouts.admin')

@section('title', 'Edit Contact Details')
@section('page_title', 'Edit Contact Details')

@section('content')
    <div class="contact-container">

        <!-- Form Section -->
        <div class="settings-card">
            <div class="settings-header">
                <div class="settings-header-left">
                    <div class="settings-header-icon">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div class="settings-header-text">
                        <h3>Edit Contact Details</h3>
                        <p>Contact values edited here appear on the public website header, footer and Contact Us page.</p>
                    </div>
                </div>
            </div>



            <form method="POST" action="{{ route('admin.contact.update') }}">
                @csrf

                <!-- Section 1: Customer Contact Actions -->
                <div class="section-title">1. Customer Contact Actions</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control"
                            value="{{ old('phone', $contact->phone) }}" required placeholder="+91 7708 650 535"
                            oninput="updatePreview()">
                        {{-- <span class="helper-text">Appears on header, footer, and Contact Us. On mobile, Call opens the dial screen.</span> --}}
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="whatsapp">WhatsApp <span
                                style="color: var(--danger);">*</span></label>
                        <input type="text" id="whatsapp" name="whatsapp" class="form-control"
                            value="{{ old('whatsapp', $contact->whatsapp) }}" required placeholder="+91 7708 650 535"
                            oninput="updatePreview()">
                        {{-- <span class="helper-text">Appears on floating WhatsApp button and header button.</span> --}}
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email <span style="color: var(--danger);">*</span></label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ old('email', $contact->email) }}" required placeholder="sales@itsk.in"
                        oninput="updatePreview()">
                    {{-- <span class="helper-text">Public display email and enquiry notification email.</span> --}}
                </div>

                <!-- Section 2: Physical Location & Working Hours -->
                <div class="section-title">2. Physical Location & Working Hours</div>

                <div class="form-group">
                    <label class="form-label" for="address">Address <span style="color: var(--danger);">*</span></label>
                    <textarea id="address" name="address" class="form-control" rows="2" required
                        placeholder="No.37, Kamaraj Salai, Thattanchavady, Puducherry - 605009" oninput="updatePreview()">{{ old('address', $contact->address) }}</textarea>
                    {{-- <span class="helper-text">Appears on footer and Contact Us page.</span> --}}
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="working_hours">Working hours <span
                                style="color: var(--danger);">*</span></label>
                        <input type="text" id="working_hours" name="working_hours" class="form-control"
                            value="{{ old('working_hours', $contact->working_hours) }}" required
                            placeholder="Mon - Sat: 9:00 AM - 8:00 PM" oninput="updatePreview()">
                        {{-- <span class="helper-text">Appears on footer and Contact Us page.</span> --}}
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="google_maps_link">Google Maps link <span
                                style="color: var(--danger);">*</span></label>
                        <input type="text" id="google_maps_link" name="google_maps_link" class="form-control"
                            value="{{ old('google_maps_link', $contact->google_maps_link) }}" required
                            placeholder="https://maps.google.com/?q=...">
                        {{-- <span class="helper-text">Appears on Contact Us map.</span> --}}
                    </div>
                </div>

                <!-- Section 3: Social Media Presence -->
                <div class="section-title">3. Social Media Links (Optional)</div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="facebook_url">Facebook URL</label>
                        <input type="url" id="facebook_url" name="facebook_url" class="form-control"
                            value="{{ old('facebook_url', $contact->facebook_url) }}"
                            placeholder="https://facebook.com/comfortaircon">
                        {{-- <span class="helper-text">Social icon (hidden if empty).</span> --}}
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="instagram_url">Instagram URL</label>
                        <input type="url" id="instagram_url" name="instagram_url" class="form-control"
                            value="{{ old('instagram_url', $contact->instagram_url) }}"
                            placeholder="https://instagram.com/comfortaircon">
                        {{-- <span class="helper-text">Social icon (hidden if empty).</span> --}}
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="twitter_url">Twitter URL</label>
                        <input type="url" id="twitter_url" name="twitter_url" class="form-control"
                            value="{{ old('twitter_url', $contact->twitter_url) }}"
                            placeholder="https://twitter.com/comfortaircon">
                        {{-- <span class="helper-text">Social icon (hidden if empty).</span> --}}
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="youtube_url">YouTube URL</label>
                        <input type="url" id="youtube_url" name="youtube_url" class="form-control"
                            value="{{ old('youtube_url', $contact->youtube_url) }}"
                            placeholder="https://youtube.com/@comfortaircon">
                        {{-- <span class="helper-text">Social icon (hidden if empty).</span> --}}
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-accent" style="padding: 12px 28px;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Update Contact Details</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Website Display Preview Card -->
        <div class="preview-sticky-card">
            <div class="preview-header">
                <h4>Website Display Preview</h4>
            </div>

            <div class="preview-phone-pill">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span id="previewPhoneVal">{{ $contact->phone }}</span>
            </div>

            <div class="preview-wa-pill">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.587 1.838.887 2.791.887 3.18 0 5.767-2.587 5.767-5.766.001-3.18-2.585-5.767-5.767-5.767zm3.391 8.163c-.144.405-.837.774-1.17.825-.335.05-.769.072-2.203-.497-1.435-.57-2.336-2.029-2.406-2.124-.07-.095-.583-.775-.583-1.479s.367-1.05.5-1.196c.133-.146.29-.182.387-.182.097 0 .193.001.277.006.09.004.21.034.321.302.115.277.393.957.427 1.027.034.07.057.152.011.243-.046.092-.07.148-.139.227-.07.079-.146.177-.208.238-.07.07-.143.146-.062.285.081.139.36 1.012 1.34 1.475.297.14.542.195.706.223.164.028.261.024.36-.089.099-.113.424-.494.537-.663.113-.169.227-.141.381-.084.154.057.978.461 1.147.546.169.085.282.127.323.197.041.07.041.409-.103.814z" />
                </svg>
                <span id="previewWaVal">{{ $contact->whatsapp }}</span>
            </div>

            <div class="preview-card-item">
                <div class="preview-label">Email (Public & Notifications)</div>
                <div class="preview-value" id="previewEmailVal">{{ $contact->email }}</div>
            </div>

            <div class="preview-card-item">
                <div class="preview-label">Address (Footer & Contact Us)</div>
                <div class="preview-value" id="previewAddressVal">{{ $contact->address }}</div>
            </div>

            <div class="preview-card-item">
                <div class="preview-label">Working Hours</div>
                <div class="preview-value" id="previewHoursVal">{{ $contact->working_hours }}</div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        function updatePreview() {
            document.getElementById('previewPhoneVal').textContent = document.getElementById('phone').value || 'Not set';
            document.getElementById('previewWaVal').textContent = document.getElementById('whatsapp').value || 'Not set';
            document.getElementById('previewEmailVal').textContent = document.getElementById('email').value || 'Not set';
            document.getElementById('previewAddressVal').textContent = document.getElementById('address').value ||
                'Not set';
            document.getElementById('previewHoursVal').textContent = document.getElementById('working_hours').value ||
                'Not set';
        }
    </script>
@endsection
