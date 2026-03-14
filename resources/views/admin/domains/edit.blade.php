@php $isEdit = isset($domain) && $domain->exists; @endphp

<x-admin-layout :title="$isEdit ? 'Edit Domain' : 'Create Domain'" :pageTitle="$isEdit ? 'Edit Domain: ' . $domain->name : 'Create Domain'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index') }}">Domains</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <x-slot:styles>
        <link href="{{ asset('vendor/summernote/css/summernote-bs5.min.css') }}" rel="stylesheet">
    </x-slot:styles>

    <style>
        .section-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 1.25rem; padding-bottom: 0.75rem;
            border-bottom: 1.5px solid var(--border-light);
        }
        .section-header .sh-icon {
            width: 34px; height: 34px; border-radius: 0.55rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .section-header h6 { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin: 0; }
        .section-header p { font-size: 0.75rem; color: var(--text-muted); margin: 0; }

        .toggle-card {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.85rem 1rem; border-radius: 0.65rem;
            border: 1.5px solid var(--border-light); background: #fff; transition: all 0.2s;
        }
        .toggle-card:has(input:checked) { border-color: var(--accent-success); background: rgba(0,200,83,0.04); }
        .toggle-card .toggle-label { display: flex; align-items: center; gap: 10px; }
        .toggle-card .toggle-label .tl-icon {
            width: 32px; height: 32px; border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; background: #f4f5f8; color: var(--text-muted); transition: all 0.2s;
        }
        .toggle-card:has(input:checked) .tl-icon { background: rgba(0,200,83,0.12); color: var(--accent-success); }
        .toggle-card .toggle-label span { font-size: 0.85rem; font-weight: 600; }

        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border-light); margin-top: 1.5rem;
        }

        /* Content Page Tabs */
        .content-tabs {
            display: flex; gap: 0; border-bottom: 2px solid var(--border-light);
            margin-bottom: 1.5rem;
        }
        .content-tab {
            padding: 0.75rem 1.25rem; cursor: pointer;
            font-size: 0.85rem; font-weight: 600; color: var(--text-muted);
            border-bottom: 2.5px solid transparent; margin-bottom: -2px;
            transition: all 0.2s; display: flex; align-items: center; gap: 8px;
        }
        .content-tab:hover { color: var(--text-primary); }
        .content-tab.active {
            color: var(--accent-primary);
            border-bottom-color: var(--accent-primary);
        }
        .content-tab i { font-size: 1.1rem; }

        .tab-content-panel { display: none; }
        .tab-content-panel.active { display: block; }

        /* SEO fields styling */
        .seo-fields {
            background: #f8fafc; border-radius: 0.65rem;
            padding: 1.25rem; margin-top: 1.25rem;
            border: 1px dashed var(--border-light);
        }
        .seo-fields-header {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 1rem; font-size: 0.8rem;
            font-weight: 600; color: var(--text-secondary);
        }
        .seo-fields-header i { font-size: 1rem; color: var(--accent-warning); }

        /* Logo circle preview */
        .logo-circle {
            width: 110px; height: 110px; border-radius: 50%;
            border: 3px dashed var(--border-light);
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden; cursor: pointer;
            background: #f8fafc; transition: all 0.3s ease;
        }
        .logo-circle:hover {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
        }
        .logo-circle.has-logo {
            border-style: solid;
            border-color: #e2e8f0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .logo-circle.has-logo:hover {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.12), 0 2px 12px rgba(0,0,0,0.08);
        }
        .logo-circle img {
            width: 100%; height: 100%;
            object-fit: cover; border-radius: 50%;
            transition: opacity 0.3s;
        }
        .logo-circle .logo-placeholder {
            display: flex; flex-direction: column;
            align-items: center; gap: 2px;
            color: var(--text-muted); transition: color 0.2s;
        }
        .logo-circle:hover .logo-placeholder { color: var(--accent-primary); }
        .logo-circle .logo-placeholder i { font-size: 1.6rem; }
        .logo-circle .logo-placeholder span { font-size: 0.7rem; font-weight: 600; }
        .logo-circle .logo-overlay {
            position: absolute; inset: 0; border-radius: 50%;
            background: rgba(0,0,0,0.45); display: flex;
            flex-direction: column; align-items: center;
            justify-content: center; gap: 2px;
            opacity: 0; transition: opacity 0.25s;
            color: #fff;
        }
        .logo-circle.has-logo:hover .logo-overlay { opacity: 1; }
        .logo-circle .logo-overlay i { font-size: 1.4rem; }
        .logo-circle .logo-overlay span { font-size: 0.7rem; font-weight: 600; }
        .logo-circle.removing img { opacity: 0.25; filter: grayscale(1); }
        .logo-circle.removing { border-color: var(--accent-danger); border-style: dashed; }

        /* Favicon preview */
        .favicon-preview {
            width: 52px; height: 52px; border-radius: 10px;
            border: 2.5px dashed var(--border-light);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; background: #f8fafc; transition: all 0.3s ease;
            overflow: hidden;
        }
        .favicon-preview:hover {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        .favicon-preview.has-favicon {
            border-style: solid; border-color: #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .favicon-preview img {
            width: 32px; height: 32px; object-fit: contain;
        }
        .favicon-preview .favicon-placeholder {
            color: var(--text-muted); font-size: 1.3rem;
            transition: color 0.2s;
        }
        .favicon-preview:hover .favicon-placeholder { color: var(--accent-primary); }
        .favicon-preview.removing img { opacity: 0.25; filter: grayscale(1); }
        .favicon-preview.removing { border-color: var(--accent-danger); border-style: dashed; }

        /* Summernote overrides */
        .note-editor.note-frame {
            border: 1.5px solid var(--border-light) !important;
            border-radius: 0.65rem !important;
            overflow: hidden;
        }
        .note-editor .note-toolbar {
            background: #f8fafc !important;
            border-bottom: 1.5px solid var(--border-light) !important;
            padding: 6px 8px !important;
        }
        .note-editor .note-editing-area .note-editable {
            background: #fff; padding: 1rem !important;
            font-size: 0.9rem; min-height: 200px;
        }
        .note-editor .note-statusbar { display: none !important; }
    </style>

    <form action="{{ $isEdit ? route('admin.domains.update', $domain) : route('admin.domains.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
            {{-- Domain Identity --}}
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-globe'></i>
                            </div>
                            <div>
                                <h6>Domain Identity</h6>
                                <p>Name and hostname for this domain</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input name="name" label="Display Name" :value="old('name', $domain->name ?? '')" required placeholder="e.g. Dubai Luxury Hotels" class="col-md-6" />
                            <x-form.input name="domain" label="Domain URL" :value="old('domain', $domain->domain ?? '')" required placeholder="e.g. dubailuxuryhotels.com" class="col-md-6" />
                            <x-form.input name="phone" label="Phone" :value="old('phone', $domain->phone ?? '')" placeholder="e.g. +971 4 123 4567" class="col-md-4" />
                            <x-form.input name="email" label="Email" type="email" :value="old('email', $domain->email ?? '')" placeholder="e.g. info@dubaihotels.com" class="col-md-4" />
                            <x-form.input name="address" label="Address" :value="old('address', $domain->address ?? '')" placeholder="e.g. Downtown Dubai, UAE" class="col-md-4" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Toggle --}}
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-toggle-right'></i>
                            </div>
                            <div>
                                <h6>Status</h6>
                                <p>Domain visibility</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="toggle-card">
                                <div class="toggle-label">
                                    <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                    <span>Active</span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                           {{ old('is_active', $isEdit ? $domain->is_active : true) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Homepage SEO --}}
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(245,158,11,0.1); color: var(--accent-warning);">
                                <i class='bx bx-search-alt'></i>
                            </div>
                            <div>
                                <h6>Homepage SEO</h6>
                                <p>Meta title, description & canonical URL for the homepage</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input name="meta_title" label="Meta Title" :value="old('meta_title', $domain->meta_title ?? '')" placeholder="e.g. Best Hotels in Dubai | Book Now" class="col-12" />
                            <x-form.textarea name="meta_description" label="Meta Description" :value="old('meta_description', $domain->meta_description ?? '')" placeholder="Brief description for search engines (max 160 chars recommended)" rows="3" class="col-12" />
                            <x-form.input name="canonical_url" label="Canonical URL" :value="old('canonical_url', $domain->canonical_url ?? '')" placeholder="https://yourdomain.com" class="col-12" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Logo & Favicon --}}
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                                <i class='bx bx-image'></i>
                            </div>
                            <div>
                                <h6>Logo & Favicon</h6>
                                <p>Brand identity for this domain</p>
                            </div>
                        </div>

                        <div class="d-flex flex-column align-items-center">
                            {{-- Logo Circle preview --}}
                            <div class="logo-circle {{ ($isEdit && $domain->logo_path) ? 'has-logo' : '' }}" id="logoCircle">
                                <img
                                    id="logoPreviewImg"
                                    src="{{ ($isEdit && $domain->logo_path) ? asset('storage/' . $domain->logo_path) : '' }}"
                                    alt="Logo"
                                    style="{{ ($isEdit && $domain->logo_path) ? '' : 'display:none;' }}"
                                >
                                <div class="logo-placeholder" id="logoPlaceholder" style="{{ ($isEdit && $domain->logo_path) ? 'display:none;' : '' }}">
                                    <i class='bx bx-image-add'></i>
                                    <span>Upload</span>
                                </div>
                                <div class="logo-overlay" id="logoOverlay">
                                    <i class='bx bx-camera'></i>
                                    <span>Change</span>
                                </div>
                            </div>

                            <label for="logo" class="btn btn-sm btn-outline-primary mt-3 px-3" style="font-size: 0.8rem;">
                                <i class='bx bx-upload me-1'></i> Choose Logo
                            </label>
                            <input type="file" name="logo" id="logo" accept="image/*" class="d-none {{ $errors->has('logo') ? 'is-invalid' : '' }}">
                            @error('logo')<div class="invalid-feedback d-block text-center" style="font-size: 0.78rem;">{{ $message }}</div>@enderror
                            <small class="text-muted mt-1" style="font-size: 0.72rem;">PNG, JPG, SVG or WebP. Max 2MB.</small>

                            @if($isEdit && $domain->logo_path)
                            <div class="mt-2" id="removeLogoWrap">
                                <label class="form-check form-check-inline mb-0" style="font-size: 0.78rem; cursor: pointer;">
                                    <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="removeLogoCheck">
                                    <span class="form-check-label text-danger"><i class='bx bx-trash-alt me-1'></i>Remove logo</span>
                                </label>
                            </div>
                            @endif
                        </div>

                        {{-- Favicon --}}
                        <hr class="my-3" style="border-color: var(--border-light);">
                        <div class="d-flex flex-column align-items-center">
                            <div class="favicon-preview {{ ($isEdit && $domain->favicon_path) ? 'has-favicon' : '' }}" id="faviconPreview">
                                <img
                                    id="faviconPreviewImg"
                                    src="{{ ($isEdit && $domain->favicon_path) ? asset('storage/' . $domain->favicon_path) : '' }}"
                                    alt="Favicon"
                                    style="{{ ($isEdit && $domain->favicon_path) ? '' : 'display:none;' }}"
                                >
                                <div class="favicon-placeholder" id="faviconPlaceholder" style="{{ ($isEdit && $domain->favicon_path) ? 'display:none;' : '' }}">
                                    <i class='bx bx-star'></i>
                                </div>
                            </div>
                            <span style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); margin-top: 0.4rem;">Favicon</span>

                            <label for="favicon" class="btn btn-sm btn-outline-secondary mt-2 px-3" style="font-size: 0.8rem;">
                                <i class='bx bx-upload me-1'></i> Choose Favicon
                            </label>
                            <input type="file" name="favicon" id="favicon" accept=".ico,.png,.svg,.jpg,.jpeg,.webp,image/*" class="d-none {{ $errors->has('favicon') ? 'is-invalid' : '' }}">
                            @error('favicon')<div class="invalid-feedback d-block text-center" style="font-size: 0.78rem;">{{ $message }}</div>@enderror
                            <small class="text-muted mt-1" style="font-size: 0.72rem;">ICO, PNG or SVG. Max 512KB.</small>

                            @if($isEdit && $domain->favicon_path)
                            <div class="mt-2" id="removeFaviconWrap">
                                <label class="form-check form-check-inline mb-0" style="font-size: 0.78rem; cursor: pointer;">
                                    <input type="checkbox" name="remove_favicon" value="1" class="form-check-input" id="removeFaviconCheck">
                                    <span class="form-check-label text-danger"><i class='bx bx-trash-alt me-1'></i>Remove favicon</span>
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            {{-- Defaults --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                <i class='bx bx-cog'></i>
                            </div>
                            <div>
                                <h6>Defaults</h6>
                                <p>Language settings for visitors on this domain</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.select name="default_language" label="Default Language" :options="[
                                'en' => 'English',
                                'ar' => 'Arabic',
                            ]" :selected="old('default_language', $domain->default_language ?? 'en')" class="col-md-6" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content Pages --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                                <i class='bx bx-file'></i>
                            </div>
                            <div>
                                <h6>Content Pages</h6>
                                <p>About Us, Privacy Policy & Terms with SEO meta tags</p>
                            </div>
                        </div>

                        {{-- Tabs --}}
                        <div class="content-tabs">
                            <div class="content-tab active" data-tab="about_us">
                                <i class='bx bx-info-circle'></i> About Us
                            </div>
                            <div class="content-tab" data-tab="privacy_policy">
                                <i class='bx bx-shield'></i> Privacy Policy
                            </div>
                            <div class="content-tab" data-tab="terms_conditions">
                                <i class='bx bx-list-check'></i> Terms & Conditions
                            </div>
                        </div>

                        {{-- About Us Tab --}}
                        <div class="tab-content-panel active" id="tab-about_us">
                            <textarea name="about_us" id="editor_about_us" class="summernote-editor">{!! old('about_us', $domain->about_us ?? '') !!}</textarea>

                            <div class="seo-fields">
                                <div class="seo-fields-header">
                                    <i class='bx bx-search-alt'></i> SEO Settings - About Us
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="about_us_meta_title" label="Meta Title" :value="old('about_us_meta_title', $domain->about_us_meta_title ?? '')" placeholder="About Us - Your Brand Name" class="col-md-6" />
                                    <x-form.input name="about_us_canonical_url" label="Canonical URL" :value="old('about_us_canonical_url', $domain->about_us_canonical_url ?? '')" placeholder="https://yourdomain.com/about-us" class="col-md-6" />
                                    <x-form.textarea name="about_us_meta_description" label="Meta Description" :value="old('about_us_meta_description', $domain->about_us_meta_description ?? '')" placeholder="Brief description for search engines (max 160 chars recommended)" rows="2" class="col-12" />
                                </div>
                            </div>
                        </div>

                        {{-- Privacy Policy Tab --}}
                        <div class="tab-content-panel" id="tab-privacy_policy">
                            <textarea name="privacy_policy" id="editor_privacy_policy" class="summernote-editor">{!! old('privacy_policy', $domain->privacy_policy ?? '') !!}</textarea>

                            <div class="seo-fields">
                                <div class="seo-fields-header">
                                    <i class='bx bx-search-alt'></i> SEO Settings - Privacy Policy
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="privacy_policy_meta_title" label="Meta Title" :value="old('privacy_policy_meta_title', $domain->privacy_policy_meta_title ?? '')" placeholder="Privacy Policy - Your Brand Name" class="col-md-6" />
                                    <x-form.input name="privacy_policy_canonical_url" label="Canonical URL" :value="old('privacy_policy_canonical_url', $domain->privacy_policy_canonical_url ?? '')" placeholder="https://yourdomain.com/privacy-policy" class="col-md-6" />
                                    <x-form.textarea name="privacy_policy_meta_description" label="Meta Description" :value="old('privacy_policy_meta_description', $domain->privacy_policy_meta_description ?? '')" placeholder="Brief description for search engines (max 160 chars recommended)" rows="2" class="col-12" />
                                </div>
                            </div>
                        </div>

                        {{-- Terms & Conditions Tab --}}
                        <div class="tab-content-panel" id="tab-terms_conditions">
                            <textarea name="terms_conditions" id="editor_terms_conditions" class="summernote-editor">{!! old('terms_conditions', $domain->terms_conditions ?? '') !!}</textarea>

                            <div class="seo-fields">
                                <div class="seo-fields-header">
                                    <i class='bx bx-search-alt'></i> SEO Settings - Terms & Conditions
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="terms_conditions_meta_title" label="Meta Title" :value="old('terms_conditions_meta_title', $domain->terms_conditions_meta_title ?? '')" placeholder="Terms & Conditions - Your Brand Name" class="col-md-6" />
                                    <x-form.input name="terms_conditions_canonical_url" label="Canonical URL" :value="old('terms_conditions_canonical_url', $domain->terms_conditions_canonical_url ?? '')" placeholder="https://yourdomain.com/terms-conditions" class="col-md-6" />
                                    <x-form.textarea name="terms_conditions_meta_description" label="Meta Description" :value="old('terms_conditions_meta_description', $domain->terms_conditions_meta_description ?? '')" placeholder="Brief description for search engines (max 160 chars recommended)" rows="2" class="col-12" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.domains.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Domain' : 'Create Domain' }}
            </button>
        </div>
    </form>

    <x-slot:scripts>
        <script src="{{ asset('vendor/summernote/js/summernote-bs5.min.js') }}"></script>
        <script>
            $(function() {
                // Init Summernote on all editors
                $('.summernote-editor').summernote({
                    height: 250,
                    placeholder: 'Write your content here...',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'hr']],
                        ['view', ['fullscreen', 'codeview']],
                    ],
                    fontSizes: ['10', '12', '14', '16', '18', '20', '24', '28', '36'],
                    callbacks: {
                        onInit: function() {
                            // Style the editor container
                            $(this).closest('.note-editor').css('margin-bottom', '0');
                        }
                    }
                });

                // Click circle to open file picker
                $('#logoCircle').on('click', function() {
                    $('#logo').trigger('click');
                });

                // Logo live preview on file select
                $('#logo').on('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#logoPreviewImg').attr('src', e.target.result).show();
                            $('#logoPlaceholder').hide();
                            $('#logoCircle').addClass('has-logo').removeClass('removing');
                            $('#removeLogoWrap').hide();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Remove logo checkbox
                $('#removeLogoCheck').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#logoCircle').addClass('removing');
                    } else {
                        $('#logoCircle').removeClass('removing');
                    }
                });

                // Click favicon to open file picker
                $('#faviconPreview').on('click', function() {
                    $('#favicon').trigger('click');
                });

                // Favicon live preview
                $('#favicon').on('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#faviconPreviewImg').attr('src', e.target.result).show();
                            $('#faviconPlaceholder').hide();
                            $('#faviconPreview').addClass('has-favicon').removeClass('removing');
                            $('#removeFaviconWrap').hide();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Remove favicon checkbox
                $('#removeFaviconCheck').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#faviconPreview').addClass('removing');
                    } else {
                        $('#faviconPreview').removeClass('removing');
                    }
                });

                // Tab switching
                $('.content-tab').on('click', function() {
                    const tab = $(this).data('tab');
                    $('.content-tab').removeClass('active');
                    $(this).addClass('active');
                    $('.tab-content-panel').removeClass('active');
                    $('#tab-' + tab).addClass('active');
                });
            });
        </script>
    </x-slot:scripts>
</x-admin-layout>
