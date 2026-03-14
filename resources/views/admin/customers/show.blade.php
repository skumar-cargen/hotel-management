<x-admin-layout title="Customer: {{ $customer->full_name }}" pageTitle="Customer Details">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
        <li class="breadcrumb-item active">{{ $customer->full_name }}</li>
    </x-slot:breadcrumb>

    <style>
        .profile-header { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #1e40af 100%); border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; position: relative; overflow: hidden; }
        .profile-header::before { content: ''; position: absolute; top: -60%; right: -5%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%); }
        .profile-avatar { width: 72px; height: 72px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: #fff; flex-shrink: 0; position: relative; z-index: 1; overflow: hidden; }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-info { position: relative; z-index: 1; }
        .profile-info h4 { color: #fff; font-weight: 700; font-size: 1.25rem; margin: 0; }
        .profile-info p { color: rgba(255,255,255,0.5); font-size: .85rem; margin: .25rem 0 0; }
        .profile-badges { position: relative; z-index: 1; }

        .detail-card { border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .detail-card .card-header { background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-weight: 700; font-size: .85rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; padding: 1rem 1.25rem; }
        .detail-card .card-body { padding: 1.25rem; }
        .detail-row { display: flex; justify-content: space-between; padding: .5rem 0; border-bottom: 1px solid #f1f5f9; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-size: .8rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; }
        .detail-value { font-size: .875rem; color: #1e293b; font-weight: 500; }

        .booking-item { border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: .75rem; transition: border-color .2s; }
        .booking-item:hover { border-color: #94a3b8; }
    </style>

    <div class="profile-header d-flex align-items-center gap-3">
        <div class="profile-avatar">
            @if($customer->avatar)
                <img src="{{ Storage::disk('public')->url($customer->avatar) }}" alt="{{ $customer->full_name }}">
            @else
                {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-info flex-grow-1">
            <h4>{{ $customer->full_name }}</h4>
            <p>{{ $customer->email }}</p>
        </div>
        <div class="profile-badges d-flex gap-2 align-items-center">
            @if($customer->hasPassword())
                <span class="badge bg-primary">Email Auth</span>
            @endif
            @if($customer->isGoogleUser())
                <span class="badge bg-danger">Google</span>
            @endif
            @if($customer->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- Left: Profile + Bookings --}}
        <div class="col-lg-8">
            <div class="card detail-card mb-4">
                <div class="card-header">Profile Information</div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value">{{ $customer->full_name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $customer->email }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value">{{ $customer->phone ?? '—' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Nationality</span>
                        <span class="detail-value">{{ $customer->nationality ?? '—' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Bookings</span>
                        <span class="detail-value">{{ $customer->bookings_count }}</span>
                    </div>
                </div>
            </div>

            <div class="card detail-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Booking History</span>
                    <span class="badge bg-secondary">{{ $bookings->total() }} total</span>
                </div>
                <div class="card-body">
                    @forelse($bookings as $booking)
                        <div class="booking-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold text-decoration-none">{{ $booking->reference_number }}</a>
                                    <div class="text-muted" style="font-size: .8rem;">{{ $booking->hotel?->name }} &middot; {{ $booking->roomType?->name }}</div>
                                </div>
                                @php
                                    $statusColors = ['pending' => 'warning', 'confirmed' => 'info', 'paid' => 'success', 'cancelled' => 'danger', 'refunded' => 'secondary', 'completed' => 'success'];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">{{ ucfirst($booking->status) }}</span>
                            </div>
                            <div class="d-flex gap-3" style="font-size: .8rem; color: #64748b;">
                                <span><i class='bx bx-calendar me-1'></i>{{ $booking->check_in_date?->format('M d') }} - {{ $booking->check_out_date?->format('M d, Y') }}</span>
                                <span><i class='bx bx-globe me-1'></i>{{ $booking->domain?->name }}</span>
                                <span class="ms-auto fw-semibold text-dark">{{ $booking->currency }} {{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class='bx bx-calendar-x' style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">No bookings found</p>
                        </div>
                    @endforelse

                    @if($bookings->hasPages())
                        <div class="mt-3">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Status & Metadata --}}
        <div class="col-lg-4">
            <div class="card detail-card mb-4">
                <div class="card-header">Account Status</div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            @if($customer->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email Verified</span>
                        <span class="detail-value">
                            @if($customer->email_verified_at)
                                <span class="text-success"><i class='bx bx-check-circle'></i> {{ $customer->email_verified_at->format('M d, Y') }}</span>
                            @else
                                <span class="text-muted">Not verified</span>
                            @endif
                        </span>
                    </div>
                    <div class="mt-3">
                        <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_active" value="{{ $customer->is_active ? '0' : '1' }}">
                            <button type="submit" class="btn btn-{{ $customer->is_active ? 'danger' : 'success' }} btn-sm w-100">
                                <i class='bx bx-{{ $customer->is_active ? "block" : "check-circle" }} me-1'></i>
                                {{ $customer->is_active ? 'Deactivate Account' : 'Activate Account' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card detail-card mb-4">
                <div class="card-header">Authentication</div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Auth Methods</span>
                        <span class="detail-value">
                            @if($customer->hasPassword())
                                <span class="badge bg-primary bg-opacity-10 text-primary me-1">Email/Password</span>
                            @endif
                            @if($customer->isGoogleUser())
                                <span class="badge bg-danger bg-opacity-10 text-danger">Google</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="card detail-card">
                <div class="card-header">Metadata</div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Last Login</span>
                        <span class="detail-value">{{ $customer->last_login_at ? $customer->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Last IP</span>
                        <span class="detail-value">{{ $customer->last_login_ip ?? '—' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Registered</span>
                        <span class="detail-value">{{ $customer->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if($customer->deleted_at)
                    <div class="detail-row">
                        <span class="detail-label">Deleted</span>
                        <span class="detail-value text-danger">{{ $customer->deleted_at->format('M d, Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
