<x-admin-layout :title="'Preview: ' . $post->title" :pageTitle="'Preview: ' . $post->title">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.blog-posts.index') }}">Blog Posts</a></li>
        <li class="breadcrumb-item active">Preview</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }

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
        .card { border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04); }
        .preview-content { font-size: 0.95rem; line-height: 1.8; color: var(--text-secondary); }
        .preview-content img { max-width: 100%; border-radius: 0.5rem; }
        .meta-item { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid #f1f5f9; font-size: 0.8rem; }
        .meta-item:last-child { border-bottom: none; }
        .meta-item .meta-label { color: var(--text-muted); }
        .meta-item .meta-value { color: var(--text-secondary); font-weight: 600; }
        .tag-badge { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 2rem; background: #f1f5f9; color: var(--text-secondary); font-size: 0.75rem; margin: 0.15rem; }
        .action-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 16px;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,.06);
            border: none; margin-top: 1.5rem;
        }
    </style>

    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Post Header --}}
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        @php
                            $statusColors = ['draft' => 'warning', 'published' => 'success', 'archived' => 'secondary'];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$post->status] ?? 'secondary' }}">{{ ucfirst($post->status) }}</span>
                        @if($post->is_featured)
                        <span class="badge bg-info">Featured</span>
                        @endif
                        @if(!$post->is_active)
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>

                    <h3 style="font-weight: 800; color: var(--text-primary); margin-bottom: 0.75rem;">{{ $post->title }}</h3>

                    <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 0.82rem; color: var(--text-muted);">
                        @if($post->author)
                        <span><i class='bx bx-user me-1'></i>{{ $post->author->name }}</span>
                        @endif
                        @if($post->category)
                        <span><i class='bx bx-category me-1'></i>{{ $post->category->name }}</span>
                        @endif
                        @if($post->published_at)
                        <span><i class='bx bx-calendar me-1'></i>{{ $post->published_at->format('M j, Y g:i A') }}</span>
                        @endif
                        <span><i class='bx bx-show me-1'></i>{{ number_format($post->view_count) }} views</span>
                    </div>

                    @if($post->excerpt)
                    <div class="mt-3 p-3" style="background: #f8fafc; border-radius: 0.5rem; border-left: 3px solid var(--accent-primary);">
                        <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary); font-style: italic;">{{ $post->excerpt }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Featured Image --}}
            @if($post->featured_image)
            <div class="card mt-3">
                <div class="card-body p-4">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-100" style="border-radius: 0.75rem; max-height: 400px; object-fit: cover;">
                </div>
            </div>
            @endif

            {{-- Content --}}
            <div class="card mt-3">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                            <i class='bx bx-detail'></i>
                        </div>
                        <div>
                            <h6>Content</h6>
                        </div>
                    </div>
                    <div class="preview-content">
                        {!! $post->content !!}
                    </div>
                </div>
            </div>

            {{-- SEO Info --}}
            @if($post->meta_title || $post->meta_description || $post->canonical_url)
            <div class="card mt-3">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(245,158,11,0.1); color: var(--accent-warning);">
                            <i class='bx bx-search-alt'></i>
                        </div>
                        <div>
                            <h6>SEO Information</h6>
                        </div>
                    </div>
                    @if($post->meta_title)
                    <div class="meta-item">
                        <span class="meta-label">Meta Title</span>
                        <span class="meta-value">{{ $post->meta_title }}</span>
                    </div>
                    @endif
                    @if($post->meta_description)
                    <div class="meta-item">
                        <span class="meta-label">Meta Description</span>
                        <span class="meta-value" style="max-width: 60%; text-align: right;">{{ $post->meta_description }}</span>
                    </div>
                    @endif
                    @if($post->meta_keywords)
                    <div class="meta-item">
                        <span class="meta-label">Meta Keywords</span>
                        <span class="meta-value">{{ $post->meta_keywords }}</span>
                    </div>
                    @endif
                    @if($post->canonical_url)
                    <div class="meta-item">
                        <span class="meta-label">Canonical URL</span>
                        <span class="meta-value">{{ $post->canonical_url }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Post Details --}}
            <div class="card">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                            <i class='bx bx-info-circle'></i>
                        </div>
                        <div>
                            <h6>Post Details</h6>
                        </div>
                    </div>
                    <div class="meta-item"><span class="meta-label">Status</span><span class="badge bg-{{ $statusColors[$post->status] ?? 'secondary' }}">{{ ucfirst($post->status) }}</span></div>
                    <div class="meta-item"><span class="meta-label">Active</span><span class="meta-value">{{ $post->is_active ? 'Yes' : 'No' }}</span></div>
                    <div class="meta-item"><span class="meta-label">Featured</span><span class="meta-value">{{ $post->is_featured ? 'Yes' : 'No' }}</span></div>
                    <div class="meta-item"><span class="meta-label">Category</span><span class="meta-value">{{ $post->category?->name ?? '—' }}</span></div>
                    <div class="meta-item"><span class="meta-label">Author</span><span class="meta-value">{{ $post->author?->name ?? '—' }}</span></div>
                    <div class="meta-item"><span class="meta-label">Published</span><span class="meta-value">{{ $post->published_at?->format('M j, Y') ?? '—' }}</span></div>
                    <div class="meta-item"><span class="meta-label">Created</span><span class="meta-value">{{ $post->created_at->format('M j, Y') }}</span></div>
                    <div class="meta-item"><span class="meta-label">Updated</span><span class="meta-value">{{ $post->updated_at->format('M j, Y') }}</span></div>
                    <div class="meta-item"><span class="meta-label">Views</span><span class="meta-value">{{ number_format($post->view_count) }}</span></div>
                    <div class="meta-item"><span class="meta-label">Slug</span><span class="meta-value">{{ $post->slug }}</span></div>
                </div>
            </div>

            {{-- Domains --}}
            @if($post->domains->count())
            <div class="card mt-3">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                            <i class='bx bx-globe'></i>
                        </div>
                        <div>
                            <h6>Domains ({{ $post->domains->count() }})</h6>
                        </div>
                    </div>
                    @foreach($post->domains as $domain)
                    <span class="badge bg-secondary me-1 mb-1">{{ $domain->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tags --}}
            @if($post->tags && count($post->tags))
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
                    @foreach($post->tags as $tag)
                    <span class="tag-badge">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- OG Image --}}
            @if($post->og_image)
            <div class="card mt-3">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                            <i class='bx bx-share-alt'></i>
                        </div>
                        <div>
                            <h6>OG Image</h6>
                        </div>
                    </div>
                    <img src="{{ asset('storage/' . $post->og_image) }}" alt="OG Image" class="w-100" style="border-radius: 0.5rem;">
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="action-bar">
        <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-light px-4">
            <i class='bx bx-arrow-back me-1'></i> Back to List
        </a>
        <a href="{{ route('admin.blog-posts.edit', $post) }}" class="btn btn-primary px-4">
            <i class='bx bx-edit-alt me-1'></i> Edit Post
        </a>
    </div>
</x-admin-layout>
