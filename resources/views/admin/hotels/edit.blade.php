@php $isEdit = isset($hotel) && $hotel->exists; @endphp

<x-admin-layout :title="$isEdit ? 'Edit Hotel' : 'Create Hotel'" :pageTitle="$isEdit ? 'Edit Hotel: ' . $hotel->name : 'Create Hotel'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotels</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <style>
        /* ── Premium Tabs ─────────────────────────────── */
        .hotel-tabs {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 6px;
            background: #fff;
            border-radius: 0.85rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            border: 1.5px solid var(--border-light);
        }
        .hotel-tabs .ht-tab {
            display: flex; align-items: center; gap: 8px;
            padding: 0.6rem 1.1rem;
            border-radius: 0.65rem;
            border: none;
            background: transparent;
            font-size: 0.82rem; font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        .hotel-tabs .ht-tab::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            opacity: 0; transition: opacity 0.25s;
            border-radius: inherit;
        }
        .hotel-tabs .ht-tab > * { position: relative; z-index: 1; }
        .hotel-tabs .ht-tab:hover { background: #f4f5f8; color: var(--text-primary); }
        .hotel-tabs .ht-tab.active::before { opacity: 1; }
        .hotel-tabs .ht-tab.active {
            color: #fff;
            box-shadow: 0 4px 14px rgba(102,126,234,0.3);
        }
        .hotel-tabs .ht-tab .tab-icon {
            width: 28px; height: 28px;
            border-radius: 0.4rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            background: rgba(0,0,0,0.04);
            transition: all 0.25s;
        }
        .hotel-tabs .ht-tab.active .tab-icon { background: rgba(255,255,255,0.2); }
        .hotel-tabs .ht-tab:hover .tab-icon { background: rgba(0,0,0,0.06); }

        /* ── Section Headers ──────────────────────────── */
        .section-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1.5px solid var(--border-light);
        }
        .section-header .sh-icon {
            width: 34px; height: 34px;
            border-radius: 0.55rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .section-header h6 {
            font-size: 0.9rem; font-weight: 700;
            color: var(--text-primary); margin: 0;
        }
        .section-header p {
            font-size: 0.75rem; color: var(--text-muted); margin: 0;
        }

        /* ── Amenity Chips ────────────────────────────── */
        .amenity-check {
            display: flex; align-items: center; gap: 8px;
            padding: 0.5rem 0.75rem;
            border-radius: 0.6rem;
            border: 1.5px solid var(--border-light);
            background: #fff;
            transition: all 0.2s;
            cursor: pointer;
        }
        .amenity-check:hover { border-color: var(--accent-primary); background: #fafaff; }
        .amenity-check:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .amenity-check input { margin: 0; flex-shrink: 0; }
        .amenity-check label { cursor: pointer; font-size: 0.82rem; font-weight: 500; margin: 0; }
        .amenity-check i { color: var(--accent-primary); }

        /* ── Domain Chips ─────────────────────────────── */
        .domain-check {
            display: flex; align-items: center; gap: 8px;
            padding: 0.55rem 0.85rem;
            border-radius: 0.6rem;
            border: 1.5px solid var(--border-light);
            background: #fff;
            transition: all 0.2s;
            cursor: pointer;
        }
        .domain-check:hover { border-color: var(--accent-primary); }
        .domain-check:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .domain-check input { margin: 0; flex-shrink: 0; }
        .domain-check label { cursor: pointer; font-size: 0.82rem; font-weight: 500; margin: 0; }

        /* ── Toggle Switches ──────────────────────────── */
        .toggle-card {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.85rem 1rem;
            border-radius: 0.65rem;
            border: 1.5px solid var(--border-light);
            background: #fff;
            transition: all 0.2s;
        }
        .toggle-card:has(input:checked) {
            border-color: var(--accent-success);
            background: rgba(0,200,83,0.04);
        }
        .toggle-card .toggle-label {
            display: flex; align-items: center; gap: 10px;
        }
        .toggle-card .toggle-label .tl-icon {
            width: 32px; height: 32px;
            border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            background: #f4f5f8;
            color: var(--text-muted);
            transition: all 0.2s;
        }
        .toggle-card:has(input:checked) .tl-icon {
            background: rgba(0,200,83,0.12);
            color: var(--accent-success);
        }
        .toggle-card .toggle-label span { font-size: 0.85rem; font-weight: 600; }

        /* ── Save Bar ─────────────────────────────────── */
        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem;
            background: #fff;
            border-radius: 0.85rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border-light);
            margin-top: 1.5rem;
        }

        /* ── Time inputs click-to-open ────────────────── */
        input[type="time"] { cursor: pointer; }
    </style>

    <form action="{{ $isEdit ? route('admin.hotels.update', $hotel) : route('admin.hotels.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Premium Tab Navigation --}}
        <div class="hotel-tabs" role="tablist">
            <a class="ht-tab active" data-bs-toggle="tab" href="#basic" role="tab">
                <span class="tab-icon"><i class='bx bx-hotel'></i></span>
                <span>Basic Info</span>
            </a>
            @if($isEdit)
            <a class="ht-tab" data-bs-toggle="tab" href="#images-tab" role="tab">
                <span class="tab-icon"><i class='bx bx-images'></i></span>
                <span>Images</span>
            </a>
            @endif
            <a class="ht-tab" data-bs-toggle="tab" href="#amenities-tab" role="tab">
                <span class="tab-icon"><i class='bx bx-spa'></i></span>
                <span>Amenities</span>
            </a>
            <a class="ht-tab" data-bs-toggle="tab" href="#policies" role="tab">
                <span class="tab-icon"><i class='bx bx-shield-quarter'></i></span>
                <span>Policies</span>
            </a>
            <a class="ht-tab" data-bs-toggle="tab" href="#seo-tab" role="tab">
                <span class="tab-icon"><i class='bx bx-search-alt-2'></i></span>
                <span>SEO</span>
            </a>
            <a class="ht-tab" data-bs-toggle="tab" href="#domains-tab" role="tab">
                <span class="tab-icon"><i class='bx bx-globe'></i></span>
                <span>Domains</span>
            </a>
        </div>

        <div class="tab-content">
            {{-- ═══ BASIC INFO ═══ --}}
            <div class="tab-pane fade show active" id="basic">
                <div class="card mb-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-info-circle'></i>
                            </div>
                            <div>
                                <h6>General Information</h6>
                                <p>Hotel name, location and basic details</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input name="name" label="Hotel Name" :value="old('name', $hotel->name ?? '')" required class="col-md-8" />
                            <x-form.select name="star_rating" label="Star Rating" :options="[1=>'1 Star',2=>'2 Stars',3=>'3 Stars',4=>'4 Stars',5=>'5 Stars']" :selected="old('star_rating', $hotel->star_rating ?? 3)" required class="col-md-4" />
                            <x-form.select2-ajax name="location_id" label="Location" :url="route('admin.api.search.locations')" :selected="$hotel->location_id" :selectedText="$hotel->location?->name" required class="col-md-6" />
                            <x-form.input name="address" label="Address" :value="old('address', $hotel->address ?? '')" class="col-md-6" />
                            <x-form.textarea name="description" label="Description" :value="old('description', $hotel->description ?? '')" rows="5" class="col-12" />
                            <x-form.input name="short_description" label="Short Description" :value="old('short_description', $hotel->short_description ?? '')" class="col-12" />
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                        <i class='bx bx-phone'></i>
                                    </div>
                                    <div>
                                        <h6>Contact Details</h6>
                                        <p>Phone, email and website</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="phone" label="Phone Number" type="tel" :value="old('phone', $hotel->phone ?? '')" placeholder="+971 4 123 4567" class="col-md-6" />
                                    <x-form.input name="email" label="Email" type="email" :value="old('email', $hotel->email ?? '')" class="col-md-6" />
                                    <x-form.input name="website" label="Website" type="url" :value="old('website', $hotel->website ?? '')" class="col-12" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(255,171,0,0.1); color: var(--accent-warning);">
                                        <i class='bx bx-time-five'></i>
                                    </div>
                                    <div>
                                        <h6>Timings & Location</h6>
                                        <p>Check-in/out times and coordinates</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="check_in_time" label="Check-in Time" type="time" :value="old('check_in_time', $hotel->check_in_time ?? '')" class="col-md-6" />
                                    <x-form.input name="check_out_time" label="Check-out Time" type="time" :value="old('check_out_time', $hotel->check_out_time ?? '')" class="col-md-6" />
                                    <x-form.input name="latitude" label="Latitude" type="number" step="any" :value="old('latitude', $hotel->latitude ?? '')" class="col-md-6" />
                                    <x-form.input name="longitude" label="Longitude" type="number" step="any" :value="old('longitude', $hotel->longitude ?? '')" class="col-md-6" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-toggle-right'></i>
                            </div>
                            <div>
                                <h6>Status</h6>
                                <p>Visibility and feature flags</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="toggle-card">
                                    <div class="toggle-label">
                                        <div class="tl-icon"><i class='bx bx-show'></i></div>
                                        <span>Active</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch" {{ old('is_active', $isEdit ? $hotel->is_active : true) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="toggle-card">
                                    <div class="toggle-label">
                                        <div class="tl-icon"><i class='bx bx-star'></i></div>
                                        <span>Featured</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" role="switch" {{ old('is_featured', $isEdit ? $hotel->is_featured : false) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ IMAGES ═══ --}}
            @if($isEdit)
            @include('admin.hotels._images-tab', ['hotel' => $hotel, 'imageCategories' => $imageCategories])
            @endif

            {{-- ═══ AMENITIES ═══ --}}
            <div class="tab-pane fade" id="amenities-tab">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(29,233,182,0.1); color: var(--accent-teal);">
                                <i class='bx bx-spa'></i>
                            </div>
                            <div>
                                <h6>Hotel Amenities</h6>
                                <p>Select all amenities available at this property</p>
                            </div>
                        </div>
                        @php $selectedAmenities = $selectedAmenities ?? ($isEdit ? $hotel->amenities->pluck('id')->toArray() : []); @endphp
                        @foreach($amenities as $category => $items)
                        <h6 class="mt-3 mb-2" style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em;">{{ $category }}</h6>
                        <div class="row g-2 mb-2">
                            @foreach($items as $amenity)
                            <div class="col-xl-3 col-md-4 col-6">
                                <label class="amenity-check w-100">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="form-check-input"
                                           {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }}>
                                    <i class='bx {{ $amenity->icon }}'></i>
                                    <label>{{ $amenity->name }}</label>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ═══ POLICIES ═══ --}}
            <div class="tab-pane fade" id="policies">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(255,82,82,0.1); color: var(--accent-danger);">
                                <i class='bx bx-shield-quarter'></i>
                            </div>
                            <div>
                                <h6>Policies & Features</h6>
                                <p>Cancellation policy and property features</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.textarea name="cancellation_policy" label="Cancellation Policy" :value="old('cancellation_policy', $hotel->cancellation_policy ?? '')" rows="4" class="col-12" />
                            <div class="col-md-4">
                                <div class="toggle-card">
                                    <div class="toggle-label">
                                        <div class="tl-icon"><i class='bx bx-water'></i></div>
                                        <span>Beach Access</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="checkbox" name="is_beach_access" value="1" class="form-check-input" role="switch" {{ old('is_beach_access', $isEdit ? $hotel->is_beach_access : false) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="toggle-card">
                                    <div class="toggle-label">
                                        <div class="tl-icon"><i class='bx bx-group'></i></div>
                                        <span>Family Friendly</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="checkbox" name="is_family_friendly" value="1" class="form-check-input" role="switch" {{ old('is_family_friendly', $isEdit ? $hotel->is_family_friendly : false) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ SEO ═══ --}}
            <div class="tab-pane fade" id="seo-tab">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(255,109,0,0.1); color: var(--accent-orange);">
                                <i class='bx bx-search-alt-2'></i>
                            </div>
                            <div>
                                <h6>SEO Settings</h6>
                                <p>Optimize for search engines</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input name="meta_title" label="Meta Title" :value="old('meta_title', $hotel->meta_title ?? '')" class="col-md-6" />
                            <x-form.input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $hotel->meta_keywords ?? '')" class="col-md-6" />
                            <x-form.textarea name="meta_description" label="Meta Description" :value="old('meta_description', $hotel->meta_description ?? '')" rows="3" class="col-12" />
                            <x-form.input name="canonical_url" label="Canonical URL" :value="old('canonical_url', $hotel->canonical_url ?? '')" placeholder="https://example.com/hotels/hotel-name" class="col-12" help="Preferred URL for this page to avoid duplicate content issues." />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ DOMAINS ═══ --}}
            <div class="tab-pane fade" id="domains-tab">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(118,75,162,0.1); color: var(--accent-secondary);">
                                <i class='bx bx-globe'></i>
                            </div>
                            <div>
                                <h6>Assign to Domains</h6>
                                <p>Select which domains will display this hotel</p>
                            </div>
                        </div>
                        @php $selectedDomains = $selectedDomains ?? ($isEdit ? $hotel->domains->pluck('id')->toArray() : []); @endphp
                        <div class="row g-2">
                            @foreach($domains as $d)
                            <div class="col-xl-3 col-md-4 col-6">
                                <label class="domain-check w-100">
                                    <input type="checkbox" name="domains[]" value="{{ $d->id }}" class="form-check-input"
                                           {{ in_array($d->id, $selectedDomains) ? 'checked' : '' }}>
                                    <label>{{ $d->name }}</label>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.hotels.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Hotel' : 'Create Hotel' }}
            </button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ── Phone: block alphabets, allow only digits + - ( ) space ──
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('keydown', function(e) {
                // Allow: backspace, delete, tab, escape, enter, arrows, home, end
                if ([8, 9, 13, 27, 46, 35, 36, 37, 38, 39, 40].includes(e.keyCode)) return;
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode)) return;
                // Allow: + - ( ) space and digits
                const allowed = '0123456789+-() ';
                if (!allowed.includes(e.key)) e.preventDefault();
            });
            phoneInput.addEventListener('paste', function(e) {
                setTimeout(() => {
                    this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
                }, 0);
            });
        }

        // ── Time inputs: click anywhere opens picker ──
        document.querySelectorAll('input[type="time"]').forEach(input => {
            input.addEventListener('click', function() {
                if (this.showPicker) this.showPicker();
            });
        });
    });
    </script>
</x-admin-layout>
