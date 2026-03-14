@php
    $isEdit = isset($pricingRule) && $pricingRule->exists;
    $daysSource = $isEdit ? $pricingRule->days_of_week : [];
    $selectedDays = is_array(old('days_of_week')) ? old('days_of_week') : (is_string($daysSource) ? json_decode($daysSource, true) : ($daysSource ?? []));
@endphp

<x-admin-layout :title="$isEdit ? 'Edit Pricing Rule' : 'Create Pricing Rule'" :pageTitle="$isEdit ? 'Edit Rule: ' . $pricingRule->name : 'Create Pricing Rule'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.pricing-rules.index') }}">Pricing Rules</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <x-slot:styles>
        <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet">
        <style>
            /* Flatpickr theme overrides to match admin design */
            .flatpickr-calendar {
                border: none !important;
                border-radius: 0.85rem !important;
                box-shadow: 0 10px 40px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06) !important;
                font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
                z-index: 9999 !important;
            }
            .flatpickr-months {
                background: linear-gradient(135deg, #667eea, #8b5cf6);
                padding: 6px 0;
                border-radius: 0.85rem 0.85rem 0 0;
                overflow: hidden;
            }
            .flatpickr-months .flatpickr-month { height: 38px; }
            .flatpickr-current-month { font-size: 0.95rem; font-weight: 700; color: #fff !important; padding-top: 4px; }
            .flatpickr-current-month .flatpickr-monthDropdown-months {
                background: transparent; color: #fff; font-weight: 700; -webkit-appearance: none;
            }
            .flatpickr-current-month .flatpickr-monthDropdown-months option { background: #fff; color: #1a1d29; }
            .flatpickr-current-month input.cur-year { color: #fff !important; font-weight: 700; }
            .flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month {
                fill: #fff !important; color: #fff !important; padding: 6px 10px;
            }
            .flatpickr-months .flatpickr-prev-month:hover svg, .flatpickr-months .flatpickr-next-month:hover svg {
                fill: rgba(255,255,255,0.7) !important;
            }
            .flatpickr-weekdays { background: linear-gradient(135deg, #667eea, #8b5cf6); }
            span.flatpickr-weekday {
                color: rgba(255,255,255,0.7) !important; font-weight: 600;
                font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.03em;
            }
            .flatpickr-innerContainer { padding: 4px; }
            .flatpickr-day {
                border-radius: 8px !important; font-weight: 500; font-size: 0.84rem;
                color: #374151; border: none; height: 36px; line-height: 36px;
                transition: all 0.15s;
            }
            .flatpickr-day:hover { background: #eef2ff; color: #667eea; }
            .flatpickr-day.today { background: rgba(102,126,234,0.1); color: #667eea; border: none; }
            .flatpickr-day.today:hover { background: rgba(102,126,234,0.2); }
            .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
                background: linear-gradient(135deg, #667eea, #8b5cf6) !important;
                color: #fff !important; border: none !important;
                box-shadow: 0 2px 8px rgba(102,126,234,0.35);
            }
            .flatpickr-day.inRange {
                background: rgba(102,126,234,0.1) !important;
                box-shadow: none !important; border: none !important; color: #667eea;
            }
            .flatpickr-day.flatpickr-disabled, .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay {
                color: #d1d5db !important;
            }
            .date-input-wrap { position: relative; }
            .date-input-wrap .form-control { padding-right: 2.5rem; cursor: pointer; }
            .date-input-wrap .flatpickr-icon {
                position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                color: var(--text-muted); pointer-events: none; font-size: 1.1rem; z-index: 2;
            }
        </style>
    </x-slot:styles>

    <style>
        .pr-tabs {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 6px; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem; border: 1.5px solid var(--border-light);
        }
        .pr-tabs .pr-tab {
            display: flex; align-items: center; gap: 8px;
            padding: 0.6rem 1.1rem; border-radius: 0.65rem; border: none;
            background: transparent; font-size: 0.82rem; font-weight: 600;
            color: var(--text-secondary); cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none; position: relative; overflow: hidden;
        }
        .pr-tabs .pr-tab::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            opacity: 0; transition: opacity 0.25s; border-radius: inherit;
        }
        .pr-tabs .pr-tab > * { position: relative; z-index: 1; }
        .pr-tabs .pr-tab:hover { background: #f4f5f8; color: var(--text-primary); }
        .pr-tabs .pr-tab.active::before { opacity: 1; }
        .pr-tabs .pr-tab.active { color: #fff; box-shadow: 0 4px 14px rgba(102,126,234,0.3); }
        .pr-tabs .pr-tab .tab-icon {
            width: 28px; height: 28px; border-radius: 0.4rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; background: rgba(0,0,0,0.04); transition: all 0.25s;
        }
        .pr-tabs .pr-tab.active .tab-icon { background: rgba(255,255,255,0.2); }

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

        .type-option {
            display: flex; align-items: center; gap: 10px;
            padding: 0.7rem 1rem; border-radius: 0.65rem;
            border: 1.5px solid var(--border-light); background: #fff;
            cursor: pointer; transition: all 0.2s;
        }
        .type-option:hover { border-color: var(--accent-primary); background: #fafaff; }
        .type-option:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .type-option input { display: none; }
        .type-option .to-icon {
            width: 34px; height: 34px; border-radius: 0.55rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.05rem; flex-shrink: 0;
            background: #f4f5f8; color: var(--text-muted); transition: all 0.2s;
        }
        .type-option:has(input:checked) .to-icon { background: rgba(102,126,234,0.12); color: var(--accent-primary); }
        .type-option .to-body { flex: 1; }
        .type-option .to-body .to-label { font-size: 0.85rem; font-weight: 600; }
        .type-option .to-body .to-desc { font-size: 0.7rem; color: var(--text-muted); }

        .day-chip {
            display: flex; align-items: center; gap: 6px;
            padding: 0.5rem 0.85rem; border-radius: 0.6rem;
            border: 1.5px solid var(--border-light); background: #fff;
            cursor: pointer; transition: all 0.2s; user-select: none;
        }
        .day-chip:hover { border-color: var(--accent-primary); }
        .day-chip:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .day-chip input { display: none; }
        .day-chip .dc-abbr {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.72rem; font-weight: 700;
            background: #f4f5f8; color: var(--text-muted); transition: all 0.2s;
        }
        .day-chip:has(input:checked) .dc-abbr { background: var(--accent-primary); color: #fff; }
        .day-chip .dc-name { font-size: 0.82rem; font-weight: 500; }

        .adj-display {
            display: flex; align-items: center; justify-content: center;
            padding: 1rem; border-radius: 0.75rem; min-height: 80px;
            background: linear-gradient(135deg, rgba(102,126,234,0.05), rgba(139,92,246,0.05));
            border: 1.5px solid rgba(102,126,234,0.12);
        }
        .adj-display .adj-value {
            font-size: 1.8rem; font-weight: 800; color: var(--accent-primary);
            font-family: 'SFMono-Regular', 'Consolas', monospace;
        }
        .adj-display .adj-suffix { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); margin-left: 4px; }

        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border-light); margin-top: 1.5rem;
        }
    </style>

    <form action="{{ $isEdit ? route('admin.pricing-rules.update', $pricingRule) : route('admin.pricing-rules.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Tab Navigation --}}
        <div class="pr-tabs" role="tablist">
            <a class="pr-tab active" data-bs-toggle="tab" href="#pr-rule" role="tab">
                <span class="tab-icon"><i class='bx bx-dollar-circle'></i></span>
                <span>Rule Setup</span>
            </a>
            <a class="pr-tab" data-bs-toggle="tab" href="#pr-scope" role="tab">
                <span class="tab-icon"><i class='bx bx-target-lock'></i></span>
                <span>Scope & Targets</span>
            </a>
            <a class="pr-tab" data-bs-toggle="tab" href="#pr-schedule" role="tab">
                <span class="tab-icon"><i class='bx bx-calendar'></i></span>
                <span>Schedule</span>
            </a>
        </div>

        <div class="tab-content">
            {{-- ═══ RULE SETUP ═══ --}}
            <div class="tab-pane fade show active" id="pr-rule">
                <div class="row g-3">
                    {{-- Rule Identity --}}
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                        <i class='bx bx-info-circle'></i>
                                    </div>
                                    <div>
                                        <h6>Rule Identity</h6>
                                        <p>Name and type of this pricing rule</p>
                                    </div>
                                </div>
                                <x-form.input name="name" label="Rule Name" :value="old('name', $pricingRule->name ?? '')" required placeholder="e.g. Summer Peak Season +20%" class="mb-3" />

                                @php
                                    $types = [
                                        'domain_markup' => ['label' => 'Domain Markup', 'desc' => 'Apply markup per domain', 'icon' => 'bx-globe', 'bg' => 'rgba(102,126,234,0.08)', 'color' => '#667eea'],
                                        'seasonal' => ['label' => 'Seasonal', 'desc' => 'Seasonal price adjustment', 'icon' => 'bx-sun', 'bg' => 'rgba(245,158,11,0.08)', 'color' => '#f59e0b'],
                                        'date_range' => ['label' => 'Date Range', 'desc' => 'Specific date period', 'icon' => 'bx-calendar-event', 'bg' => 'rgba(239,68,68,0.08)', 'color' => '#ef4444'],
                                        'category' => ['label' => 'Category', 'desc' => 'By room or hotel type', 'icon' => 'bx-category', 'bg' => 'rgba(107,114,128,0.08)', 'color' => '#6b7280'],
                                        'day_of_week' => ['label' => 'Day of Week', 'desc' => 'Weekend / weekday rates', 'icon' => 'bx-calendar-week', 'bg' => 'rgba(118,75,162,0.08)', 'color' => '#764ba2'],
                                    ];
                                    $selectedType = old('type', $pricingRule->type ?? '');
                                @endphp

                                <label class="form-label" style="font-size: 0.82rem; font-weight: 600;">Rule Type <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    @foreach($types as $typeVal => $typeMeta)
                                    <div class="col-md-6">
                                        <label class="type-option w-100">
                                            <input type="radio" name="type" value="{{ $typeVal }}" {{ $selectedType === $typeVal ? 'checked' : '' }} required>
                                            <div class="to-icon" style="background: {{ $typeMeta['bg'] }}; color: {{ $typeMeta['color'] }};">
                                                <i class='bx {{ $typeMeta['icon'] }}'></i>
                                            </div>
                                            <div class="to-body">
                                                <div class="to-label">{{ $typeMeta['label'] }}</div>
                                                <div class="to-desc">{{ $typeMeta['desc'] }}</div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('type')<div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Adjustment --}}
                        <div class="card mt-3">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                        <i class='bx bx-trending-up'></i>
                                    </div>
                                    <div>
                                        <h6>Price Adjustment</h6>
                                        <p>How the price should change when this rule applies</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <x-form.select name="adjustment_type" label="Adjustment Type" :options="[
                                        'percentage' => 'Percentage (%)',
                                        'fixed_amount' => 'Fixed Amount (AED)',
                                    ]" :selected="old('adjustment_type', $pricingRule->adjustment_type ?? 'percentage')" required class="col-md-6" />
                                    <x-form.input name="adjustment_value" label="Value" type="number" step="0.01" :value="old('adjustment_value', $pricingRule->adjustment_value ?? '')" required placeholder="e.g. 15 or -10" class="col-md-6" />
                                </div>

                                {{-- Live Preview --}}
                                <div class="adj-display mt-3">
                                    <span class="adj-value" id="adjPreviewValue">{{ old('adjustment_value', $pricingRule->adjustment_value ?? '0') }}</span>
                                    <span class="adj-suffix" id="adjPreviewSuffix">{{ old('adjustment_type', $pricingRule->adjustment_type ?? 'percentage') === 'percentage' ? '%' : 'AED' }}</span>
                                </div>
                                <div class="text-center mt-2" style="font-size: 0.75rem; color: var(--text-muted);">
                                    Positive values increase price, negative values decrease
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
                                        <h6>Settings</h6>
                                        <p>Status and execution order</p>
                                    </div>
                                </div>
                                <div class="toggle-card mb-3">
                                    <div class="toggle-label">
                                        <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                        <span>Active</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                               {{ old('is_active', $isEdit ? $pricingRule->is_active : true) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <x-form.input name="priority" label="Priority" type="number" :value="old('priority', $pricingRule->priority ?? 0)" min="0" help="Higher priority rules execute first" />
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
                                        <h6>How Rules Work</h6>
                                    </div>
                                </div>
                                <div style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.7;">
                                    <p class="mb-2"><strong>Execution order:</strong> Rules are applied by priority (highest first).</p>
                                    <p class="mb-2"><strong>Scope:</strong> A rule with a hotel target only applies to that hotel. Leave targets blank for global rules.</p>
                                    <p class="mb-0"><strong>Stacking:</strong> Multiple matching rules stack their adjustments on top of the base price.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ SCOPE & TARGETS ═══ --}}
            <div class="tab-pane fade" id="pr-scope">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(118,75,162,0.1); color: var(--accent-secondary);">
                                <i class='bx bx-target-lock'></i>
                            </div>
                            <div>
                                <h6>Apply To</h6>
                                <p>Leave all blank for a global rule, or narrow down to specific targets</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.select2-ajax name="domain_id" label="Domain" :url="route('admin.api.search.domains')" :selected="$pricingRule->domain_id" :selectedText="$pricingRule->domain?->name" placeholder="All Domains" class="col-md-6" />
                            <x-form.select2-ajax name="hotel_id" label="Hotel" :url="route('admin.api.search.hotels')" :selected="$pricingRule->hotel_id" :selectedText="$pricingRule->hotel?->name" placeholder="All Hotels" class="col-md-6" />
                            <x-form.select2-ajax name="room_type_id" label="Room Type" :url="route('admin.api.search.room-types')" :selected="$pricingRule->room_type_id" :selectedText="$pricingRule->roomType?->name" placeholder="All Room Types" class="col-md-6" />
                            <x-form.select2-ajax name="location_id" label="Location" :url="route('admin.api.search.locations')" :selected="$pricingRule->location_id" :selectedText="$pricingRule->location?->name" placeholder="All Locations" class="col-md-6" />
                        </div>

                        {{-- Scope Summary --}}
                        <div class="mt-3 p-3" style="background: rgba(102,126,234,0.04); border-radius: 0.65rem; border: 1px solid rgba(102,126,234,0.1);">
                            <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0; line-height: 1.6;">
                                <i class='bx bx-info-circle me-1' style="color: var(--accent-primary);"></i>
                                Only select targets that are relevant. For example, a <strong>Domain Markup</strong> rule typically only needs a Domain target.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ SCHEDULE ═══ --}}
            <div class="tab-pane fade" id="pr-schedule">
                <div class="row g-3">
                    {{-- Date Range --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                                        <i class='bx bx-calendar-event'></i>
                                    </div>
                                    <div>
                                        <h6>Date Range</h6>
                                        <p>When this rule is effective</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <div class="date-input-wrap">
                                            <input type="text" name="start_date" id="start_date" class="form-control flatpickr-date @error('start_date') is-invalid @enderror"
                                                   value="{{ old('start_date', $isEdit ? $pricingRule->start_date?->format('Y-m-d') : '') }}"
                                                   placeholder="Select start date" autocomplete="off" readonly>
                                            <i class='bx bx-calendar flatpickr-icon'></i>
                                        </div>
                                        @error('start_date')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <div class="date-input-wrap">
                                            <input type="text" name="end_date" id="end_date" class="form-control flatpickr-date @error('end_date') is-invalid @enderror"
                                                   value="{{ old('end_date', $isEdit ? $pricingRule->end_date?->format('Y-m-d') : '') }}"
                                                   placeholder="Select end date" autocomplete="off" readonly>
                                            <i class='bx bx-calendar flatpickr-icon'></i>
                                        </div>
                                        @error('end_date')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="mt-2" style="font-size: 0.75rem; color: var(--text-muted);">
                                    Leave empty for an always-active rule (no date restriction).
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Days of Week --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <div class="sh-icon" style="background: rgba(118,75,162,0.1); color: var(--accent-secondary);">
                                        <i class='bx bx-calendar-week'></i>
                                    </div>
                                    <div>
                                        <h6>Days of Week</h6>
                                        <p>Restrict to specific days</p>
                                    </div>
                                </div>

                                @php
                                    $daysMeta = [
                                        'Monday' => 'Mo', 'Tuesday' => 'Tu', 'Wednesday' => 'We',
                                        'Thursday' => 'Th', 'Friday' => 'Fr', 'Saturday' => 'Sa', 'Sunday' => 'Su',
                                    ];
                                @endphp

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($daysMeta as $day => $abbr)
                                    <label class="day-chip">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $day }}"
                                               {{ is_array($selectedDays) && in_array($day, $selectedDays) ? 'checked' : '' }}>
                                        <span class="dc-abbr">{{ $abbr }}</span>
                                        <span class="dc-name">{{ $day }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @error('days_of_week')<div class="text-danger mt-2" style="font-size: 0.78rem;">{{ $message }}</div>@enderror

                                <div class="mt-3 d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleDays(true)">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleDays(false)">Clear All</button>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="selectWeekend()">Weekend Only</button>
                                </div>
                                <div class="mt-2" style="font-size: 0.75rem; color: var(--text-muted);">
                                    Leave all unchecked to apply every day.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.pricing-rules.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Rule' : 'Create Rule' }}
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
                        if (selectedDates.length > 0) {
                            endPicker.set('minDate', selectedDates[0]);
                        } else {
                            endPicker.set('minDate', null);
                        }
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
                        if (selectedDates.length > 0) {
                            startPicker.set('maxDate', selectedDates[0]);
                        } else {
                            startPicker.set('maxDate', null);
                        }
                    }
                });

                // Set initial constraints if values exist
                if (startInput.value) {
                    endPicker.set('minDate', startInput.value);
                }
                if (endInput.value) {
                    startPicker.set('maxDate', endInput.value);
                }
            })();

            // Live adjustment preview
            const adjValueInput = document.querySelector('input[name="adjustment_value"]');
            const adjTypeSelect = document.querySelector('select[name="adjustment_type"]');
            const adjPreviewValue = document.getElementById('adjPreviewValue');
            const adjPreviewSuffix = document.getElementById('adjPreviewSuffix');

            function updateAdjPreview() {
                const val = adjValueInput.value || '0';
                const sign = parseFloat(val) >= 0 ? '+' : '';
                adjPreviewValue.textContent = sign + val;
                adjPreviewSuffix.textContent = adjTypeSelect.value === 'percentage' ? '%' : ' AED';

                // Color: green for positive, red for negative
                const isPositive = parseFloat(val) >= 0;
                adjPreviewValue.style.color = isPositive ? 'var(--accent-success)' : '#ef4444';
            }

            if (adjValueInput) adjValueInput.addEventListener('input', updateAdjPreview);
            if (adjTypeSelect) adjTypeSelect.addEventListener('change', updateAdjPreview);
            updateAdjPreview();

            // Day quick actions
            function toggleDays(checked) {
                document.querySelectorAll('input[name="days_of_week[]"]').forEach(cb => cb.checked = checked);
            }
            function selectWeekend() {
                document.querySelectorAll('input[name="days_of_week[]"]').forEach(cb => {
                    cb.checked = ['Friday', 'Saturday', 'Sunday'].includes(cb.value);
                });
            }
        </script>
    </x-slot:scripts>
</x-admin-layout>
