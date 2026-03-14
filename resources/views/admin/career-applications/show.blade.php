<x-admin-layout title="Application Details" :pageTitle="'Application: ' . $careerApplication->name">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.careers.index') }}">Careers</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.career-applications.index') }}">Applications</a></li>
        <li class="breadcrumb-item active">{{ $careerApplication->name }}</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }

        .card { border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04); }
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

        .detail-row {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;
        }
        .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
        .detail-row .dr-icon {
            width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1rem;
        }
        .detail-row .dr-content { flex: 1; }
        .detail-row .dr-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: var(--text-muted); font-weight: 700; margin-bottom: 2px; }
        .detail-row .dr-value { font-size: .88rem; color: var(--text-primary); font-weight: 500; }
        .detail-row .dr-value a { color: var(--accent-primary); text-decoration: none; }
        .detail-row .dr-value a:hover { text-decoration: underline; }

        .contact-action {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .5rem 1rem; border-radius: 8px; border: 1.5px solid var(--border-light);
            background: #fff; font-size: .82rem; font-weight: 600;
            color: var(--text-secondary); text-decoration: none; transition: all .2s;
        }
        .contact-action:hover { border-color: var(--accent-primary); color: var(--accent-primary); background: rgba(79,70,229,.04); }
        .contact-action i { font-size: 1rem; }

        .status-select {
            padding: .45rem .75rem; border-radius: 8px; border: 1.5px solid var(--border-light);
            font-size: .85rem; font-weight: 600; cursor: pointer; transition: border-color .2s;
        }
        .status-select:focus { border-color: var(--accent-primary); outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }

        .cover-letter-content {
            background: #f8fafc; border-radius: 10px; padding: 1.25rem;
            font-size: .88rem; line-height: 1.65; color: var(--text-secondary);
            border: 1px solid #e2e8f0;
        }
    </style>

    <div class="row g-3">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Applicant Info --}}
            <div class="card">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                            <i class='bx bx-user'></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6>{{ $careerApplication->name }}</h6>
                            <p>Applicant Details</p>
                        </div>
                        @php
                            $statusColors = ['new' => 'primary', 'reviewed' => 'info', 'shortlisted' => 'success', 'rejected' => 'danger'];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$careerApplication->status] ?? 'secondary' }}" style="font-size: .8rem; padding: .4rem .8rem;">
                            {{ ucfirst($careerApplication->status) }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <div class="dr-icon" style="background: rgba(79,70,229,.08); color: var(--accent-primary);">
                            <i class='bx bx-envelope'></i>
                        </div>
                        <div class="dr-content">
                            <div class="dr-label">Email</div>
                            <div class="dr-value"><a href="mailto:{{ $careerApplication->email }}">{{ $careerApplication->email }}</a></div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="dr-icon" style="background: rgba(5,150,105,.08); color: var(--accent-success);">
                            <i class='bx bx-phone'></i>
                        </div>
                        <div class="dr-content">
                            <div class="dr-label">Phone</div>
                            <div class="dr-value"><a href="tel:{{ $careerApplication->phone }}">{{ $careerApplication->phone }}</a></div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="dr-icon" style="background: rgba(8,145,178,.08); color: var(--accent-info);">
                            <i class='bx bx-calendar'></i>
                        </div>
                        <div class="dr-content">
                            <div class="dr-label">Applied On</div>
                            <div class="dr-value">{{ $careerApplication->created_at->format('F j, Y \a\t g:i A') }} ({{ $careerApplication->created_at->diffForHumans() }})</div>
                        </div>
                    </div>

                    {{-- Quick Contact Buttons --}}
                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <a href="mailto:{{ $careerApplication->email }}" class="contact-action">
                            <i class='bx bx-envelope'></i> Send Email
                        </a>
                        <a href="tel:{{ $careerApplication->phone }}" class="contact-action">
                            <i class='bx bx-phone-call'></i> Call
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $careerApplication->phone) }}" target="_blank" class="contact-action">
                            <i class='bx bxl-whatsapp'></i> WhatsApp
                        </a>
                        @if($careerApplication->resume_path)
                        <a href="{{ asset('storage/' . $careerApplication->resume_path) }}" target="_blank" class="contact-action">
                            <i class='bx bx-download'></i> Download Resume
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cover Letter --}}
            @if($careerApplication->cover_letter)
            <div class="card mt-3">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(99,102,241,.1); color: #6366f1;">
                            <i class='bx bx-message-detail'></i>
                        </div>
                        <div>
                            <h6>Cover Letter</h6>
                            <p>Applicant's message</p>
                        </div>
                    </div>
                    <div class="cover-letter-content">
                        {!! nl2br(e($careerApplication->cover_letter)) !!}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Update Status --}}
            <div class="card">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                            <i class='bx bx-transfer-alt'></i>
                        </div>
                        <div>
                            <h6>Update Status</h6>
                            <p>Change application status</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.career-applications.update', $careerApplication) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select status-select mb-3">
                            <option value="new" {{ $careerApplication->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="reviewed" {{ $careerApplication->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="shortlisted" {{ $careerApplication->status === 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="rejected" {{ $careerApplication->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class='bx bx-check me-1'></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- Job Info --}}
            <div class="card mt-3">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(102,126,234,.1); color: var(--accent-primary);">
                            <i class='bx bx-briefcase'></i>
                        </div>
                        <div>
                            <h6>Applied For</h6>
                        </div>
                    </div>

                    @if($careerApplication->career)
                    <div style="font-size: 0.82rem; color: var(--text-muted);">
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                            <span>Position</span>
                            <strong style="color: var(--text-secondary);">{{ $careerApplication->career->title }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                            <span>Department</span>
                            <strong style="color: var(--text-secondary);">{{ $careerApplication->career->department }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                            <span>Location</span>
                            <strong style="color: var(--text-secondary);">{{ $careerApplication->career->location }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                            <span>Type</span>
                            @php
                                $typeLabels = ['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Contract', 'internship' => 'Internship'];
                            @endphp
                            <span class="badge bg-info">{{ $typeLabels[$careerApplication->career->job_type] ?? $careerApplication->career->job_type }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Last Date</span>
                            <strong style="color: var(--text-secondary);">{{ $careerApplication->career->last_apply_date->format('M j, Y') }}</strong>
                        </div>
                    </div>
                    <a href="{{ route('admin.careers.edit', $careerApplication->career) }}" class="btn btn-outline-primary btn-sm w-100 mt-3">
                        <i class='bx bx-edit-alt me-1'></i> View Career Posting
                    </a>
                    @endif
                </div>
            </div>

            {{-- Domain --}}
            <div class="card mt-3">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="sh-icon" style="background: rgba(0,176,255,.1); color: var(--accent-info);">
                            <i class='bx bx-globe'></i>
                        </div>
                        <div>
                            <h6>Source Domain</h6>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary" style="font-size: .8rem;">{{ $careerApplication->domain->name ?? 'N/A' }}</span>
                        @if($careerApplication->domain)
                        <span class="text-muted" style="font-size: .78rem;">{{ $careerApplication->domain->domain ?? '' }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-3">
        <a href="{{ route('admin.career-applications.index') }}" class="btn btn-light px-4">
            <i class='bx bx-arrow-back me-1'></i> Back to Applications
        </a>
    </div>
</x-admin-layout>
