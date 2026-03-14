@php $isEdit = isset($location) && $location->exists; @endphp

<x-admin-layout :title="$isEdit ? 'Edit Location' : 'Create Location'" :pageTitle="$isEdit ? 'Edit Location: ' . $location->name : 'Create Location'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.locations.index') }}">Locations</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <style>
        .loc-tabs {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 6px; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem; border: 1.5px solid var(--border-light);
        }
        .loc-tabs .loc-tab {
            display: flex; align-items: center; gap: 8px;
            padding: 0.6rem 1.1rem; border-radius: 0.65rem; border: none;
            background: transparent; font-size: 0.82rem; font-weight: 600;
            color: var(--text-secondary); cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none; position: relative; overflow: hidden;
        }
        .loc-tabs .loc-tab::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            opacity: 0; transition: opacity 0.25s; border-radius: inherit;
        }
        .loc-tabs .loc-tab > * { position: relative; z-index: 1; }
        .loc-tabs .loc-tab:hover { background: #f4f5f8; color: var(--text-primary); }
        .loc-tabs .loc-tab.active::before { opacity: 1; }
        .loc-tabs .loc-tab.active { color: #fff; box-shadow: 0 4px 14px rgba(102,126,234,0.3); }
        .loc-tabs .loc-tab .tab-icon {
            width: 28px; height: 28px; border-radius: 0.4rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; background: rgba(0,0,0,0.04); transition: all 0.25s;
        }
        .loc-tabs .loc-tab.active .tab-icon { background: rgba(255,255,255,0.2); }

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

        .domain-check {
            display: flex; align-items: center; gap: 8px;
            padding: 0.55rem 0.85rem; border-radius: 0.6rem;
            border: 1.5px solid var(--border-light); background: #fff;
            transition: all 0.2s; cursor: pointer;
        }
        .domain-check:hover { border-color: var(--accent-primary); }
        .domain-check:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .domain-check input { margin: 0; flex-shrink: 0; }
        .domain-check label { cursor: pointer; font-size: 0.82rem; font-weight: 500; margin: 0; }

        .image-upload-zone {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 1.5rem; border-radius: 0.75rem;
            border: 2px dashed var(--border-light); background: #fafbfc;
            transition: all 0.25s; cursor: pointer; text-align: center; min-height: 160px;
        }
        .image-upload-zone:hover { border-color: var(--accent-primary); background: rgba(102,126,234,0.02); }
        .image-upload-zone .uz-icon {
            width: 48px; height: 48px; border-radius: 0.65rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 8px;
            background: rgba(102,126,234,0.08); color: var(--accent-primary);
        }
        .image-upload-zone .uz-label { font-size: 0.82rem; font-weight: 600; color: var(--text-primary); }
        .image-upload-zone .uz-hint { font-size: 0.72rem; color: var(--text-muted); margin-top: 2px; }
        .image-upload-zone img.uz-preview {
            max-height: 120px; max-width: 100%; object-fit: contain;
            border-radius: 0.5rem; margin-bottom: 8px;
        }

        .coord-input {
            display: flex; align-items: center; gap: 10px;
            padding: 0.6rem 0.85rem; border-radius: 0.65rem;
            border: 1.5px solid var(--border-light); background: #fff; transition: all 0.2s;
        }
        .coord-input:focus-within { border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(102,126,234,0.08); }
        .coord-input .ci-icon {
            width: 32px; height: 32px; border-radius: 0.45rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem; flex-shrink: 0;
        }
        .coord-input .ci-body { flex: 1; min-width: 0; }
        .coord-input .ci-body label {
            display: block; font-size: 0.68rem; font-weight: 600;
            color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 1px;
        }
        .coord-input .ci-body input {
            width: 100%; border: none; outline: none; background: transparent;
            font-size: 0.85rem; font-weight: 500; color: var(--text-primary);
            font-family: 'SFMono-Regular', 'Consolas', monospace;
        }
        .coord-input .ci-body input::placeholder { color: #c0c4cc; font-family: inherit; }

        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border-light); margin-top: 1.5rem;
        }
    </style>

    <form action="{{ $isEdit ? route('admin.locations.update', $location) : route('admin.locations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Tab Navigation --}}
        <div class="loc-tabs" role="tablist">
            <a class="loc-tab active" data-bs-toggle="tab" href="#loc-general" role="tab">
                <span class="tab-icon"><i class='bx bx-map-pin'></i></span>
                <span>General</span>
            </a>
            <a class="loc-tab" data-bs-toggle="tab" href="#loc-media" role="tab">
                <span class="tab-icon"><i class='bx bx-image'></i></span>
                <span>Media & Location</span>
            </a>
            @if(isset($domains) && $domains->count())
            <a class="loc-tab" data-bs-toggle="tab" href="#loc-domains" role="tab">
                <span class="tab-icon"><i class='bx bx-globe'></i></span>
                <span>Domains</span>
            </a>
            @endif
            <a class="loc-tab" data-bs-toggle="tab" href="#loc-seo" role="tab">
                <span class="tab-icon"><i class='bx bx-search-alt'></i></span>
                <span>SEO</span>
            </a>
        </div>

        <div class="tab-content">
            {{-- ═══ GENERAL ═══ --}}
            <div class="tab-pane fade show active" id="loc-general">
                <div class="row g-3">
                    {{-- Identity --}}
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                        <i class='bx bx-map-pin'></i>
                                    </div>
                                    <div>
                                        <h6>Location Details</h6>
                                        <p>Name, city, country and descriptions</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="name" label="Location Name" :value="old('name', $location->name ?? '')" required placeholder="e.g. Dubai Marina" class="col-md-4" />
                                    <x-form.input name="city" label="City" :value="old('city', $location->city ?? '')" placeholder="e.g. Dubai" class="col-md-4" />
                                    <x-form.input name="country" label="Country" :value="old('country', $location->country ?? '')" placeholder="e.g. UAE" class="col-md-4" />
                                    <x-form.input name="short_description" label="Short Description" :value="old('short_description', $location->short_description ?? '')" placeholder="Brief one-liner for cards and listings" class="col-12" />
                                    <x-form.textarea name="description" label="Full Description" :value="old('description', $location->description ?? '')" rows="5" placeholder="Detailed description of this location, its highlights and attractions..." class="col-12" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                        <i class='bx bx-toggle-right'></i>
                                    </div>
                                    <div>
                                        <h6>Status</h6>
                                        <p>Visibility and prominence</p>
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
                                                   {{ old('is_active', $isEdit ? $location->is_active : true) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div class="toggle-card">
                                        <div class="toggle-label">
                                            <div class="tl-icon"><i class='bx bx-star'></i></div>
                                            <span>Featured</span>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox" name="is_featured" value="1" class="form-check-input" role="switch"
                                                   {{ old('is_featured', $isEdit ? $location->is_featured : false) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                {{-- Stats (edit only) --}}
                                @if($isEdit)
                                <div class="mt-4 pt-3" style="border-top: 1.5px solid var(--border-light);">
                                    <div class="d-flex gap-3">
                                        <div class="text-center flex-fill p-2" style="background: rgba(102,126,234,0.05); border-radius: 0.55rem;">
                                            <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-primary);">{{ $location->hotels()->count() }}</div>
                                            <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Hotels</div>
                                        </div>
                                        <div class="text-center flex-fill p-2" style="background: rgba(0,200,83,0.05); border-radius: 0.55rem;">
                                            <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-success);">{{ $location->domains()->count() }}</div>
                                            <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Domains</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ MEDIA & LOCATION ═══ --}}
            <div class="tab-pane fade" id="loc-media">
                <div class="row g-3">
                    {{-- Image --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(118,75,162,0.1); color: var(--accent-secondary);">
                                        <i class='bx bx-image-alt'></i>
                                    </div>
                                    <div>
                                        <h6>Cover Image</h6>
                                        <p>Displayed on location cards and hero sections</p>
                                    </div>
                                </div>
                                <label class="image-upload-zone w-100" for="loc-image-input">
                                    @if($isEdit && $location->image_path)
                                        <img src="{{ asset('storage/' . $location->image_path) }}" class="uz-preview" alt="{{ $location->name }}" id="locImagePreview">
                                    @else
                                        <div class="uz-icon" id="locImageIcon"><i class='bx bx-cloud-upload'></i></div>
                                    @endif
                                    <span class="uz-label">Click to upload image</span>
                                    <span class="uz-hint">JPG, PNG, WebP — Recommended 1200x600</span>
                                    <input type="file" name="image" id="loc-image-input" accept="image/*" style="display: none;"
                                           onchange="previewLocImage(this)">
                                </label>
                                @error('image')<div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Coordinates --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                        <i class='bx bx-current-location'></i>
                                    </div>
                                    <div>
                                        <h6>GPS Coordinates</h6>
                                        <p>Map position for this location</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <div class="coord-input">
                                        <div class="ci-icon" style="background: rgba(234,88,12,0.08); color: #ea580c;">
                                            <i class='bx bx-move-vertical'></i>
                                        </div>
                                        <div class="ci-body">
                                            <label for="latitude">Latitude</label>
                                            <input type="number" name="latitude" id="latitude" step="any"
                                                   value="{{ old('latitude', $location->latitude ?? '') }}"
                                                   placeholder="e.g. 25.0762">
                                        </div>
                                    </div>
                                    @error('latitude')<div class="text-danger" style="font-size: 0.78rem;">{{ $message }}</div>@enderror

                                    <div class="coord-input">
                                        <div class="ci-icon" style="background: rgba(37,99,235,0.08); color: #2563eb;">
                                            <i class='bx bx-move-horizontal'></i>
                                        </div>
                                        <div class="ci-body">
                                            <label for="longitude">Longitude</label>
                                            <input type="number" name="longitude" id="longitude" step="any"
                                                   value="{{ old('longitude', $location->longitude ?? '') }}"
                                                   placeholder="e.g. 55.1404">
                                        </div>
                                    </div>
                                    @error('longitude')<div class="text-danger" style="font-size: 0.78rem;">{{ $message }}</div>@enderror
                                </div>

                                {{-- Map hint --}}
                                <div class="mt-3 p-3" style="background: rgba(102,126,234,0.04); border-radius: 0.65rem; border: 1px solid rgba(102,126,234,0.1);">
                                    <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0; line-height: 1.6;">
                                        <i class='bx bx-info-circle me-1' style="color: var(--accent-primary);"></i>
                                        Coordinates are used for map markers and distance calculations on the public site.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ DOMAINS ═══ --}}
            @if(isset($domains) && $domains->count())
            <div class="tab-pane fade" id="loc-domains">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-globe'></i>
                            </div>
                            <div>
                                <h6>Assign to Domains</h6>
                                <p>Select which domains should display this location</p>
                            </div>
                        </div>
                        @php
                            $selectedDomains = old('domains', $selectedDomains ?? ($isEdit ? $location->domains->pluck('id')->toArray() : []));
                        @endphp
                        <div class="row g-2">
                            @foreach($domains as $d)
                            <div class="col-xl-3 col-md-4 col-6">
                                <label class="domain-check w-100">
                                    <input type="checkbox" name="domains[]" value="{{ $d->id }}" class="form-check-input"
                                           {{ in_array($d->id, $selectedDomains) ? 'checked' : '' }}>
                                    <label><i class='bx bx-globe me-1' style="color: var(--accent-primary); font-size: 0.9rem;"></i>{{ $d->name }}</label>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ═══ SEO ═══ --}}
            <div class="tab-pane fade" id="loc-seo">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(255,152,0,0.1); color: var(--accent-warning);">
                                <i class='bx bx-search-alt'></i>
                            </div>
                            <div>
                                <h6>Search Engine Optimization</h6>
                                <p>Meta tags and SEO content for this location page</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input name="meta_title" label="Meta Title" :value="old('meta_title', $location->meta_title ?? '')" placeholder="Page title for search engines" class="col-md-6" />
                            <x-form.input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $location->meta_keywords ?? '')" placeholder="dubai marina, hotels, apartments" class="col-md-6" />
                            <x-form.textarea name="meta_description" label="Meta Description" :value="old('meta_description', $location->meta_description ?? '')" rows="2" placeholder="Short description for search engine results..." class="col-12" />
                            <x-form.textarea name="seo_content" label="SEO Content" :value="old('seo_content', $location->seo_content ?? '')" rows="5" placeholder="Long-form SEO content displayed on the location page..." class="col-12" />
                            <x-form.input name="canonical_url" label="Canonical URL" :value="old('canonical_url', $location->canonical_url ?? '')" placeholder="https://example.com/locations/dubai-marina" class="col-12" help="Preferred URL for this page to avoid duplicate content issues." />
                        </div>

                        {{-- Live Preview --}}
                        <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 0.65rem; border: 1px solid var(--border-light);">
                            <p style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">
                                <i class='bx bx-show me-1'></i> Search Preview
                            </p>
                            <div>
                                <div id="seo-preview-title" style="font-size: 1.1rem; font-weight: 600; color: #1a0dab; margin-bottom: 2px;">
                                    {{ $location->meta_title ?? 'Page Title' }}
                                </div>
                                <div style="font-size: 0.8rem; color: #006621; margin-bottom: 4px;">
                                    example.com/{{ $location->slug ?? 'location-slug' }}-hotels
                                </div>
                                <div id="seo-preview-desc" style="font-size: 0.82rem; color: #545454; line-height: 1.5;">
                                    {{ $location->meta_description ?? 'Meta description will appear here...' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.locations.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Location' : 'Create Location' }}
            </button>
        </div>
    </form>

    <x-slot:scripts>
        <script>
            // Image upload preview
            function previewLocImage(input) {
                if (input.files && input.files[0]) {
                    const zone = input.closest('.image-upload-zone');
                    let img = zone.querySelector('img.uz-preview');
                    const icon = zone.querySelector('.uz-icon');

                    if (!img) {
                        img = document.createElement('img');
                        img.className = 'uz-preview';
                        img.id = 'locImagePreview';
                        if (icon) icon.replaceWith(img);
                        else zone.prepend(img);
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) { img.src = e.target.result; };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Live SEO preview
            const titleInput = document.querySelector('input[name="meta_title"]');
            const descInput = document.querySelector('textarea[name="meta_description"]');
            const previewTitle = document.getElementById('seo-preview-title');
            const previewDesc = document.getElementById('seo-preview-desc');

            if (titleInput && previewTitle) {
                titleInput.addEventListener('input', function() {
                    previewTitle.textContent = this.value || 'Page Title';
                });
            }
            if (descInput && previewDesc) {
                descInput.addEventListener('input', function() {
                    previewDesc.textContent = this.value || 'Meta description will appear here...';
                });
            }
        </script>
    </x-slot:scripts>
</x-admin-layout>
