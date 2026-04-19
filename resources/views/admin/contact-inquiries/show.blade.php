<x-admin-layout title="Contact Inquiry" pageTitle="Contact Inquiry">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.contact-inquiries.index') }}">Contact Inquiries</a></li>
        <li class="breadcrumb-item active">#{{ $contactInquiry->id }}</li>
    </x-slot:breadcrumb>

    <style>
        .detail-card { border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .detail-card .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; font-weight: 700; font-size: .9rem; border-radius: 16px 16px 0 0; }
        .detail-card .card-body { padding: 1.5rem; }
        .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; font-weight: 700; margin-bottom: .25rem; }
        .detail-value { font-size: .9rem; color: #111827; margin-bottom: 1rem; }
        .message-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.25rem; font-size: .9rem; line-height: 1.7; color: #374151; white-space: pre-wrap; }
    </style>

    <div class="mb-3">
        <a href="{{ route('admin.contact-inquiries.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class='bx bx-arrow-back me-1'></i> Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card detail-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class='bx bx-envelope me-2'></i>Message</span>
                    @if($contactInquiry->status->value === 'read')
                        <span class="badge bg-info">Read</span>
                    @elseif($contactInquiry->status->value === 'replied')
                        <span class="badge bg-success">Replied</span>
                    @else
                        <span class="badge bg-warning">New</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="detail-label">Subject</div>
                    <div class="detail-value fw-semibold" style="font-size: 1rem;">{{ $contactInquiry->subject ?? 'No Subject' }}</div>

                    <div class="detail-label">Message</div>
                    <div class="message-box">{{ $contactInquiry->message }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card detail-card mb-4">
                <div class="card-header"><i class='bx bx-user me-2'></i>Sender Details</div>
                <div class="card-body">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value">{{ $contactInquiry->name }}</div>

                    <div class="detail-label">Email</div>
                    <div class="detail-value">
                        <a href="mailto:{{ $contactInquiry->email }}">{{ $contactInquiry->email }}</a>
                    </div>

                    @if($contactInquiry->phone)
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">{{ $contactInquiry->phone }}</div>
                    @endif

                    <div class="detail-label">Domain</div>
                    <div class="detail-value">{{ $contactInquiry->domain->name ?? '-' }}</div>

                    @if($contactInquiry->hotel)
                    <div class="detail-label">Hotel</div>
                    <div class="detail-value">{{ $contactInquiry->hotel->name }}</div>
                    @endif

                    <div class="detail-label">IP Address</div>
                    <div class="detail-value mb-0">{{ $contactInquiry->ip_address ?? '-' }}</div>
                </div>
            </div>

            <div class="card detail-card">
                <div class="card-header"><i class='bx bx-cog me-2'></i>Update Status</div>
                <div class="card-body">
                    <form action="{{ route('admin.contact-inquiries.update', $contactInquiry) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select form-select-sm mb-3">
                            <option value="new" {{ $contactInquiry->status->value === 'new' ? 'selected' : '' }}>New</option>
                            <option value="read" {{ $contactInquiry->status->value === 'read' ? 'selected' : '' }}>Read</option>
                            <option value="replied" {{ $contactInquiry->status->value === 'replied' ? 'selected' : '' }}>Replied</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Update Status</button>
                    </form>

                    <div class="mt-3 pt-3 border-top">
                        <div class="detail-label">Submitted</div>
                        <div class="detail-value mb-0">{{ $contactInquiry->created_at?->format('M d, Y \a\t h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
