@php $isEdit = isset($amenity) && $amenity->exists; @endphp

<x-admin-layout :title="$isEdit ? 'Edit Amenity' : 'Add Amenity'" :pageTitle="$isEdit ? 'Edit Amenity: ' . $amenity->name : 'Add Amenity'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.amenities.index') }}">Amenities</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Add' }}</li>
    </x-slot:breadcrumb>

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

        .icon-preview-box {
            display: flex; align-items: center; justify-content: center;
            width: 80px; height: 80px; border-radius: 0.85rem;
            background: linear-gradient(135deg, rgba(102,126,234,0.08), rgba(139,92,246,0.08));
            border: 2px solid rgba(102,126,234,0.15);
            transition: all 0.3s;
        }
        .icon-preview-box i {
            font-size: 2rem; color: var(--accent-primary);
            transition: all 0.3s;
        }
        .icon-preview-box:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(102,126,234,0.15);
        }

        .category-option {
            display: flex; align-items: center; gap: 10px;
            padding: 0.7rem 1rem; border-radius: 0.65rem;
            border: 1.5px solid var(--border-light); background: #fff;
            cursor: pointer; transition: all 0.2s;
        }
        .category-option:hover { border-color: var(--accent-primary); background: #fafaff; }
        .category-option:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .category-option input { display: none; }
        .category-option .co-icon {
            width: 32px; height: 32px; border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
            background: #f4f5f8; color: var(--text-muted); transition: all 0.2s;
        }
        .category-option:has(input:checked) .co-icon {
            background: rgba(102,126,234,0.12); color: var(--accent-primary);
        }
        .category-option .co-label { font-size: 0.85rem; font-weight: 600; }

        .icon-input-group {
            display: flex; align-items: center; gap: 12px;
            padding: 0.65rem 1rem; border-radius: 0.65rem;
            border: 1.5px solid var(--border-light); background: #fff;
            transition: all 0.2s;
        }
        .icon-input-group:focus-within {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(102,126,234,0.08);
        }
        .icon-input-group .iig-preview {
            width: 36px; height: 36px; border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
            background: rgba(102,126,234,0.08); color: var(--accent-primary);
            transition: all 0.3s;
        }
        .icon-input-group .iig-body { flex: 1; min-width: 0; }
        .icon-input-group .iig-body label {
            display: block; font-size: 0.68rem; font-weight: 600;
            color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 1px;
        }
        .icon-input-group .iig-body input {
            width: 100%; border: none; outline: none; background: transparent;
            font-size: 0.85rem; font-weight: 500; color: var(--text-primary);
            font-family: 'SFMono-Regular', 'Consolas', monospace;
        }
        .icon-input-group .iig-body input::placeholder { color: #c0c4cc; font-family: inherit; }

        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border-light); margin-top: 1.5rem;
        }
    </style>

    <form action="{{ $isEdit ? route('admin.amenities.update', $amenity) : route('admin.amenities.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
            {{-- Main Info --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-star'></i>
                            </div>
                            <div>
                                <h6>Amenity Details</h6>
                                <p>Name, identifier and display icon</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input name="name" label="Amenity Name" id="amenity-name" :value="old('name', $isEdit ? $amenity->name : '')" required placeholder="e.g. Free Wi-Fi" class="col-md-6" />
                            <x-form.input name="slug" label="Slug" id="amenity-slug" :value="old('slug', $isEdit ? $amenity->slug : '')" placeholder="Auto-generated from name" class="col-md-6" />

                            {{-- Icon Input --}}
                            <div class="col-12">
                                <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Icon</label>
                                <div class="icon-input-group">
                                    <div class="iig-preview">
                                        <i class='bx {{ old('icon', $isEdit ? $amenity->icon : '') }}' id="icon-preview"></i>
                                    </div>
                                    <div class="iig-body">
                                        <label for="amenity-icon">Boxicon Class</label>
                                        <input type="text" name="icon" id="amenity-icon"
                                               value="{{ old('icon', $isEdit ? $amenity->icon : '') }}"
                                               placeholder="e.g. bx-wifi, bx-swim, bx-car">
                                    </div>
                                </div>
                                @error('icon')<div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>@enderror
                                <div class="mt-2 d-flex flex-wrap gap-1">
                                    @foreach(['bx-wifi', 'bx-swim', 'bx-car', 'bx-coffee', 'bx-dumbbell', 'bx-spa', 'bx-tv', 'bx-wind', 'bx-bath', 'bx-bed', 'bx-restaurant', 'bx-bus', 'bx-briefcase', 'bx-lock-alt', 'bx-key'] as $ico)
                                    <button type="button" class="btn btn-sm px-2 py-1"
                                            style="border: 1px solid var(--border-light); border-radius: 0.4rem; font-size: 1rem; line-height: 1; background: #fff; transition: all 0.15s;"
                                            onmouseenter="this.style.borderColor='var(--accent-primary)';this.style.background='rgba(102,126,234,0.04)'"
                                            onmouseleave="this.style.borderColor='var(--border-light)';this.style.background='#fff'"
                                            onclick="document.getElementById('amenity-icon').value='{{ $ico }}';document.getElementById('icon-preview').className='bx {{ $ico }}'">
                                        <i class='bx {{ $ico }}'></i>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(255,152,0,0.1); color: var(--accent-warning);">
                                <i class='bx bx-category'></i>
                            </div>
                            <div>
                                <h6>Category</h6>
                                <p>Group this amenity under a category</p>
                            </div>
                        </div>
                        @php
                            $categories = [
                                'General' => ['icon' => 'bx-grid-alt', 'bg' => 'rgba(107,114,128,0.08)', 'color' => '#6b7280'],
                                'Recreation' => ['icon' => 'bx-swim', 'bg' => 'rgba(59,130,246,0.08)', 'color' => '#3b82f6'],
                                'Dining' => ['icon' => 'bx-restaurant', 'bg' => 'rgba(239,68,68,0.08)', 'color' => '#ef4444'],
                                'Transport' => ['icon' => 'bx-car', 'bg' => 'rgba(34,197,94,0.08)', 'color' => '#22c55e'],
                                'Business' => ['icon' => 'bx-briefcase', 'bg' => 'rgba(118,75,162,0.08)', 'color' => '#764ba2'],
                                'Room' => ['icon' => 'bx-bed', 'bg' => 'rgba(245,158,11,0.08)', 'color' => '#f59e0b'],
                            ];
                            $selectedCategory = old('category', $isEdit ? $amenity->category : 'General');
                        @endphp
                        <div class="row g-2">
                            @foreach($categories as $catName => $catMeta)
                            <div class="col-md-4 col-6">
                                <label class="category-option w-100">
                                    <input type="radio" name="category" value="{{ $catName }}"
                                           {{ $selectedCategory === $catName ? 'checked' : '' }}>
                                    <div class="co-icon" style="background: {{ $catMeta['bg'] }}; color: {{ $catMeta['color'] }};">
                                        <i class='bx {{ $catMeta['icon'] }}'></i>
                                    </div>
                                    <span class="co-label">{{ $catName }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('category')<div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Icon Preview --}}
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <div class="section-header justify-content-center" style="border: none; padding: 0; margin-bottom: 1rem;">
                            <div>
                                <h6>Preview</h6>
                                <p>How the amenity appears</p>
                            </div>
                        </div>
                        <div class="icon-preview-box mx-auto mb-3">
                            <i class='bx {{ old('icon', $isEdit ? $amenity->icon : 'bx-star') }}' id="icon-preview-large"></i>
                        </div>
                        <div id="preview-name" style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">
                            {{ old('name', $isEdit ? $amenity->name : 'Amenity Name') }}
                        </div>
                        <div id="preview-category" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                            {{ $selectedCategory }}
                        </div>
                    </div>
                </div>

                {{-- Status & Sort --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-cog'></i>
                            </div>
                            <div>
                                <h6>Settings</h6>
                                <p>Status and display order</p>
                            </div>
                        </div>

                        <div class="toggle-card mb-3">
                            <div class="toggle-label">
                                <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                <span>Active</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                       {{ old('is_active', $isEdit ? $amenity->is_active : true) ? 'checked' : '' }}>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Usage Stats (edit only) --}}
                @if($isEdit)
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                <i class='bx bx-bar-chart-alt-2'></i>
                            </div>
                            <div>
                                <h6>Usage</h6>
                                <p>Where this amenity is assigned</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="text-center flex-fill p-2" style="background: rgba(102,126,234,0.05); border-radius: 0.55rem;">
                                <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-primary);">{{ $amenity->hotels()->count() }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Hotels</div>
                            </div>
                            <div class="text-center flex-fill p-2" style="background: rgba(0,200,83,0.05); border-radius: 0.55rem;">
                                <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-success);">{{ $amenity->roomTypes()->count() }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Room Types</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.amenities.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Amenity' : 'Create Amenity' }}
            </button>
        </div>
    </form>

    <x-slot:scripts>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const nameInput = document.getElementById('amenity-name');
                const slugInput = document.getElementById('amenity-slug');
                const iconInput = document.getElementById('amenity-icon');
                const iconPreview = document.getElementById('icon-preview');
                const iconPreviewLarge = document.getElementById('icon-preview-large');
                const previewName = document.getElementById('preview-name');
                const previewCategory = document.getElementById('preview-category');
                let slugManuallyEdited = {{ $isEdit ? 'true' : 'false' }};

                slugInput.addEventListener('input', function() {
                    slugManuallyEdited = this.value.length > 0;
                });

                nameInput.addEventListener('input', function() {
                    if (!slugManuallyEdited) {
                        slugInput.value = this.value
                            .toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-')
                            .trim();
                    }
                    previewName.textContent = this.value || 'Amenity Name';
                });

                iconInput.addEventListener('input', function() {
                    iconPreview.className = 'bx ' + this.value;
                    iconPreviewLarge.className = 'bx ' + this.value;
                });

                // Category radio → update preview
                document.querySelectorAll('input[name="category"]').forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        previewCategory.textContent = this.value;
                    });
                });
            });
        </script>
    </x-slot:scripts>
</x-admin-layout>
