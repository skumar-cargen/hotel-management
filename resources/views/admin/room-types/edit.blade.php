@php
    $isEdit = isset($roomType) && $roomType->exists;
    $hotel = $isEdit ? $roomType->hotel : $hotel;
@endphp

<x-admin-layout :title="$isEdit ? 'Edit Room Type - ' . $roomType->name : 'Create Room Type'" :pageTitle="$isEdit ? 'Edit Room Type: ' . $roomType->name : 'Create Room Type'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotels</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.hotels.edit', $hotel) }}">{{ $hotel->name }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.hotels.room-types.index', $hotel) }}">Room Types</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <style>
        .rt-tabs {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 6px; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem; border: 1.5px solid var(--border-light);
        }
        .rt-tabs .rt-tab {
            display: flex; align-items: center; gap: 8px;
            padding: 0.6rem 1.1rem; border-radius: 0.65rem; border: none;
            background: transparent; font-size: 0.82rem; font-weight: 600;
            color: var(--text-secondary); cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none; position: relative; overflow: hidden;
        }
        .rt-tabs .rt-tab::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            opacity: 0; transition: opacity 0.25s; border-radius: inherit;
        }
        .rt-tabs .rt-tab > * { position: relative; z-index: 1; }
        .rt-tabs .rt-tab:hover { background: #f4f5f8; color: var(--text-primary); }
        .rt-tabs .rt-tab.active::before { opacity: 1; }
        .rt-tabs .rt-tab.active { color: #fff; box-shadow: 0 4px 14px rgba(102,126,234,0.3); }
        .rt-tabs .rt-tab .tab-icon {
            width: 28px; height: 28px; border-radius: 0.4rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; background: rgba(0,0,0,0.04); transition: all 0.25s;
        }
        .rt-tabs .rt-tab.active .tab-icon { background: rgba(255,255,255,0.2); }

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

        .amenity-check {
            display: flex; align-items: center; gap: 8px;
            padding: 0.5rem 0.75rem; border-radius: 0.6rem;
            border: 1.5px solid var(--border-light); background: #fff;
            transition: all 0.2s; cursor: pointer;
        }
        .amenity-check:hover { border-color: var(--accent-primary); background: #fafaff; }
        .amenity-check:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .amenity-check input { margin: 0; flex-shrink: 0; }
        .amenity-check label { cursor: pointer; font-size: 0.82rem; font-weight: 500; margin: 0; }
        .amenity-check i { color: var(--accent-primary); }

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
    </style>

    <form action="{{ $isEdit ? route('admin.room-types.update', $roomType) : route('admin.hotels.room-types.store', $hotel) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Tab Navigation --}}
        <div class="rt-tabs" role="tablist">
            <a class="rt-tab active" data-bs-toggle="tab" href="#rt-basic" role="tab">
                <span class="tab-icon"><i class='bx bx-bed'></i></span>
                <span>Room Details</span>
            </a>
            <a class="rt-tab" data-bs-toggle="tab" href="#rt-capacity" role="tab">
                <span class="tab-icon"><i class='bx bx-user-plus'></i></span>
                <span>Capacity & Pricing</span>
            </a>
            <a class="rt-tab" data-bs-toggle="tab" href="#rt-images" role="tab">
                <span class="tab-icon"><i class='bx bx-images'></i></span>
                <span>Images</span>
            </a>
            @if($amenities->count())
            <a class="rt-tab" data-bs-toggle="tab" href="#rt-amenities" role="tab">
                <span class="tab-icon"><i class='bx bx-spa'></i></span>
                <span>Amenities</span>
            </a>
            @endif
        </div>

        <div class="tab-content">
            {{-- ═══ ROOM DETAILS ═══ --}}
            <div class="tab-pane fade show active" id="rt-basic">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-info-circle'></i>
                            </div>
                            <div>
                                <h6>Basic Information</h6>
                                <p>Room type name, bed configuration and description</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input name="name" label="Room Type Name" :value="old('name', $roomType->name ?? '')" required placeholder="e.g. Deluxe King Room" class="col-md-8" />
                            <x-form.select name="bed_type" label="Bed Type" :options="[
                                '' => 'Select Bed Type',
                                'Single' => 'Single',
                                'Double' => 'Double',
                                'Twin' => 'Twin',
                                'King' => 'King',
                                'Queen' => 'Queen',
                            ]" :selected="old('bed_type', $roomType->bed_type ?? '')" class="col-md-4" />
                            <x-form.textarea name="description" label="Description" :value="old('description', $roomType->description ?? '')" rows="4" placeholder="Describe the room type, highlights, and what makes it special..." class="col-12" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ CAPACITY & PRICING ═══ --}}
            <div class="tab-pane fade" id="rt-capacity">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                        <i class='bx bx-group'></i>
                                    </div>
                                    <div>
                                        <h6>Capacity & Size</h6>
                                        <p>Guest limits and room dimensions</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="max_guests" label="Max Guests" type="number" :value="old('max_guests', $roomType->max_guests ?? '')" required placeholder="e.g. 3" class="col-md-6" />
                                    <x-form.input name="max_adults" label="Max Adults" type="number" :value="old('max_adults', $roomType->max_adults ?? '')" required placeholder="e.g. 2" class="col-md-6" />
                                    <x-form.input name="max_children" label="Max Children" type="number" :value="old('max_children', $roomType->max_children ?? 0)" required placeholder="e.g. 1" class="col-md-6" />
                                    <x-form.input name="room_size_sqm" label="Room Size (sqm)" type="number" step="any" :value="old('room_size_sqm', $roomType->room_size_sqm ?? '')" placeholder="e.g. 45" class="col-md-6" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                        <i class='bx bx-money'></i>
                                    </div>
                                    <div>
                                        <h6>Pricing & Availability</h6>
                                        <p>Base rate and room inventory</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <x-form.input name="base_price" label="Base Price (AED)" type="number" step="0.01" :value="old('base_price', $roomType->base_price ?? '')" required placeholder="e.g. 450.00" class="col-md-6" />
                                    <x-form.input name="total_rooms" label="Total Rooms" type="number" :value="old('total_rooms', $roomType->total_rooms ?? '')" required placeholder="e.g. 10" class="col-md-6" />
                                    <x-form.input name="sort_order" label="Sort Order" type="number" :value="old('sort_order', $roomType->sort_order ?? 0)" class="col-md-6" />
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="toggle-card w-100">
                                            <div class="toggle-label">
                                                <div class="tl-icon"><i class='bx bx-show'></i></div>
                                                <span>Active</span>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch" {{ old('is_active', $isEdit ? $roomType->is_active : true) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ IMAGES ═══ --}}
            <div class="tab-pane fade" id="rt-images">
                <style>
                    .img-gallery-card { border: none; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; }
                    .img-gallery-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1.5px solid #f1f5f9; }
                    .img-gallery-header .igh-left { display: flex; align-items: center; gap: 12px; }
                    .img-gallery-header .igh-icon {
                        width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
                        font-size: 1.2rem; background: linear-gradient(135deg, rgba(139,92,246,0.12), rgba(102,126,234,0.12)); color: #8b5cf6;
                    }
                    .img-gallery-header h6 { font-size: 0.92rem; font-weight: 700; margin: 0; }
                    .img-gallery-header .igh-count {
                        font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 20px;
                        background: linear-gradient(135deg, #8b5cf6, #667eea); color: #fff;
                    }
                    .img-gallery-grid { padding: 1.25rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }

                    .img-thumb {
                        position: relative; border-radius: 12px; overflow: hidden;
                        aspect-ratio: 4/3; cursor: pointer;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                    }
                    .img-thumb:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(102,126,234,0.18); }
                    .img-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
                    .img-thumb:hover img { transform: scale(1.08); }
                    .img-thumb .img-overlay {
                        position: absolute; inset: 0; opacity: 0;
                        background: linear-gradient(180deg, rgba(15,23,42,0) 40%, rgba(15,23,42,0.75) 100%);
                        transition: opacity 0.3s; display: flex; flex-direction: column; justify-content: flex-end; padding: 0.75rem;
                    }
                    .img-thumb:hover .img-overlay { opacity: 1; }
                    .img-thumb .img-overlay .img-alt { font-size: 0.72rem; color: rgba(255,255,255,0.85); font-weight: 500; margin-bottom: 4px; }
                    .img-thumb .img-overlay .img-actions { display: flex; gap: 6px; }
                    .img-thumb .img-overlay .img-action-btn {
                        width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer;
                        display: flex; align-items: center; justify-content: center; font-size: 0.85rem;
                        backdrop-filter: blur(8px); transition: all 0.2s;
                    }
                    .img-thumb .img-overlay .img-action-btn.btn-view { background: rgba(255,255,255,0.2); color: #fff; }
                    .img-thumb .img-overlay .img-action-btn.btn-view:hover { background: rgba(255,255,255,0.35); }
                    .img-thumb .img-overlay .img-action-btn.btn-remove { background: rgba(239,68,68,0.25); color: #fca5a5; }
                    .img-thumb .img-overlay .img-action-btn.btn-remove:hover { background: rgba(239,68,68,0.5); color: #fff; }
                    .img-empty {
                        text-align: center; padding: 3rem 1rem; color: var(--text-muted);
                    }
                    .img-empty i { font-size: 2.5rem; opacity: 0.3; margin-bottom: 0.5rem; display: block; }
                    .img-empty p { font-size: 0.82rem; margin: 0; }

                    /* Upload Dropzone */
                    .upload-dropzone-card { border: none; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; }
                    .upload-dropzone {
                        position: relative; padding: 2.5rem 1.5rem; text-align: center; cursor: pointer;
                        border: 2.5px dashed #d1d5db; border-radius: 14px; margin: 1.25rem;
                        background: linear-gradient(135deg, #fafbff 0%, #f5f3ff 100%);
                        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                    }
                    .upload-dropzone:hover, .upload-dropzone.drag-over {
                        border-color: #8b5cf6; background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
                        transform: scale(1.005);
                    }
                    .upload-dropzone.drag-over { box-shadow: 0 0 0 4px rgba(139,92,246,0.12); }
                    .upload-dropzone input[type="file"] { display: none; }
                    .upload-dropzone .dz-icon {
                        width: 64px; height: 64px; border-radius: 18px; margin: 0 auto 1rem;
                        display: flex; align-items: center; justify-content: center;
                        background: linear-gradient(135deg, #8b5cf6, #667eea);
                        box-shadow: 0 8px 24px rgba(139,92,246,0.25);
                        transition: transform 0.3s;
                    }
                    .upload-dropzone:hover .dz-icon { transform: translateY(-3px); }
                    .upload-dropzone .dz-icon i { font-size: 1.6rem; color: #fff; }
                    .upload-dropzone .dz-title { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; }
                    .upload-dropzone .dz-subtitle { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.75rem; }
                    .upload-dropzone .dz-btn {
                        display: inline-flex; align-items: center; gap: 6px; padding: 0.5rem 1.25rem;
                        border-radius: 10px; font-size: 0.8rem; font-weight: 600;
                        background: #fff; color: #8b5cf6; border: 1.5px solid rgba(139,92,246,0.25);
                        transition: all 0.2s; cursor: pointer;
                    }
                    .upload-dropzone .dz-btn:hover { background: #8b5cf6; color: #fff; border-color: #8b5cf6; }
                    .upload-dropzone .dz-formats {
                        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
                        margin-top: 1rem; flex-wrap: wrap;
                    }
                    .upload-dropzone .dz-formats .format-tag {
                        font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
                        padding: 0.2rem 0.5rem; border-radius: 6px; background: rgba(139,92,246,0.08); color: #8b5cf6;
                    }
                    .upload-dropzone .dz-formats .format-size {
                        font-size: 0.68rem; color: var(--text-muted); font-weight: 500;
                    }

                    /* Upload Preview */
                    .upload-preview-grid {
                        display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                        gap: 0.75rem; padding: 0 1.25rem 1.25rem; display: none;
                    }
                    .upload-preview-grid.has-files { display: grid; }
                    .upload-preview-item {
                        position: relative; border-radius: 10px; overflow: hidden;
                        aspect-ratio: 4/3; background: #f1f5f9;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                        animation: previewFadeIn 0.4s ease;
                    }
                    @keyframes previewFadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
                    .upload-preview-item img { width: 100%; height: 100%; object-fit: cover; }
                    .upload-preview-item .preview-remove {
                        position: absolute; top: 6px; right: 6px;
                        width: 24px; height: 24px; border-radius: 50%; border: none;
                        background: rgba(239,68,68,0.85); color: #fff; font-size: 0.7rem;
                        display: flex; align-items: center; justify-content: center;
                        cursor: pointer; transition: all 0.2s; backdrop-filter: blur(4px);
                    }
                    .upload-preview-item .preview-remove:hover { background: #ef4444; transform: scale(1.15); }
                    .upload-preview-item .preview-name {
                        position: absolute; bottom: 0; left: 0; right: 0;
                        padding: 4px 8px; font-size: 0.62rem; font-weight: 600; color: #fff;
                        background: linear-gradient(transparent, rgba(0,0,0,0.6));
                        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                    }
                </style>

                {{-- Current Images Gallery (edit only) --}}
                @if($isEdit)
                <div class="card img-gallery-card mb-3">
                    <div class="img-gallery-header">
                        <div class="igh-left">
                            <div class="igh-icon"><i class='bx bx-photo-album'></i></div>
                            <div>
                                <h6>Image Gallery</h6>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Manage room type photos</span>
                            </div>
                        </div>
                        @if($roomType->images->count())
                        <span class="igh-count">{{ $roomType->images->count() }} {{ Str::plural('image', $roomType->images->count()) }}</span>
                        @endif
                    </div>
                    @if($roomType->images->count())
                    <div class="img-gallery-grid">
                        @foreach($roomType->images as $image)
                        <div class="img-thumb" id="gallery-image-{{ $image->id }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->alt_text }}" loading="lazy">
                            <div class="img-overlay">
                                @if($image->alt_text)
                                <div class="img-alt">{{ $image->alt_text }}</div>
                                @endif
                                <div class="img-actions">
                                    <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" class="img-action-btn btn-view" title="View full size"><i class='bx bx-expand-alt'></i></a>
                                    <button type="button" class="img-action-btn btn-remove" title="Delete image"
                                            onclick="deleteImage({{ $roomType->id }}, {{ $image->id }}, this)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="img-empty" id="imgEmptyState">
                        <i class='bx bx-image-add'></i>
                        <p>No images uploaded yet. Add some below!</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Premium Upload Dropzone --}}
                <div class="card upload-dropzone-card">
                    <div class="img-gallery-header">
                        <div class="igh-left">
                            <div class="igh-icon" style="background: linear-gradient(135deg, rgba(0,200,83,0.12), rgba(16,185,129,0.12)); color: #059669;">
                                <i class='bx bx-cloud-upload'></i>
                            </div>
                            <div>
                                <h6>Upload Images</h6>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Drag & drop or click to browse</span>
                            </div>
                        </div>
                    </div>
                    <div class="upload-dropzone" id="uploadDropzone">
                        <input type="file" name="images[]" id="imageFileInput" multiple accept="image/jpeg,image/png,image/webp">
                        <div class="dz-icon"><i class='bx bx-cloud-upload'></i></div>
                        <div class="dz-title">Drop your images here</div>
                        <div class="dz-subtitle">or click to browse from your computer</div>
                        <span class="dz-btn"><i class='bx bx-folder-open'></i> Choose Files</span>
                        <div class="dz-formats">
                            <span class="format-tag">JPG</span>
                            <span class="format-tag">PNG</span>
                            <span class="format-tag">WebP</span>
                            <span class="format-size">Max 5 MB each</span>
                        </div>
                    </div>
                    <div class="upload-preview-grid" id="uploadPreviewGrid"></div>
                    @error('images')<div class="px-4 pb-3"><span class="text-danger" style="font-size:.78rem;">{{ $message }}</span></div>@enderror
                    @error('images.*')<div class="px-4 pb-3"><span class="text-danger" style="font-size:.78rem;">{{ $message }}</span></div>@enderror
                </div>
            </div>

            {{-- ═══ AMENITIES ═══ --}}
            @if($amenities->count())
            <div class="tab-pane fade" id="rt-amenities">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(29,233,182,0.1); color: var(--accent-teal);">
                                <i class='bx bx-spa'></i>
                            </div>
                            <div>
                                <h6>Room Amenities</h6>
                                <p>Select amenities available in this room type</p>
                            </div>
                        </div>
                        @php $selectedAmenities = old('amenities', $selectedAmenities ?? []); @endphp
                        @foreach($amenities as $category => $items)
                        <h6 class="mt-3 mb-2" style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em;">{{ $category }}</h6>
                        <div class="row g-2 mb-2">
                            @foreach($items as $amenity)
                            <div class="col-xl-3 col-md-4 col-6">
                                <label class="amenity-check w-100">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="form-check-input"
                                           {{ in_array($amenity->id, $selectedAmenities) ? 'checked' : '' }}>
                                    @if($amenity->icon)<i class='bx {{ $amenity->icon }}'></i>@endif
                                    <label>{{ $amenity->name }}</label>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.hotels.room-types.index', $hotel) }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Room Type' : 'Create Room Type' }}
            </button>
        </div>
    </form>

    <script>
    @if($isEdit)
    function deleteImage(roomTypeId, imageId, btn) {
        Swal.fire({
            title: 'Delete Image',
            text: 'Are you sure you want to delete this image?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5252',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: { popup: 'rounded-4', confirmButton: 'rounded-3', cancelButton: 'rounded-3' }
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var thumb = document.getElementById('gallery-image-' + imageId);
            if (thumb) { thumb.style.opacity = '0.4'; thumb.style.pointerEvents = 'none'; }

            fetch('/admin/room-types/' + roomTypeId + '/images/' + imageId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    if (thumb) {
                        thumb.style.transition = 'all 0.35s ease';
                        thumb.style.transform = 'scale(0.8)';
                        thumb.style.opacity = '0';
                        setTimeout(function() { thumb.remove(); updateGalleryCount(); }, 350);
                    }
                    Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Image has been deleted.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true, customClass: { popup: 'rounded-3' } });
                }
            }).catch(function() {
                if (thumb) { thumb.style.opacity = '1'; thumb.style.pointerEvents = ''; }
                Swal.fire({ icon: 'error', title: 'Error!', text: 'Failed to delete image. Please try again.', toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true });
            });
        });
    }

    @endif

    function updateGalleryCount() {
        var remaining = document.querySelectorAll('.img-gallery-grid .img-thumb');
        var countBadge = document.querySelector('.igh-count');
        if (remaining.length === 0) {
            var grid = document.querySelector('.img-gallery-grid');
            if (grid) grid.remove();
            var galleryCard = document.querySelector('.img-gallery-card');
            if (galleryCard && !document.getElementById('imgEmptyState')) {
                var empty = document.createElement('div');
                empty.className = 'img-empty';
                empty.id = 'imgEmptyState';
                empty.innerHTML = '<i class="bx bx-image-add"></i><p>No images uploaded yet. Add some below!</p>';
                galleryCard.appendChild(empty);
            }
            if (countBadge) countBadge.remove();
        } else if (countBadge) {
            var n = remaining.length;
            countBadge.textContent = n + ' ' + (n === 1 ? 'image' : 'images');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var dropzone = document.getElementById('uploadDropzone');
        var fileInput = document.getElementById('imageFileInput');
        var previewGrid = document.getElementById('uploadPreviewGrid');
        if (!dropzone || !fileInput) return;

        // Click to browse
        dropzone.addEventListener('click', function(e) {
            if (e.target.closest('.preview-remove')) return;
            fileInput.click();
        });

        // Drag events
        ['dragenter', 'dragover'].forEach(function(evt) {
            dropzone.addEventListener(evt, function(e) { e.preventDefault(); e.stopPropagation(); dropzone.classList.add('drag-over'); });
        });
        ['dragleave', 'drop'].forEach(function(evt) {
            dropzone.addEventListener(evt, function(e) { e.preventDefault(); e.stopPropagation(); dropzone.classList.remove('drag-over'); });
        });

        // Drop handler
        dropzone.addEventListener('drop', function(e) {
            var dt = new DataTransfer();
            // Keep existing files
            if (fileInput.files) {
                for (var i = 0; i < fileInput.files.length; i++) dt.items.add(fileInput.files[i]);
            }
            // Add dropped files
            for (var i = 0; i < e.dataTransfer.files.length; i++) {
                if (e.dataTransfer.files[i].type.startsWith('image/')) dt.items.add(e.dataTransfer.files[i]);
            }
            fileInput.files = dt.files;
            renderPreviews();
        });

        // File input change
        fileInput.addEventListener('change', renderPreviews);

        function renderPreviews() {
            previewGrid.innerHTML = '';
            var files = fileInput.files;
            if (files.length === 0) { previewGrid.classList.remove('has-files'); return; }
            previewGrid.classList.add('has-files');

            for (var i = 0; i < files.length; i++) {
                (function(index) {
                    var file = files[index];
                    var item = document.createElement('div');
                    item.className = 'upload-preview-item';

                    var img = document.createElement('img');
                    img.alt = file.name;
                    var reader = new FileReader();
                    reader.onload = function(e) { img.src = e.target.result; };
                    reader.readAsDataURL(file);

                    var nameEl = document.createElement('div');
                    nameEl.className = 'preview-name';
                    nameEl.textContent = file.name;

                    var removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'preview-remove';
                    removeBtn.innerHTML = '<i class="bx bx-x"></i>';
                    removeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        var dt = new DataTransfer();
                        for (var j = 0; j < fileInput.files.length; j++) {
                            if (j !== index) dt.items.add(fileInput.files[j]);
                        }
                        fileInput.files = dt.files;
                        renderPreviews();
                    });

                    item.appendChild(img);
                    item.appendChild(nameEl);
                    item.appendChild(removeBtn);
                    previewGrid.appendChild(item);
                })(i);
            }
        }
    });
    </script>
</x-admin-layout>
