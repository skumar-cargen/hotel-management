@php
    $isEdit = isset($career) && $career->exists;
@endphp

<x-admin-layout :title="$isEdit ? 'Edit Career' : 'Create Career'" :pageTitle="$isEdit ? 'Edit Career: ' . $career->title : 'Create Career'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.careers.index') }}">Careers</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <x-slot:styles>
        <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet">
        <link href="{{ asset('vendor/summernote/css/summernote-bs5.min.css') }}" rel="stylesheet">
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
                font-size: 0.9rem; min-height: 200px;
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

        /* ── Description Tab Pills ────────────────────── */
        .desc-tabs {
            display: flex; flex-wrap: wrap; gap: 4px; padding: 4px;
            background: #f8fafc; border-radius: 10px; margin-bottom: 1.25rem;
            border: 1.5px solid var(--border-light);
        }
        .desc-tabs .dt-tab {
            display: flex; align-items: center; gap: 6px;
            padding: 0.5rem 0.9rem; border-radius: 8px; border: none;
            background: transparent; font-size: 0.78rem; font-weight: 600;
            color: var(--text-secondary); cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none; position: relative; overflow: hidden;
        }
        .desc-tabs .dt-tab::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            opacity: 0; transition: opacity 0.2s; border-radius: inherit;
        }
        .desc-tabs .dt-tab > * { position: relative; z-index: 1; }
        .desc-tabs .dt-tab:hover { background: #fff; color: var(--text-primary); }
        .desc-tabs .dt-tab.active::before { opacity: 1; }
        .desc-tabs .dt-tab.active { color: #fff; box-shadow: 0 4px 14px rgba(102,126,234,0.3); }
        .desc-tabs .dt-tab i { font-size: 0.95rem; }

        /* ── Sidebar Info Card ────────────────────────── */
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

        /* ── Job Type Preview ─────────────────────────── */
        .job-type-preview {
            display: flex; align-items: center; justify-content: center;
            padding: 0.85rem; border-radius: 0.75rem; min-height: 60px;
            background: linear-gradient(135deg, rgba(79,70,229,0.05), rgba(139,92,246,0.05));
            border: 1.5px solid rgba(79,70,229,0.12); margin-top: 0.75rem;
        }
        .job-type-preview .jtp-badge {
            font-size: 0.85rem; font-weight: 700; color: var(--accent-primary);
            display: flex; align-items: center; gap: 8px;
        }
        .jtp-badge i { font-size: 1.1rem; }

        /* ── Save Bar ─────────────────────────────────── */
        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 16px;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,.06);
            border: none; margin-top: 1.5rem;
        }
    </style>

    <form action="{{ $isEdit ? route('admin.careers.update', $career) : route('admin.careers.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
            {{-- ═══ Main Content ═══ --}}
            <div class="col-lg-8">
                {{-- Job Details --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-briefcase'></i>
                            </div>
                            <div>
                                <h6>Job Details</h6>
                                <p>Title, department, location, and type</p>
                            </div>
                        </div>

                        <x-form.input name="title" label="Title" :value="old('title', $career->title ?? '')" required placeholder="e.g. Senior Laravel Developer" class="mb-3" />

                        <div class="row g-3 mb-3">
                            <x-form.input name="department" label="Department" :value="old('department', $career->department ?? '')" required placeholder="e.g. Engineering" class="col-md-6" />
                            <x-form.input name="location" label="Location" :value="old('location', $career->location ?? '')" required placeholder="e.g. Dubai, UAE" class="col-md-6" />
                        </div>

                        <x-form.select name="job_type" label="Job Type" :options="[
                            'full_time' => 'Full Time',
                            'part_time' => 'Part Time',
                            'contract' => 'Contract',
                            'internship' => 'Internship',
                        ]" :selected="old('job_type', $career->job_type ?? 'full_time')" required class="col-12" />

                        {{-- Live Job Type Preview --}}
                        <div class="job-type-preview">
                            <div class="jtp-badge">
                                <i class='bx bx-briefcase-alt-2'></i>
                                <span id="jobTypePreview">{{ ['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','internship'=>'Internship'][old('job_type', $career->job_type ?? 'full_time')] }}</span>
                                <span style="color: var(--text-muted); font-weight: 500; font-size: .8rem;">
                                    &mdash; {{ old('location', $career->location ?? 'Location') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description (Tabbed) --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                                <i class='bx bx-detail'></i>
                            </div>
                            <div>
                                <h6>Description</h6>
                                <p>Detailed job description sections</p>
                            </div>
                        </div>

                        {{-- Tab Pills --}}
                        <div class="desc-tabs" role="tablist">
                            <a class="dt-tab active" data-bs-toggle="tab" href="#tab-about" role="tab">
                                <i class='bx bx-info-circle'></i>
                                <span>About Role</span>
                            </a>
                            <a class="dt-tab" data-bs-toggle="tab" href="#tab-responsibilities" role="tab">
                                <i class='bx bx-list-check'></i>
                                <span>Responsibilities</span>
                            </a>
                            <a class="dt-tab" data-bs-toggle="tab" href="#tab-requirements" role="tab">
                                <i class='bx bx-check-shield'></i>
                                <span>Requirements</span>
                            </a>
                            <a class="dt-tab" data-bs-toggle="tab" href="#tab-offer" role="tab">
                                <i class='bx bx-gift'></i>
                                <span>What We Offer</span>
                            </a>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab-about">
                                <label class="form-label">About the Role</label>
                                <textarea name="about_role" class="summernote-editor">{!! old('about_role', $career->about_role ?? '') !!}</textarea>
                            </div>
                            <div class="tab-pane fade" id="tab-responsibilities">
                                <label class="form-label">Responsibilities</label>
                                <textarea name="responsibilities" class="summernote-editor">{!! old('responsibilities', $career->responsibilities ?? '') !!}</textarea>
                            </div>
                            <div class="tab-pane fade" id="tab-requirements">
                                <label class="form-label">Requirements</label>
                                <textarea name="requirements" class="summernote-editor">{!! old('requirements', $career->requirements ?? '') !!}</textarea>
                            </div>
                            <div class="tab-pane fade" id="tab-offer">
                                <label class="form-label">What We Offer</label>
                                <textarea name="what_we_offer" class="summernote-editor">{!! old('what_we_offer', $career->what_we_offer ?? '') !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scope --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-globe'></i>
                            </div>
                            <div>
                                <h6>Domain Assignment</h6>
                                <p>Select which domains will display this career posting</p>
                            </div>
                        </div>
                        <x-form.select2-ajax name="domains" label="Domains" :url="route('admin.api.search.domains')" multiple placeholder="Search domains..." class="col-12" />
                    </div>
                </div>
            </div>

            {{-- ═══ Sidebar ═══ --}}
            <div class="col-lg-4">
                {{-- Settings --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-cog'></i>
                            </div>
                            <div>
                                <h6>Settings</h6>
                                <p>Status and application deadline</p>
                            </div>
                        </div>

                        <div class="toggle-card mb-3">
                            <div class="toggle-label">
                                <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                <span>Active</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                       {{ old('is_active', $isEdit ? $career->is_active : true) ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div>
                            <label for="last_apply_date" class="form-label">Last Apply Date <span class="text-danger">*</span></label>
                            <div class="date-input-wrap">
                                <input type="text" name="last_apply_date" id="last_apply_date" class="form-control flatpickr-date @error('last_apply_date') is-invalid @enderror"
                                       value="{{ old('last_apply_date', $isEdit ? $career->last_apply_date?->format('Y-m-d') : '') }}"
                                       placeholder="Select last apply date" autocomplete="off" readonly required>
                                <i class='bx bx-calendar flatpickr-icon'></i>
                            </div>
                            @error('last_apply_date')<div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Quick Stats (edit only) --}}
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
                                <span>Created</span>
                                <strong style="color: var(--text-secondary);">{{ $career->created_at->format('M j, Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Last Updated</span>
                                <strong style="color: var(--text-secondary);">{{ $career->updated_at->format('M j, Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Domains</span>
                                <strong style="color: var(--text-secondary);">{{ $career->domains->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span>Status</span>
                                @if($career->is_active && $career->last_apply_date->gte(now()->startOfDay()))
                                    <span class="badge bg-success" style="font-size: .7rem;">Open</span>
                                @elseif($career->is_active)
                                    <span class="badge bg-warning" style="font-size: .7rem;">Expired</span>
                                @else
                                    <span class="badge bg-danger" style="font-size: .7rem;">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Info --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                <i class='bx bx-bulb'></i>
                            </div>
                            <div>
                                <h6>How Careers Work</h6>
                            </div>
                        </div>
                        <div class="info-card-item">
                            <div class="ici-icon" style="background: rgba(102,126,234,0.08); color: var(--accent-primary);">
                                <i class='bx bx-show'></i>
                            </div>
                            <div class="ici-text">
                                <strong>Visibility</strong> &mdash; Postings show on frontend domains that are assigned to them.
                            </div>
                        </div>
                        <div class="info-card-item">
                            <div class="ici-icon" style="background: rgba(245,158,11,0.08); color: #f59e0b;">
                                <i class='bx bx-calendar-exclamation'></i>
                            </div>
                            <div class="ici-text">
                                <strong>Deadline</strong> &mdash; Only active postings with a future apply date are visible to visitors.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.careers.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Career' : 'Create Career' }}
            </button>
        </div>
    </form>

    <x-slot:scripts>
        <script src="{{ asset('assets/js/flatpickr.min.js') }}"></script>
        <script src="{{ asset('vendor/summernote/js/summernote-bs5.min.js') }}"></script>
        <script>
            $(function() {
                // Summernote config
                var summernoteOpts = {
                    height: 250,
                    placeholder: 'Write your content here...',
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

                // Init Summernote on the active tab first
                $('#tab-about .summernote-editor').summernote(summernoteOpts);

                // Init Summernote on other tabs when they become visible
                var initialized = { 'tab-about': true };
                $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                    var target = $(e.target).attr('href').substring(1);
                    if (!initialized[target]) {
                        $('#' + target + ' .summernote-editor').summernote(summernoteOpts);
                        initialized[target] = true;
                    }
                });

                // Flatpickr date picker
                flatpickr(document.getElementById('last_apply_date'), {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'M j, Y',
                    allowInput: false,
                    disableMobile: true,
                    appendTo: document.body
                });

                // Live job type preview
                var jobTypeSelect = document.querySelector('select[name="job_type"]');
                var previewText = document.getElementById('jobTypePreview');
                var labels = { full_time: 'Full Time', part_time: 'Part Time', contract: 'Contract', internship: 'Internship' };
                if (jobTypeSelect && previewText) {
                    jobTypeSelect.addEventListener('change', function() {
                        previewText.textContent = labels[this.value] || this.value;
                    });
                }

                // Pre-populate Select2 with existing domains on edit
                @if($isEdit && $career->domains->count())
                var $domainSelect = $('#select2-domains');
                @foreach($career->domains as $domain)
                $domainSelect.append(new Option(@json($domain->name), {{ $domain->id }}, true, true));
                @endforeach
                $domainSelect.trigger('change');
                @endif
            });
        </script>
    </x-slot:scripts>
</x-admin-layout>
