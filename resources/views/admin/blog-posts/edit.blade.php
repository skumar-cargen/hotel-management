@php
    $isEdit = isset($post) && $post->exists;
@endphp

<x-admin-layout :title="$isEdit ? 'Edit Blog Post' : 'Create Blog Post'" :pageTitle="$isEdit ? 'Edit: ' . $post->title : 'Create Blog Post'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <x-slot:styles>
        <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
        <style>
            .flatpickr-calendar {
                border: none !important; border-radius: 0.85rem !important;
                box-shadow: 0 10px 40px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06) !important;
                font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important; z-index: 9999 !important;
            }
            .flatpickr-months {
                background: linear-gradient(135deg, #667eea, #8b5cf6);
                padding: 6px 0; border-radius: 0.85rem 0.85rem 0 0; overflow: hidden;
            }
            .flatpickr-months .flatpickr-month { height: 38px; }
            .flatpickr-current-month { font-size: 0.95rem; font-weight: 700; color: #fff !important; padding-top: 4px; }
            .flatpickr-current-month .flatpickr-monthDropdown-months { background: transparent; color: #fff; font-weight: 700; -webkit-appearance: none; }
            .flatpickr-current-month .flatpickr-monthDropdown-months option { background: #fff; color: #1a1d29; }
            .flatpickr-current-month input.cur-year { color: #fff !important; font-weight: 700; }
            .flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month { fill: #fff !important; color: #fff !important; padding: 6px 10px; }
            .flatpickr-months .flatpickr-prev-month:hover svg, .flatpickr-months .flatpickr-next-month:hover svg { fill: rgba(255,255,255,0.7) !important; }
            .flatpickr-weekdays { background: linear-gradient(135deg, #667eea, #8b5cf6); }
            span.flatpickr-weekday { color: rgba(255,255,255,0.7) !important; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.03em; }
            .flatpickr-innerContainer { padding: 4px; }
            .flatpickr-day {
                border-radius: 8px !important; font-weight: 500; font-size: 0.84rem;
                color: #374151; border: none; height: 36px; line-height: 36px; transition: all 0.15s;
            }
            .flatpickr-day:hover { background: #eef2ff; color: #667eea; }
            .flatpickr-day.today { background: rgba(102,126,234,0.1); color: #667eea; border: none; }
            .flatpickr-day.today:hover { background: rgba(102,126,234,0.2); }
            .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
                background: linear-gradient(135deg, #667eea, #8b5cf6) !important;
                color: #fff !important; border: none !important; box-shadow: 0 2px 8px rgba(102,126,234,0.35);
            }
            .flatpickr-day.inRange { background: rgba(102,126,234,0.1) !important; box-shadow: none !important; border: none !important; color: #667eea; }
            .flatpickr-day.flatpickr-disabled, .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay { color: #d1d5db !important; }
            .date-input-wrap { position: relative; }
            .date-input-wrap .form-control { padding-right: 2.5rem; cursor: pointer; }
            .date-input-wrap .flatpickr-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; font-size: 1.1rem; z-index: 2; }

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
                font-size: 0.9rem; min-height: 300px;
            }
            .note-editor .note-statusbar { display: none !important; }
        </style>
    </x-slot:styles>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }

        /* ── Section Headers ──────────────────────────── */
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

        /* ── Premium Cards ────────────────────────────── */
        .card {
            border: none; border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        }

        /* ── Toggle Switches ──────────────────────────── */
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

        /* ── Image Upload ────────────────────────────── */
        .image-upload-zone {
            border: 2px dashed var(--border-light); border-radius: 0.75rem;
            padding: 1.25rem; text-align: center; cursor: pointer;
            transition: all 0.2s; position: relative; overflow: hidden;
        }
        .image-upload-zone:hover { border-color: var(--accent-primary); background: rgba(79,70,229,0.02); }
        .image-upload-zone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer;
        }
        .image-upload-zone .upload-icon { font-size: 2rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        .image-upload-zone .upload-text { font-size: 0.8rem; color: var(--text-muted); }
        .image-upload-zone .upload-text strong { color: var(--accent-primary); }
        .current-image { margin-top: 0.75rem; }
        .current-image img { max-height: 120px; border-radius: 0.5rem; border: 1px solid var(--border-light); }

        /* ── Tags Input ──────────────────────────────── */
        .tags-help { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.35rem; }

        /* ── Save Bar ─────────────────────────────────── */
        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 16px;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,.06);
            border: none; margin-top: 1.5rem;
        }

        /* ── Info Card ───────────────────────────────── */
        .info-card-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 0.65rem 0; border-bottom: 1px solid #f1f5f9;
        }
        .info-card-item:last-child { border-bottom: none; padding-bottom: 0; }
        .info-card-item .ici-icon {
            width: 28px; height: 28px; border-radius: 6px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
        }
        .info-card-item .ici-text { font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; }
        .info-card-item .ici-text strong { color: var(--text-secondary); }
    </style>

    <form action="{{ $isEdit ? route('admin.blog-posts.update', $post) : route('admin.blog-posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
            {{-- ═══ Main Content (Left Column) ═══ --}}
            <div class="col-lg-8">
                {{-- Post Details --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-edit'></i>
                            </div>
                            <div>
                                <h6>Post Details</h6>
                                <p>Title, category, and excerpt</p>
                            </div>
                        </div>

                        <x-form.input name="title" label="Title" :value="old('title', $post->title ?? '')" required placeholder="Enter blog post title" class="mb-3" />

                        <div class="row g-3 mb-3">
                            <x-form.select name="blog_category_id" label="Category" :options="$categories->pluck('name', 'id')" :selected="old('blog_category_id', $post->blog_category_id)" placeholder="— No Category —" class="col-md-6" />
                            <div class="col-md-6">
                                <x-form.input name="excerpt" label="Excerpt" :value="old('excerpt', $post->excerpt ?? '')" placeholder="Brief summary (max 500 chars)" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Content (Summernote) --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                                <i class='bx bx-detail'></i>
                            </div>
                            <div>
                                <h6>Content</h6>
                                <p>Write the blog post content</p>
                            </div>
                        </div>

                        <textarea name="content" id="content-editor" class="summernote-editor">{!! old('content', $post->content ?? '') !!}</textarea>
                        @error('content')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- SEO --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(245,158,11,0.1); color: var(--accent-warning);">
                                <i class='bx bx-search-alt'></i>
                            </div>
                            <div>
                                <h6>SEO Settings</h6>
                                <p>Search engine optimization fields</p>
                            </div>
                        </div>

                        <x-form.input name="meta_title" label="Meta Title" :value="old('meta_title', $post->meta_title ?? '')" placeholder="SEO title (max 60 chars)" class="mb-3" />

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="3" placeholder="SEO description (max 160 chars)">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                            @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <x-form.input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $post->meta_keywords ?? '')" placeholder="Comma-separated keywords" class="mb-3" />

                        <x-form.input name="canonical_url" label="Canonical URL" :value="old('canonical_url', $post->canonical_url ?? '')" placeholder="https://..." class="mb-3" />

                        <div>
                            <label for="seo_content" class="form-label">SEO Content</label>
                            <textarea name="seo_content" id="seo_content" class="form-control @error('seo_content') is-invalid @enderror" rows="4" placeholder="Additional SEO content block">{{ old('seo_content', $post->seo_content ?? '') }}</textarea>
                            @error('seo_content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Domain Assignment --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-globe'></i>
                            </div>
                            <div>
                                <h6>Domain Assignment</h6>
                                <p>Select which domains will display this blog post</p>
                            </div>
                        </div>
                        <x-form.select2-ajax name="domains" label="Domains" :url="route('admin.api.search.domains')" multiple placeholder="Search domains..." class="col-12" />
                    </div>
                </div>
            </div>

            {{-- ═══ Sidebar (Right Column) ═══ --}}
            <div class="col-lg-4">
                {{-- Publish Settings --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-cog'></i>
                            </div>
                            <div>
                                <h6>Publish Settings</h6>
                                <p>Status, visibility, and schedule</p>
                            </div>
                        </div>

                        <div class="toggle-card mb-3">
                            <div class="toggle-label">
                                <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                <span>Active</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                       {{ old('is_active', $isEdit ? $post->is_active : true) ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="toggle-card mb-3">
                            <div class="toggle-label">
                                <div class="tl-icon"><i class='bx bx-star'></i></div>
                                <span>Featured</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" name="is_featured" value="1" class="form-check-input" role="switch"
                                       {{ old('is_featured', $isEdit ? $post->is_featured : false) ? 'checked' : '' }}>
                            </div>
                        </div>

                        <x-form.select name="status" label="Status" :options="['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']" :selected="old('status', $post->status ?? 'draft')" :placeholder="false" required class="mb-3" />

                        <div>
                            <label for="published_at" class="form-label">Publish Date</label>
                            <div class="date-input-wrap">
                                <input type="text" name="published_at" id="published_at" class="form-control flatpickr-date @error('published_at') is-invalid @enderror"
                                       value="{{ old('published_at', $isEdit && $post->published_at ? $post->published_at->format('Y-m-d H:i') : '') }}"
                                       placeholder="Select publish date" autocomplete="off" readonly>
                                <i class='bx bx-calendar flatpickr-icon'></i>
                            </div>
                            @error('published_at')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Featured Image --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                                <i class='bx bx-image'></i>
                            </div>
                            <div>
                                <h6>Featured Image</h6>
                            </div>
                        </div>

                        <div class="image-upload-zone">
                            <input type="file" name="featured_image" accept="image/*" id="featured-image-input">
                            <div class="upload-icon"><i class='bx bx-cloud-upload'></i></div>
                            <div class="upload-text"><strong>Click to upload</strong> or drag and drop<br>PNG, JPG up to 2MB</div>
                        </div>
                        @if($isEdit && $post->featured_image)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="Featured image">
                        </div>
                        @endif
                        @error('featured_image')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- OG Image --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                <i class='bx bx-share-alt'></i>
                            </div>
                            <div>
                                <h6>OG Image</h6>
                                <p>Social media sharing image</p>
                            </div>
                        </div>

                        <div class="image-upload-zone">
                            <input type="file" name="og_image" accept="image/*" id="og-image-input">
                            <div class="upload-icon"><i class='bx bx-cloud-upload'></i></div>
                            <div class="upload-text"><strong>Click to upload</strong><br>Recommended: 1200x630px</div>
                        </div>
                        @if($isEdit && $post->og_image)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $post->og_image) }}" alt="OG image">
                        </div>
                        @endif
                        @error('og_image')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Tags --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(236,72,153,0.1); color: #ec4899;">
                                <i class='bx bx-purchase-tag'></i>
                            </div>
                            <div>
                                <h6>Tags</h6>
                            </div>
                        </div>

                        <input type="text" name="tags" class="form-control @error('tags') is-invalid @enderror"
                               value="{{ old('tags', $isEdit && $post->tags ? implode(', ', $post->tags) : '') }}"
                               placeholder="e.g. dubai, travel, hotels">
                        <div class="tags-help">Separate tags with commas</div>
                        @error('tags')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Quick Info (edit only) --}}
                @if($isEdit)
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                                <i class='bx bx-bar-chart-alt-2'></i>
                            </div>
                            <div>
                                <h6>Quick Info</h6>
                            </div>
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Author</span>
                                <strong style="color: var(--text-secondary);">{{ $post->author?->name ?? '—' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Created</span>
                                <strong style="color: var(--text-secondary);">{{ $post->created_at->format('M j, Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Updated</span>
                                <strong style="color: var(--text-secondary);">{{ $post->updated_at->format('M j, Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Views</span>
                                <strong style="color: var(--text-secondary);">{{ number_format($post->view_count) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Domains</span>
                                <strong style="color: var(--text-secondary);">{{ $post->domains->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span>Slug</span>
                                <strong style="color: var(--text-secondary);">{{ $post->slug }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <div class="d-flex gap-2">
                @if($isEdit)
                <a href="{{ route('admin.blog-posts.show', $post) }}" class="btn btn-outline-primary px-4">
                    <i class='bx bx-show me-1'></i> Preview
                </a>
                @endif
                <button type="submit" class="btn btn-primary px-4">
                    <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Post' : 'Create Post' }}
                </button>
            </div>
        </div>
    </form>

    <x-slot:scripts>
        <script src="{{ asset('assets/js/flatpickr.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
        <script>
            $(function() {
                // Summernote config
                var summernoteOpts = {
                    height: 350,
                    placeholder: 'Write your blog post content here...',
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
                            $(this).closest('.note-editor').css('margin-bottom', '0');
                        }
                    }
                };

                // Init Summernote
                $('.summernote-editor').summernote(summernoteOpts);

                // Flatpickr date picker with time
                flatpickr(document.getElementById('published_at'), {
                    dateFormat: 'Y-m-d H:i',
                    altInput: true,
                    altFormat: 'M j, Y h:i K',
                    enableTime: true,
                    allowInput: false,
                    disableMobile: true,
                    appendTo: document.body
                });

                // Pre-populate Select2 with existing domains on edit
                @if($isEdit && $post->domains->count())
                var $domainSelect = $('#select2-domains');
                @foreach($post->domains as $domain)
                $domainSelect.append(new Option(@json($domain->name), {{ $domain->id }}, true, true));
                @endforeach
                $domainSelect.trigger('change');
                @endif
            });
        </script>
    </x-slot:scripts>
</x-admin-layout>
