<div class="tab-pane fade" id="images-tab">
    <style>
        /* ── Gallery Styles ─────────────────────────── */
        .gallery-drop-zone {
            position: relative;
            border: 2px dashed #d0d5e0;
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2f8 100%);
        }
        .gallery-drop-zone:hover {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, #eef1ff 0%, #e8ecff 100%);
        }
        .gallery-drop-zone.drag-over {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, #e8ecff 0%, #dde3ff 100%);
            transform: scale(1.01);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
        }
        .gallery-drop-zone .drop-icon {
            width: 64px; height: 64px;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 8px 24px rgba(102,126,234,0.3);
            transition: transform 0.3s;
        }
        .gallery-drop-zone:hover .drop-icon { transform: translateY(-3px) scale(1.05); }
        .gallery-drop-zone.drag-over .drop-icon { transform: translateY(-5px) scale(1.1); }

        .gallery-upload-bar {
            height: 4px; border-radius: 2px;
            background: #e8ecff; overflow: hidden;
        }
        .gallery-upload-bar .bar-fill {
            height: 100%; border-radius: 2px;
            background: linear-gradient(90deg, var(--accent-primary), #8b5cf6, var(--grad-end));
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            transition: width 0.3s;
        }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

        /* Filter chips */
        .gallery-filter-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .gallery-chip {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.45rem 0.9rem 0.45rem 0.55rem;
            border-radius: 0.65rem; border: none;
            background: #f4f5f8;
            font-size: 0.78rem; font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap; position: relative; overflow: hidden;
        }
        .gallery-chip::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            opacity: 0; transition: opacity 0.25s;
        }
        .gallery-chip > * { position: relative; z-index: 1; }
        .gallery-chip:hover { background: #eaecf3; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .gallery-chip.active::before { opacity: 1; }
        .gallery-chip.active { color: #fff; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(102,126,234,0.35); }
        .gallery-chip .chip-icon {
            width: 26px; height: 26px; border-radius: 0.4rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; flex-shrink: 0; transition: all 0.25s;
            background: rgba(0,0,0,0.05); color: var(--text-muted);
        }
        .gallery-chip:hover .chip-icon { background: rgba(0,0,0,0.08); }
        .gallery-chip.active .chip-icon { background: rgba(255,255,255,0.2); color: #fff; }
        .gallery-chip .chip-count {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 22px; height: 22px; padding: 0 5px;
            border-radius: 0.4rem; font-size: 0.65rem; font-weight: 800;
            background: rgba(0,0,0,0.06); color: var(--text-muted); transition: all 0.25s;
        }
        .gallery-chip.active .chip-count { background: rgba(255,255,255,0.2); color: #fff; }

        /* Image cards */
        .gallery-card {
            border-radius: 0.85rem; overflow: hidden;
            background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .gallery-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(102,126,234,0.15); }
        .gallery-card .gc-thumb { position: relative; padding-top: 70%; overflow: hidden; }
        .gallery-card .gc-thumb img {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover; transition: transform 0.4s;
        }
        .gallery-card:hover .gc-thumb img { transform: scale(1.06); }
        .gallery-card .gc-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.5) 0%, transparent 35%, transparent 65%, rgba(0,0,0,0.55) 100%);
            opacity: 0; transition: opacity 0.25s;
            display: flex; flex-direction: column; justify-content: space-between; padding: 0.65rem;
        }
        .gallery-card:hover .gc-overlay { opacity: 1; }
        .gallery-card .gc-star-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 0.3rem 0.6rem; border-radius: 2rem;
            font-size: 0.7rem; font-weight: 700; border: none; cursor: pointer;
            transition: all 0.2s; color: #fff;
            background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);
        }
        .gallery-card .gc-star-btn:hover { background: rgba(255,255,255,0.35); transform: scale(1.05); }
        .gallery-card .gc-star-btn.is-primary {
            background: linear-gradient(135deg, #ffab00, #ff6d00);
            color: #fff; box-shadow: 0 2px 8px rgba(255,171,0,0.4);
        }
        .gallery-card .gc-del-btn {
            width: 30px; height: 30px; border-radius: 50%; border: none;
            background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);
            color: #fff; display: inline-flex; align-items: center; justify-content: center;
            font-size: 1rem; cursor: pointer; transition: all 0.2s;
        }
        .gallery-card .gc-del-btn:hover { background: var(--accent-danger); transform: scale(1.1); }
        .gallery-card .gc-cat-badge {
            display: inline-block; padding: 0.22rem 0.55rem; border-radius: 2rem;
            font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
            background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); color: #fff;
        }
        .gallery-card .gc-primary-ribbon {
            position: absolute; top: 10px; left: -28px;
            width: 100px; text-align: center; padding: 3px 0;
            font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
            background: linear-gradient(135deg, #ffab00, #ff6d00);
            color: #fff; transform: rotate(-45deg);
            box-shadow: 0 2px 6px rgba(255,171,0,0.4); z-index: 2;
        }
        .gallery-card .gc-footer { padding: 0.6rem 0.7rem; border-top: 1px solid #f1f3f6; }
        .gallery-card .gc-footer input, .gallery-card .gc-footer select {
            border: 1.5px solid #eef0f4; border-radius: 0.5rem;
            font-size: 0.76rem; padding: 0.3rem 0.5rem; transition: border-color 0.2s;
        }
        .gallery-card .gc-footer input:focus, .gallery-card .gc-footer select:focus {
            border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(102,126,234,0.08); outline: none;
        }
        .gallery-empty { padding: 4rem 2rem; text-align: center; }
        .gallery-empty .empty-icon {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #f0f2f8, #e8ecff);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #b5bac8; margin-bottom: 1rem;
        }
        @keyframes cardSlideIn {
            from { opacity: 0; transform: translateY(16px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .image-card { animation: cardSlideIn 0.35s ease forwards; }
    </style>

    {{-- Upload Zone --}}
    <div class="card mb-3">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.78rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                        <i class='bx bx-category me-1'></i>Upload Category
                    </label>
                    <select id="upload-category" class="form-select">
                        @foreach($imageCategories as $slug => $label)
                            <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 text-end">
                    <label for="file-input" class="btn btn-primary px-4">
                        <i class='bx bx-upload me-1'></i> Choose Files
                    </label>
                    <input type="file" id="file-input" class="d-none" multiple accept="image/*">
                </div>
            </div>
            <div id="drop-zone" class="gallery-drop-zone">
                <div class="drop-icon"><i class='bx bx-cloud-upload'></i></div>
                <h6 style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem;">Drop images here</h6>
                <p style="font-size: 0.82rem; color: var(--text-muted); margin: 0;">or click to browse. JPG, PNG, WebP up to 5 MB each.</p>
            </div>
            <div id="upload-progress" class="mt-3 d-none">
                <div class="d-flex justify-content-between mb-1">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--accent-primary);">Uploading...</span>
                    <span id="upload-pct" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-primary);">0%</span>
                </div>
                <div class="gallery-upload-bar"><div class="bar-fill" style="width: 0%"></div></div>
            </div>
        </div>
    </div>

    {{-- Stats & Filters --}}
    @php
        $chipIcons = [
            'all' => 'bx-grid-alt', 'exterior' => 'bx-building-house', 'lobby' => 'bx-door-open',
            'rooms' => 'bx-bed', 'bathroom' => 'bx-bath', 'pool' => 'bx-water',
            'restaurant' => 'bx-restaurant', 'gym' => 'bx-dumbbell', 'spa' => 'bx-spa',
            'meeting' => 'bx-chalkboard', 'general' => 'bx-image-alt',
        ];
    @endphp
    <div class="card mb-3">
        <div class="card-body py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; border-radius: 0.6rem; background: linear-gradient(135deg, var(--accent-primary), #8b5cf6); display: flex; align-items: center; justify-content: center;">
                        <i class='bx bx-images text-white' style="font-size: 1.1rem;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Gallery</div>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--text-primary); line-height: 1;" id="total-count-label">{{ $hotel->images->count() }} images</div>
                    </div>
                </div>
                <div class="gallery-filter-chips" id="category-filters">
                    <button type="button" class="gallery-chip active" data-category="all">
                        <span class="chip-icon"><i class='bx bx-grid-alt'></i></span>
                        <span>All</span>
                        <span class="chip-count" id="count-all">{{ $hotel->images->count() }}</span>
                    </button>
                    @foreach($imageCategories as $slug => $label)
                        @php $catCount = $hotel->images->where('category', $slug)->count(); @endphp
                        <button type="button" class="gallery-chip" data-category="{{ $slug }}" {!! $catCount === 0 ? 'style="display:none;"' : '' !!}>
                            <span class="chip-icon"><i class='bx {{ $chipIcons[$slug] ?? 'bx-image-alt' }}'></i></span>
                            <span>{{ $label }}</span>
                            <span class="chip-count" id="count-{{ $slug }}">{{ $catCount }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Image Grid --}}
    <div class="row g-3" id="image-grid">
        @foreach($hotel->images->sortByDesc('is_primary') as $img)
        <div class="col-xl-3 col-lg-4 col-md-6 image-card" data-id="{{ $img->id }}" data-category="{{ $img->category }}">
            <div class="gallery-card">
                <div class="gc-thumb">
                    @if($img->is_primary)<div class="gc-primary-ribbon">Primary</div>@endif
                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $img->alt_text }}" loading="lazy">
                    <div class="gc-overlay">
                        <div class="d-flex justify-content-between align-items-start">
                            <button type="button" class="gc-star-btn primary-badge {{ $img->is_primary ? 'is-primary' : '' }}"
                                    onclick="setPrimary({{ $img->id }})">
                                <i class='bx bxs-star'></i> {{ $img->is_primary ? 'Primary' : 'Set Primary' }}
                            </button>
                            <button type="button" class="gc-del-btn" onclick="deleteImage({{ $img->id }})">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                        <div><span class="gc-cat-badge category-badge">{{ $imageCategories[$img->category] ?? $img->category }}</span></div>
                    </div>
                </div>
                <div class="gc-footer">
                    <div class="d-flex gap-1">
                        <input type="text" class="form-control form-control-sm flex-grow-1" placeholder="Alt text..."
                               value="{{ $img->alt_text }}" data-field="alt_text" onchange="updateImage({{ $img->id }}, this)">
                        <select class="form-select form-select-sm" style="width: 120px; flex-shrink: 0;"
                                data-field="category" onchange="updateImage({{ $img->id }}, this)">
                            @foreach($imageCategories as $slug => $label)
                                <option value="{{ $slug }}" {{ $img->category === $slug ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($hotel->images->isEmpty())
    <div id="empty-state" class="gallery-empty">
        <div class="empty-icon"><i class='bx bx-image-alt'></i></div>
        <h6 style="font-weight: 700; color: var(--text-secondary);">No images yet</h6>
        <p style="font-size: 0.85rem; color: var(--text-muted); max-width: 320px; margin: 0 auto;">Drag & drop photos above or click "Choose Files" to start building your gallery.</p>
    </div>
    @endif
</div>

<script>
(function() {
    const hotelId = {{ $hotel->id }};
    const csrfToken = '{{ csrf_token() }}';
    const categories = @json($imageCategories);
    const baseUrl = '{{ url("admin/hotels") }}/' + hotelId + '/images';
    let activeFilter = 'all';

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');

    ['dragenter', 'dragover'].forEach(e => dropZone.addEventListener(e, function(ev) { ev.preventDefault(); dropZone.classList.add('drag-over'); }));
    ['dragleave', 'drop'].forEach(e => dropZone.addEventListener(e, function(ev) { ev.preventDefault(); dropZone.classList.remove('drag-over'); }));
    dropZone.addEventListener('drop', function(ev) { ev.preventDefault(); if (ev.dataTransfer.files.length) uploadFiles(ev.dataTransfer.files); });
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', function() { if (this.files.length) uploadFiles(this.files); this.value = ''; });

    function uploadFiles(files) {
        const category = document.getElementById('upload-category').value;
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('category', category);
        for (let i = 0; i < files.length; i++) formData.append('images[]', files[i]);

        const progress = document.getElementById('upload-progress');
        const bar = progress.querySelector('.bar-fill');
        const pct = document.getElementById('upload-pct');
        progress.classList.remove('d-none');
        bar.style.width = '0%'; pct.textContent = '0%';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', baseUrl);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) { const p = Math.round((e.loaded / e.total) * 100); bar.style.width = p + '%'; pct.textContent = p + '%'; }
        };
        xhr.onload = function() {
            progress.classList.add('d-none');
            if (xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                if (data.success && data.images) {
                    const empty = document.getElementById('empty-state'); if (empty) empty.remove();
                    data.images.forEach(function(img) { appendImageCard(img); });
                    updateCounts();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.images.length + ' image(s) uploaded', showConfirmButton: false, timer: 2000, customClass: { popup: 'rounded-3' } });
                }
            } else { Swal.fire({ icon: 'error', title: 'Upload Failed', text: 'Check file size and format.', customClass: { popup: 'rounded-4' } }); }
        };
        xhr.onerror = function() { progress.classList.add('d-none'); Swal.fire({ icon: 'error', title: 'Upload Failed', text: 'Network error.', customClass: { popup: 'rounded-4' } }); };
        xhr.send(formData);
    }

    document.querySelectorAll('.gallery-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            activeFilter = this.dataset.category;
            document.querySelectorAll('.gallery-chip').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterImages();
        });
    });

    function filterImages() {
        document.querySelectorAll('.image-card').forEach(card => {
            card.style.display = (activeFilter === 'all' || card.dataset.category === activeFilter) ? '' : 'none';
        });
    }

    window.setPrimary = function(imageId) {
        fetch(baseUrl + '/' + imageId + '/primary', {
            method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        }).then(r => r.json()).then(data => {
            if (data.success) {
                document.querySelectorAll('.primary-badge').forEach(b => { b.classList.remove('is-primary'); b.innerHTML = "<i class='bx bxs-star'></i> Set Primary"; });
                document.querySelectorAll('.gc-primary-ribbon').forEach(r => r.remove());
                const card = document.querySelector(`.image-card[data-id="${imageId}"]`);
                if (card) {
                    const badge = card.querySelector('.primary-badge'); badge.classList.add('is-primary'); badge.innerHTML = "<i class='bx bxs-star'></i> Primary";
                    card.querySelector('.gc-thumb').insertAdjacentHTML('afterbegin', '<div class="gc-primary-ribbon">Primary</div>');
                }
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Primary image set', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3' } });
            }
        }).catch(() => Swal.fire('Error', 'Failed to set primary image.', 'error'));
    };

    window.deleteImage = function(imageId) {
        Swal.fire({
            title: 'Delete Image?', text: 'This action cannot be undone.', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#ff5252', cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> Delete', cancelButtonText: 'Cancel',
            reverseButtons: true, customClass: { popup: 'rounded-4', confirmButton: 'rounded-3', cancelButton: 'rounded-3' }
        }).then(result => {
            if (!result.isConfirmed) return;
            fetch(baseUrl + '/' + imageId, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    const card = document.querySelector(`.image-card[data-id="${imageId}"]`);
                    if (card) { card.style.transition = 'all 0.3s'; card.style.opacity = '0'; card.style.transform = 'scale(0.8)'; setTimeout(() => { card.remove(); updateCounts(); }, 300); }
                    if (document.querySelectorAll('.image-card').length <= 1) {
                        setTimeout(() => { if (!document.querySelectorAll('.image-card').length) { document.getElementById('image-grid').insertAdjacentHTML('afterend', '<div id="empty-state" class="gallery-empty"><div class="empty-icon"><i class="bx bx-image-alt"></i></div><h6 style="font-weight:700;color:var(--text-secondary);">No images yet</h6></div>'); } }, 350);
                    }
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Image deleted', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3' } });
                }
            }).catch(() => Swal.fire('Error', 'Failed to delete image.', 'error'));
        });
    };

    window.updateImage = function(imageId, el) {
        const field = el.dataset.field, value = el.value, body = {}; body[field] = value;
        fetch(baseUrl + '/' + imageId, {
            method: 'PUT', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body),
        }).then(r => r.json()).then(data => {
            if (data.success) {
                if (field === 'category') {
                    const card = document.querySelector(`.image-card[data-id="${imageId}"]`);
                    card.dataset.category = value; card.querySelector('.category-badge').textContent = categories[value] || value;
                    updateCounts(); filterImages();
                }
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Updated', showConfirmButton: false, timer: 1000, customClass: { popup: 'rounded-3' } });
            }
        }).catch(() => Swal.fire('Error', 'Failed to update image.', 'error'));
    };

    function appendImageCard(img) {
        const grid = document.getElementById('image-grid');
        const catOptions = Object.keys(categories).map(slug => `<option value="${slug}" ${img.category === slug ? 'selected' : ''}>${categories[slug]}</option>`).join('');
        const isPrimary = img.is_primary;
        const html = `<div class="col-xl-3 col-lg-4 col-md-6 image-card" data-id="${img.id}" data-category="${img.category}">
            <div class="gallery-card"><div class="gc-thumb">
                ${isPrimary ? '<div class="gc-primary-ribbon">Primary</div>' : ''}
                <img src="${img.url}" alt="${img.alt_text || ''}" loading="lazy">
                <div class="gc-overlay">
                    <div class="d-flex justify-content-between align-items-start">
                        <button type="button" class="gc-star-btn primary-badge ${isPrimary ? 'is-primary' : ''}" onclick="setPrimary(${img.id})"><i class='bx bxs-star'></i> ${isPrimary ? 'Primary' : 'Set Primary'}</button>
                        <button type="button" class="gc-del-btn" onclick="deleteImage(${img.id})"><i class='bx bx-trash'></i></button>
                    </div>
                    <div><span class="gc-cat-badge category-badge">${img.category_label}</span></div>
                </div>
            </div>
            <div class="gc-footer"><div class="d-flex gap-1">
                <input type="text" class="form-control form-control-sm flex-grow-1" placeholder="Alt text..." value="${img.alt_text || ''}" data-field="alt_text" onchange="updateImage(${img.id}, this)">
                <select class="form-select form-select-sm" style="width:120px;flex-shrink:0;" data-field="category" onchange="updateImage(${img.id}, this)">${catOptions}</select>
            </div></div></div></div>`;
        grid.insertAdjacentHTML('beforeend', html); filterImages();
    }

    function updateCounts() {
        const cards = document.querySelectorAll('.image-card');
        const total = cards.length;
        const el = document.getElementById('count-all'); if (el) el.textContent = total;
        const label = document.getElementById('total-count-label'); if (label) label.textContent = total + ' image' + (total !== 1 ? 's' : '');
        Object.keys(categories).forEach(cat => {
            const count = document.querySelectorAll(`.image-card[data-category="${cat}"]`).length;
            const badge = document.getElementById('count-' + cat); if (badge) badge.textContent = count;
            const chip = badge ? badge.closest('.gallery-chip') : null; if (chip) chip.style.display = count > 0 ? '' : 'none';
        });
    }
})();
</script>
