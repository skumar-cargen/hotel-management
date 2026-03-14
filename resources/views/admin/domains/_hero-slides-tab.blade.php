<div class="col-12">
    <div class="card">
        <div class="card-body p-4">
            <style>
                .hero-drop-zone {
                    position: relative; border: 2px dashed #d0d5e0; border-radius: 1rem;
                    padding: 2rem; text-align: center; cursor: pointer;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    background: linear-gradient(135deg, #f8f9ff 0%, #f0f2f8 100%);
                }
                .hero-drop-zone:hover { border-color: var(--accent-primary); background: linear-gradient(135deg, #eef1ff 0%, #e8ecff 100%); }
                .hero-drop-zone.drag-over { border-color: var(--accent-primary); background: linear-gradient(135deg, #e8ecff 0%, #dde3ff 100%); transform: scale(1.005); box-shadow: 0 0 0 4px rgba(102,126,234,0.1); }
                .hero-drop-zone .drop-icon {
                    width: 52px; height: 52px; border-radius: 0.85rem;
                    background: linear-gradient(135deg, #ec4899, #8b5cf6); color: #fff;
                    display: inline-flex; align-items: center; justify-content: center;
                    font-size: 1.5rem; margin-bottom: 0.5rem;
                    box-shadow: 0 6px 20px rgba(236,72,153,0.3); transition: transform 0.3s;
                }
                .hero-drop-zone:hover .drop-icon { transform: translateY(-2px) scale(1.05); }

                .hero-upload-bar { height: 4px; border-radius: 2px; background: #e8ecff; overflow: hidden; }
                .hero-upload-bar .bar-fill {
                    height: 100%; border-radius: 2px;
                    background: linear-gradient(90deg, #ec4899, #8b5cf6);
                    background-size: 200% 100%; animation: heroShimmer 1.5s infinite; transition: width 0.3s;
                }
                @keyframes heroShimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

                .hero-slide-card {
                    border-radius: 0.85rem; overflow: hidden; background: #fff;
                    box-shadow: 0 1px 4px rgba(0,0,0,0.06); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }
                .hero-slide-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(236,72,153,0.12); }
                .hero-slide-card .hsc-thumb { position: relative; padding-top: 56.25%; overflow: hidden; }
                .hero-slide-card .hsc-thumb img {
                    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                    object-fit: cover; transition: transform 0.4s;
                }
                .hero-slide-card:hover .hsc-thumb img { transform: scale(1.04); }
                .hero-slide-card .hsc-overlay {
                    position: absolute; inset: 0;
                    background: linear-gradient(180deg, rgba(0,0,0,0.45) 0%, transparent 40%, transparent 60%, rgba(0,0,0,0.5) 100%);
                    opacity: 0; transition: opacity 0.25s;
                    display: flex; flex-direction: column; justify-content: space-between; padding: 0.6rem;
                }
                .hero-slide-card:hover .hsc-overlay { opacity: 1; }
                .hero-slide-card .hsc-del-btn {
                    width: 30px; height: 30px; border-radius: 50%; border: none;
                    background: rgba(255,255,255,0.2); backdrop-filter: blur(4px);
                    color: #fff; display: inline-flex; align-items: center; justify-content: center;
                    font-size: 1rem; cursor: pointer; transition: all 0.2s;
                }
                .hero-slide-card .hsc-del-btn:hover { background: var(--accent-danger); transform: scale(1.1); }
                .hero-slide-card .hsc-sort-badge {
                    display: inline-flex; align-items: center; gap: 3px;
                    padding: 0.25rem 0.55rem; border-radius: 2rem;
                    font-size: 0.65rem; font-weight: 700;
                    background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); color: #fff;
                }
                .hero-slide-card .hsc-active-badge {
                    display: inline-flex; align-items: center; gap: 3px;
                    padding: 0.25rem 0.55rem; border-radius: 2rem;
                    font-size: 0.65rem; font-weight: 700;
                }
                .hero-slide-card .hsc-active-badge.active { background: rgba(0,200,83,0.2); color: #fff; }
                .hero-slide-card .hsc-active-badge.inactive { background: rgba(255,82,82,0.2); color: #fff; }
                .hero-slide-card .hsc-footer {
                    padding: 0.65rem 0.75rem; border-top: 1px solid #f1f3f6;
                }
                .hero-empty { padding: 3rem 2rem; text-align: center; }
                .hero-empty .empty-icon {
                    width: 70px; height: 70px; border-radius: 50%;
                    background: linear-gradient(135deg, #fce4ec, #f3e5f5);
                    display: inline-flex; align-items: center; justify-content: center;
                    font-size: 1.8rem; color: #d4a0b9; margin-bottom: 0.75rem;
                }
                @keyframes heroCardSlideIn {
                    from { opacity: 0; transform: translateY(12px) scale(0.97); }
                    to { opacity: 1; transform: translateY(0) scale(1); }
                }
                .hero-slide-item { animation: heroCardSlideIn 0.35s ease forwards; }

                .hero-toggle-switch { display: flex; align-items: center; gap: 6px; }
                .hero-toggle-switch .form-check-input { cursor: pointer; }
                .hero-toggle-switch label { font-size: 0.72rem; font-weight: 600; color: var(--text-muted); cursor: pointer; }
            </style>

            {{-- Upload Zone --}}
            <div class="mb-3">
                <input type="file" id="hero-file-input" class="d-none" accept="image/*">
                <div id="hero-drop-zone" class="hero-drop-zone">
                    <div class="drop-icon"><i class='bx bx-cloud-upload'></i></div>
                    <h6 style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; font-size: 0.9rem;">Drop slide image here</h6>
                    <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0;">or click to browse. JPG, PNG, WebP up to 5 MB.</p>
                </div>
                <div id="hero-upload-progress" class="mt-2 d-none">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size: 0.73rem; font-weight: 600; color: #ec4899;">Uploading...</span>
                        <span id="hero-upload-pct" style="font-size: 0.73rem; font-weight: 700; color: #ec4899;">0%</span>
                    </div>
                    <div class="hero-upload-bar"><div class="bar-fill" style="width: 0%"></div></div>
                </div>
            </div>

            {{-- Slides Count --}}
            <div class="d-flex align-items: center; gap-2 mb-3">
                <div style="width: 32px; height: 32px; border-radius: 0.55rem; background: linear-gradient(135deg, #ec4899, #8b5cf6); display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-images text-white' style="font-size: 0.95rem;"></i>
                </div>
                <div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Slides</div>
                    <div style="font-size: 0.92rem; font-weight: 800; color: var(--text-primary); line-height: 1;" id="hero-slide-count">{{ $domain->heroSlides->count() }} slide{{ $domain->heroSlides->count() !== 1 ? 's' : '' }}</div>
                </div>
            </div>

            {{-- Slides Grid --}}
            <div class="row g-3" id="hero-slide-grid">
                @foreach($domain->heroSlides as $slide)
                <div class="col-xl-4 col-lg-6 col-md-6 hero-slide-item" data-id="{{ $slide->id }}">
                    <div class="hero-slide-card">
                        <div class="hsc-thumb">
                            <img src="{{ asset('storage/' . $slide->image_path) }}" alt="Slide {{ $slide->sort_order + 1 }}" loading="lazy">
                            <div class="hsc-overlay">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="hsc-sort-badge"><i class='bx bx-sort-alt-2'></i> #{{ $slide->sort_order + 1 }}</span>
                                    <div class="d-flex gap-1">
                                        <span class="hsc-active-badge {{ $slide->is_active ? 'active' : 'inactive' }}">
                                            {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <button type="button" class="hsc-del-btn" onclick="deleteHeroSlide({{ $slide->id }})">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                                <div></div>
                            </div>
                        </div>
                        <div class="hsc-footer">
                            <div class="hero-toggle-switch">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           {{ $slide->is_active ? 'checked' : '' }}
                                           onchange="toggleHeroSlide({{ $slide->id }}, this)">
                                </div>
                                <label>Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($domain->heroSlides->isEmpty())
            <div id="hero-empty-state" class="hero-empty">
                <div class="empty-icon"><i class='bx bx-slideshow'></i></div>
                <h6 style="font-weight: 700; color: var(--text-secondary);">No slides yet</h6>
                <p style="font-size: 0.82rem; color: var(--text-muted); max-width: 320px; margin: 0 auto;">Upload images for the domain slider.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
(function() {
    const domainId = {{ $domain->id }};
    const csrfToken = '{{ csrf_token() }}';
    const baseUrl = '{{ url("admin/domains") }}/' + domainId + '/hero-slides';

    const dropZone = document.getElementById('hero-drop-zone');
    const fileInput = document.getElementById('hero-file-input');

    ['dragenter', 'dragover'].forEach(e => dropZone.addEventListener(e, function(ev) { ev.preventDefault(); dropZone.classList.add('drag-over'); }));
    ['dragleave', 'drop'].forEach(e => dropZone.addEventListener(e, function(ev) { ev.preventDefault(); dropZone.classList.remove('drag-over'); }));
    dropZone.addEventListener('drop', function(ev) { ev.preventDefault(); if (ev.dataTransfer.files.length) uploadHeroSlide(ev.dataTransfer.files[0]); });
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', function() { if (this.files.length) uploadHeroSlide(this.files[0]); this.value = ''; });

    function uploadHeroSlide(file) {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('image', file);

        const progress = document.getElementById('hero-upload-progress');
        const bar = progress.querySelector('.bar-fill');
        const pct = document.getElementById('hero-upload-pct');
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
                if (data.success && data.slide) {
                    const empty = document.getElementById('hero-empty-state'); if (empty) empty.remove();
                    appendHeroCard(data.slide);
                    updateSlideCount();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Slide uploaded', showConfirmButton: false, timer: 2000, customClass: { popup: 'rounded-3' } });
                }
            } else {
                let msg = 'Check file size and format.';
                try { const err = JSON.parse(xhr.responseText); if (err.message) msg = err.message; } catch(e) {}
                Swal.fire({ icon: 'error', title: 'Upload Failed', text: msg, customClass: { popup: 'rounded-4' } });
            }
        };
        xhr.onerror = function() { progress.classList.add('d-none'); Swal.fire({ icon: 'error', title: 'Upload Failed', text: 'Network error.', customClass: { popup: 'rounded-4' } }); };
        xhr.send(formData);
    }

    function appendHeroCard(slide) {
        const grid = document.getElementById('hero-slide-grid');
        const html = `<div class="col-xl-4 col-lg-6 col-md-6 hero-slide-item" data-id="${slide.id}">
            <div class="hero-slide-card"><div class="hsc-thumb">
                <img src="${slide.url}" alt="Slide ${slide.sort_order + 1}" loading="lazy">
                <div class="hsc-overlay">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="hsc-sort-badge"><i class='bx bx-sort-alt-2'></i> #${slide.sort_order + 1}</span>
                        <div class="d-flex gap-1">
                            <span class="hsc-active-badge active">Active</span>
                            <button type="button" class="hsc-del-btn" onclick="deleteHeroSlide(${slide.id})"><i class='bx bx-trash'></i></button>
                        </div>
                    </div>
                    <div></div>
                </div>
            </div>
            <div class="hsc-footer">
                <div class="hero-toggle-switch">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" checked onchange="toggleHeroSlide(${slide.id}, this)">
                    </div>
                    <label>Active</label>
                </div>
            </div></div></div>`;
        grid.insertAdjacentHTML('beforeend', html);
    }

    window.deleteHeroSlide = function(slideId) {
        Swal.fire({
            title: 'Delete Slide?', text: 'This action cannot be undone.', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#ff5252', cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> Delete', cancelButtonText: 'Cancel',
            reverseButtons: true, customClass: { popup: 'rounded-4', confirmButton: 'rounded-3', cancelButton: 'rounded-3' }
        }).then(result => {
            if (!result.isConfirmed) return;
            fetch(baseUrl + '/' + slideId, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    const card = document.querySelector(`.hero-slide-item[data-id="${slideId}"]`);
                    if (card) { card.style.transition = 'all 0.3s'; card.style.opacity = '0'; card.style.transform = 'scale(0.85)'; setTimeout(() => { card.remove(); updateSlideCount(); showEmptyIfNeeded(); }, 300); }
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Slide deleted', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3' } });
                }
            }).catch(() => Swal.fire('Error', 'Failed to delete slide.', 'error'));
        });
    };

    window.toggleHeroSlide = function(slideId, el) {
        const isActive = el.checked;
        fetch(baseUrl + '/' + slideId, {
            method: 'PUT', headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ is_active: isActive }),
        }).then(r => r.json()).then(data => {
            if (data.success) {
                const card = document.querySelector(`.hero-slide-item[data-id="${slideId}"]`);
                if (card) {
                    const badge = card.querySelector('.hsc-active-badge');
                    badge.className = 'hsc-active-badge ' + (isActive ? 'active' : 'inactive');
                    badge.textContent = isActive ? 'Active' : 'Inactive';
                }
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: isActive ? 'Slide activated' : 'Slide deactivated', showConfirmButton: false, timer: 1000, customClass: { popup: 'rounded-3' } });
            }
        }).catch(() => Swal.fire('Error', 'Failed to toggle slide.', 'error'));
    };

    function updateSlideCount() {
        const count = document.querySelectorAll('.hero-slide-item').length;
        const label = document.getElementById('hero-slide-count');
        if (label) label.textContent = count + ' slide' + (count !== 1 ? 's' : '');
    }

    function showEmptyIfNeeded() {
        if (document.querySelectorAll('.hero-slide-item').length === 0) {
            document.getElementById('hero-slide-grid').insertAdjacentHTML('afterend',
                '<div id="hero-empty-state" class="hero-empty"><div class="empty-icon"><i class="bx bx-slideshow"></i></div><h6 style="font-weight:700;color:var(--text-secondary);">No slides yet</h6><p style="font-size:0.82rem;color:var(--text-muted);max-width:320px;margin:0 auto;">Upload images for the domain slider.</p></div>'
            );
        }
    }
})();
</script>
