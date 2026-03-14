@php
    $isEdit = isset($deal) && $deal->exists;
@endphp

<x-admin-layout :title="$isEdit ? 'Edit Deal' : 'Create Deal'" :pageTitle="$isEdit ? 'Edit Deal: ' . $deal->title : 'Create Deal'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.deals.index') }}">Deals</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <x-slot:styles>
        <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet">
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
        </style>
    </x-slot:styles>

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

        .discount-preview {
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; border-radius: 0.75rem; min-height: 80px;
            background: linear-gradient(135deg, rgba(245,158,11,0.05), rgba(249,115,22,0.05));
            border: 1.5px solid rgba(245,158,11,0.12);
        }
        .discount-preview .dp-value {
            font-size: 1.8rem; font-weight: 800; color: #f59e0b;
            font-family: 'SFMono-Regular', 'Consolas', monospace;
        }
        .discount-preview .dp-suffix { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); margin-left: 4px; }

        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border-light); margin-top: 1.5rem;
        }
    </style>

    <form action="{{ $isEdit ? route('admin.deals.update', $deal) : route('admin.deals.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
            {{-- Main Content --}}
            <div class="col-lg-8">
                {{-- Deal Details --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                                <i class='bx bx-purchase-tag'></i>
                            </div>
                            <div>
                                <h6>Deal Details</h6>
                                <p>Title, description, and discount configuration</p>
                            </div>
                        </div>

                        <x-form.input name="title" label="Title" :value="old('title', $deal->title ?? '')" required placeholder="e.g. Summer Special 30% OFF" class="mb-3" />
                        <x-form.textarea name="description" label="Description" :value="old('description', $deal->description ?? '')" placeholder="Optional deal description..." class="mb-3" rows="3" />

                        <div class="row g-3">
                            <x-form.select name="discount_type" label="Discount Type" :options="[
                                'percentage' => 'Percentage (%)',
                                'fixed_amount' => 'Fixed Amount (AED)',
                            ]" :selected="old('discount_type', $deal->discount_type ?? 'percentage')" required class="col-md-6" />
                            <x-form.input name="discount_value" label="Discount Value" type="number" step="0.01" min="0" :value="old('discount_value', $deal->discount_value ?? '')" required placeholder="e.g. 30" class="col-md-6" />
                        </div>

                        {{-- Live Preview --}}
                        <div class="discount-preview mt-3">
                            <span class="dp-value" id="discountPreviewValue">{{ old('discount_value', $deal->discount_value ?? '0') }}</span>
                            <span class="dp-suffix" id="discountPreviewSuffix">{{ old('discount_type', $deal->discount_type ?? 'percentage') === 'percentage' ? '% OFF' : 'AED OFF' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                                <i class='bx bx-calendar-event'></i>
                            </div>
                            <div>
                                <h6>Validity Period</h6>
                                <p>When this deal is available to customers</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <div class="date-input-wrap">
                                    <input type="text" name="start_date" id="start_date" class="form-control flatpickr-date @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date', $isEdit ? $deal->start_date?->format('Y-m-d') : '') }}"
                                           placeholder="Select start date" autocomplete="off" readonly required>
                                    <i class='bx bx-calendar flatpickr-icon'></i>
                                </div>
                                @error('start_date')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <div class="date-input-wrap">
                                    <input type="text" name="end_date" id="end_date" class="form-control flatpickr-date @error('end_date') is-invalid @enderror"
                                           value="{{ old('end_date', $isEdit ? $deal->end_date?->format('Y-m-d') : '') }}"
                                           placeholder="Select end date" autocomplete="off" readonly required>
                                    <i class='bx bx-calendar flatpickr-icon'></i>
                                </div>
                                @error('end_date')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scope --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-target-lock'></i>
                            </div>
                            <div>
                                <h6>Scope</h6>
                                <p>Which hotels and domains this deal applies to</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.select2-ajax name="hotels" label="Hotels" :url="route('admin.api.search.hotels')" multiple placeholder="Search hotels..." class="col-12" />
                            <x-form.select2-ajax name="domains" label="Domains" :url="route('admin.api.search.domains')" multiple placeholder="Search domains..." class="col-12" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Status --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-cog'></i>
                            </div>
                            <div>
                                <h6>Status</h6>
                                <p>Control deal visibility</p>
                            </div>
                        </div>
                        <div class="toggle-card">
                            <div class="toggle-label">
                                <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                <span>Active</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                       {{ old('is_active', $isEdit ? $deal->is_active : true) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                <i class='bx bx-bulb'></i>
                            </div>
                            <div>
                                <h6>How Deals Work</h6>
                            </div>
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.7;">
                            <p class="mb-2"><strong>Visibility:</strong> Deals show on frontend domains that are assigned to them.</p>
                            <p class="mb-2"><strong>Hotels:</strong> Assign specific hotels to show the deal on their pages.</p>
                            <p class="mb-0"><strong>Dates:</strong> Deals are only visible between their start and end dates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.deals.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Deal' : 'Create Deal' }}
            </button>
        </div>
    </form>

    <x-slot:scripts>
        <script src="{{ asset('assets/js/flatpickr.min.js') }}"></script>
        <script>
            // Flatpickr date pickers
            (function() {
                var startInput = document.getElementById('start_date');
                var endInput = document.getElementById('end_date');

                var startPicker = flatpickr(startInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'M j, Y',
                    allowInput: false,
                    disableMobile: true,
                    appendTo: document.body,
                    onChange: function(selectedDates) {
                        endPicker.set('minDate', selectedDates.length > 0 ? selectedDates[0] : null);
                    }
                });

                var endPicker = flatpickr(endInput, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'M j, Y',
                    allowInput: false,
                    disableMobile: true,
                    appendTo: document.body,
                    onChange: function(selectedDates) {
                        startPicker.set('maxDate', selectedDates.length > 0 ? selectedDates[0] : null);
                    }
                });

                if (startInput.value) endPicker.set('minDate', startInput.value);
                if (endInput.value) startPicker.set('maxDate', endInput.value);
            })();

            // Pre-populate Select2 with existing values on edit
            @if($isEdit && $deal->hotels->count())
            (function() {
                var $hotelSelect = $('#select2-hotels');
                @foreach($deal->hotels as $hotel)
                $hotelSelect.append(new Option(@json($hotel->name), {{ $hotel->id }}, true, true));
                @endforeach
                $hotelSelect.trigger('change');
            })();
            @endif

            @if($isEdit && $deal->domains->count())
            (function() {
                var $domainSelect = $('#select2-domains');
                @foreach($deal->domains as $domain)
                $domainSelect.append(new Option(@json($domain->name), {{ $domain->id }}, true, true));
                @endforeach
                $domainSelect.trigger('change');
            })();
            @endif

            // Live discount preview
            var discountValueInput = document.querySelector('input[name="discount_value"]');
            var discountTypeSelect = document.querySelector('select[name="discount_type"]');
            var dpValue = document.getElementById('discountPreviewValue');
            var dpSuffix = document.getElementById('discountPreviewSuffix');

            function updateDiscountPreview() {
                var val = discountValueInput.value || '0';
                dpValue.textContent = val;
                dpSuffix.textContent = discountTypeSelect.value === 'percentage' ? '% OFF' : ' AED OFF';
            }

            if (discountValueInput) discountValueInput.addEventListener('input', updateDiscountPreview);
            if (discountTypeSelect) discountTypeSelect.addEventListener('change', updateDiscountPreview);
            updateDiscountPreview();
        </script>
    </x-slot:scripts>
</x-admin-layout>
