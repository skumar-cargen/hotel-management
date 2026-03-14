<x-admin-layout title="Testimonials" pageTitle="Testimonials">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Testimonials</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }
        .page-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #1e40af 100%);
            border-radius: 16px; padding: 1.75rem 2rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 1.25rem; position: relative; overflow: hidden;
        }
        .page-header::before {
            content: ''; position: absolute; top: -60%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
        }
        .page-header .header-icon {
            width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff; flex-shrink: 0; position: relative; z-index: 1;
        }
        .page-header h4 { color: #fff; font-weight: 700; font-size: 1.25rem; margin: 0; position: relative; z-index: 1; }
        .page-header p { color: rgba(255,255,255,0.5); font-size: .85rem; margin: .25rem 0 0; position: relative; z-index: 1; }
        .page-header .header-actions { position: relative; z-index: 1; }

        .domain-selector { max-width: 350px; }

        .testimonial-card {
            border: 1px solid var(--border-light); border-radius: 12px; padding: 1.25rem;
            background: #fff; transition: box-shadow .2s; position: relative;
        }
        .testimonial-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
        .testimonial-card .card-number {
            position: absolute; top: -10px; left: -10px; width: 28px; height: 28px;
            background: var(--accent-primary); color: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700;
        }
        .testimonial-card .guest-name { font-weight: 600; font-size: .95rem; color: var(--text-primary); }
        .testimonial-card .hotel-name { font-size: .8rem; color: var(--text-muted); }
        .testimonial-card .review-title { font-weight: 600; margin-top: .5rem; font-size: .9rem; }
        .testimonial-card .review-comment { font-size: .85rem; color: var(--text-secondary); margin-top: .25rem; }
        .testimonial-card .review-meta { font-size: .75rem; color: var(--text-muted); margin-top: .5rem; }
        .testimonial-card .stars { color: #f59e0b; }
        .testimonial-card .remove-btn {
            position: absolute; top: 10px; right: 10px; border: none; background: none;
            color: var(--text-muted); cursor: pointer; font-size: 1.1rem; padding: .25rem;
            border-radius: 6px; transition: all .2s;
        }
        .testimonial-card .remove-btn:hover { background: #fee2e2; color: var(--accent-danger); }

        .empty-state {
            text-align: center; padding: 3rem 1.5rem; background: #f9fafb; border-radius: 12px;
            border: 2px dashed var(--border-light);
        }
        .empty-state i { font-size: 3rem; color: var(--text-muted); }
        .empty-state h5 { color: var(--text-secondary); margin-top: .75rem; }
        .empty-state p { color: var(--text-muted); font-size: .875rem; }

        .slots-indicator { display: flex; gap: .35rem; }
        .slot { width: 32px; height: 6px; border-radius: 3px; background: #e5e7eb; }
        .slot.filled { background: var(--accent-primary); }

        .search-review-item {
            border: 1px solid var(--border-light); border-radius: 10px; padding: 1rem;
            cursor: pointer; transition: all .2s; margin-bottom: .5rem;
        }
        .search-review-item:hover { border-color: var(--accent-primary); background: #f0f0ff; }
        .search-review-item .stars { color: #f59e0b; font-size: .85rem; }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
            <i class='bx bx-award'></i>
        </div>
        <div class="flex-grow-1">
            <h4>Testimonials</h4>
            <p>Manage featured reviews displayed on each domain's website</p>
        </div>
    </div>

    <!-- Domain Selector + Slots + Add Button -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center g-3">
                <div class="col-md-5">
                    <select id="domainSelect" class="form-select form-select-sm" onchange="window.location='{{ route('admin.testimonials.index') }}?domain_id='+this.value">
                        @foreach($domains as $domain)
                            <option value="{{ $domain->id }}" {{ $selectedDomain && $selectedDomain->id === $domain->id ? 'selected' : '' }}>
                                {{ $domain->name }} ({{ $domain->domain }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedDomain)
                <div class="col-md-7 d-flex align-items-center justify-content-end gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="slots-indicator">
                            @for($i = 0; $i < 5; $i++)
                                <div class="slot {{ $i < $testimonials->count() ? 'filled' : '' }}"></div>
                            @endfor
                        </div>
                        <span style="font-size: .78rem; color: var(--text-muted);">{{ $testimonials->count() }}/5</span>
                    </div>
                    @if($testimonials->count() < 5)
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                        <i class='bx bx-plus me-1'></i> Add Review
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Testimonials Grid -->
    @if($selectedDomain)
        @if($testimonials->isNotEmpty())
            <div class="row g-3">
                @foreach($testimonials as $index => $review)
                <div class="col-md-6 col-xl-4">
                    <div class="testimonial-card">
                        <div class="card-number">{{ $index + 1 }}</div>
                        <form action="{{ route('admin.testimonials.destroy', $review->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="domain_id" value="{{ $selectedDomain->id }}">
                            <button type="button" class="remove-btn" title="Remove testimonial" data-confirm-delete="Remove this review from testimonials?">
                                <i class='bx bx-x'></i>
                            </button>
                        </form>

                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div>
                                <div class="guest-name">{{ $review->guest_name }}</div>
                                <div class="hotel-name">{{ $review->hotel->name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="stars">
                            @for($i = 0; $i < $review->rating; $i++)
                                <i class='bx bxs-star'></i>
                            @endfor
                            @for($i = $review->rating; $i < 5; $i++)
                                <i class='bx bx-star' style="color: #d1d5db;"></i>
                            @endfor
                        </div>

                        @if($review->title)
                            <div class="review-title">{{ $review->title }}</div>
                        @endif
                        <div class="review-comment">{{ Str::limit($review->comment, 150) }}</div>

                        <div class="review-meta">
                            @if($review->is_verified)
                                <span class="text-success"><i class='bx bx-check-circle'></i> Verified</span> &middot;
                            @endif
                            {{ $review->created_at?->format('M d, Y') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class='bx bx-message-square-detail' style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">No testimonials yet for <strong>{{ $selectedDomain->name }}</strong>. Click <strong>Add Review</strong> above to get started.</p>
            </div>
        @endif
    @else
        <div class="empty-state">
            <i class='bx bx-globe'></i>
            <h5>No Domains Available</h5>
            <p>Create a domain first to manage testimonials.</p>
        </div>
    @endif

    <!-- Add Testimonial Modal -->
    @if($selectedDomain)
    <div class="modal fade" id="addTestimonialModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add Testimonial — {{ $selectedDomain->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class='bx bx-search'></i></span>
                            <input type="text" id="reviewSearch" class="form-control" placeholder="Search by guest name, hotel, or review content..." autofocus>
                        </div>
                        <small class="text-muted">Only approved reviews from hotels linked to this domain are shown.</small>
                    </div>

                    <div id="reviewResults" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center text-muted py-4" id="searchPlaceholder">
                            <i class='bx bx-search' style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Search for reviews to add as testimonials</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <x-slot:scripts>
    @if($selectedDomain)
    <script>
    $(document).ready(function() {
        let searchTimer;
        const domainId = {{ $selectedDomain->id }};

        function loadReviews(query = '') {
            $('#searchPlaceholder').hide();
            $('#reviewResults').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div></div>');

            $.get('{{ route("admin.testimonials.search-reviews") }}', { domain_id: domainId, q: query }, function(data) {
                if (data.reviews.length === 0) {
                    $('#reviewResults').html('<div class="text-center text-muted py-4"><p>No reviews found.</p></div>');
                    return;
                }

                let html = '';
                data.reviews.forEach(function(review) {
                    let stars = '';
                    for (let i = 0; i < review.rating; i++) stars += '<i class="bx bxs-star"></i>';
                    for (let i = review.rating; i < 5; i++) stars += '<i class="bx bx-star" style="color:#d1d5db"></i>';

                    html += `<div class="search-review-item" data-review-id="${review.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${review.guest_name}</strong>
                                <span class="text-muted" style="font-size:.8rem"> — ${review.hotel_name}</span>
                            </div>
                            <div class="stars">${stars}</div>
                        </div>
                        ${review.title ? '<div class="fw-semibold mt-1" style="font-size:.88rem">' + review.title + '</div>' : ''}
                        <div class="text-secondary mt-1" style="font-size:.83rem">${review.comment}</div>
                        <div class="text-muted mt-1" style="font-size:.75rem">${review.created_at}</div>
                    </div>`;
                });

                $('#reviewResults').html(html);
            });
        }

        // Load initial reviews when modal opens
        $('#addTestimonialModal').on('shown.bs.modal', function() {
            loadReviews();
            $('#reviewSearch').focus();
        });

        // Search with debounce
        $('#reviewSearch').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadReviews($(this).val()), 300);
        });

        // Click to add testimonial
        $(document).on('click', '.search-review-item', function() {
            const reviewId = $(this).data('review-id');
            const $item = $(this);

            $item.css('opacity', '0.5').css('pointer-events', 'none');

            $.post('{{ route("admin.testimonials.store") }}', {
                _token: '{{ csrf_token() }}',
                domain_id: domainId,
                review_id: reviewId
            }).done(function() {
                window.location.reload();
            }).fail(function(xhr) {
                $item.css('opacity', '1').css('pointer-events', 'auto');
                const msg = xhr.responseJSON?.message || 'Failed to add testimonial.';
                alert(msg);
            });
        });
    });
    </script>
    @endif
    </x-slot:scripts>
</x-admin-layout>
