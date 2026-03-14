<x-admin-layout :title="'Booking #' . $booking->reference_number" pageTitle="Booking Details">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
        <li class="breadcrumb-item active">#{{ $booking->reference_number }}</li>
    </x-slot:breadcrumb>

    <x-slot:actions>
        @php
            $statusColors = ['pending'=>'warning','paid'=>'info','confirmed'=>'success','cancelled'=>'danger','refunded'=>'secondary'];
        @endphp
        <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }} fs-6">{{ ucfirst($booking->status) }}</span>
    </x-slot:actions>

    <div class="row g-4">
        {{-- Booking Summary --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class='bx bx-receipt me-1'></i> Booking #{{ $booking->reference_number }}</h6>
                    <span class="text-muted small">{{ $booking->created_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Guest Information --}}
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class='bx bx-user me-1'></i> Guest Information</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><th width="120">Name</th><td>{{ $booking->guest_full_name }}</td></tr>
                                <tr><th>Email</th><td><a href="mailto:{{ $booking->guest_email }}">{{ $booking->guest_email }}</a></td></tr>
                                <tr><th>Phone</th><td>{{ $booking->guest_phone ?? '-' }}</td></tr>
                                <tr><th>Nationality</th><td>{{ $booking->guest_nationality ?? '-' }}</td></tr>
                            </table>
                        </div>

                        {{-- Stay Details --}}
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3"><i class='bx bx-building-house me-1'></i> Stay Details</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><th width="120">Hotel</th><td>{{ $booking->hotel?->name }}</td></tr>
                                <tr><th>Room Type</th><td>{{ $booking->roomType?->name ?? '-' }}</td></tr>
                                <tr><th>Check-in</th><td>{{ $booking->check_in_date?->format('M d, Y') }}</td></tr>
                                <tr><th>Check-out</th><td>{{ $booking->check_out_date?->format('M d, Y') }}</td></tr>
                            </table>
                        </div>

                        {{-- Occupancy --}}
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold text-primary">{{ $booking->num_nights }}</div>
                                        <small class="text-muted">Night{{ $booking->num_nights != 1 ? 's' : '' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold text-primary">{{ $booking->num_rooms }}</div>
                                        <small class="text-muted">Room{{ $booking->num_rooms != 1 ? 's' : '' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold text-primary">{{ $booking->num_adults }}</div>
                                        <small class="text-muted">Adult{{ $booking->num_adults != 1 ? 's' : '' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="fs-4 fw-bold text-primary">{{ $booking->num_children }}</div>
                                        <small class="text-muted">Child{{ $booking->num_children != 1 ? 'ren' : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Special Requests --}}
                        @if($booking->special_requests)
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase small fw-bold mb-2"><i class='bx bx-message-detail me-1'></i> Special Requests</h6>
                            <div class="bg-light rounded p-3">
                                {{ $booking->special_requests }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment History --}}
            @if($booking->payments && $booking->payments->count())
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class='bx bx-credit-card me-1'></i> Payment History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->payments as $payment)
                                <tr>
                                    <td><code>{{ $payment->transaction_id }}</code></td>
                                    @php
                                        $methodLabels = ['cash'=>'Cash','bank_transfer'=>'Bank Transfer','card'=>'Card (POS)','cheque'=>'Cheque','mashreq'=>'Mashreq Gateway'];
                                    @endphp
                                    <td><span class="badge bg-light text-dark">{{ $methodLabels[$payment->payment_method] ?? ucfirst($payment->payment_method ?? '-') }}</span></td>
                                    <td>
                                        @php
                                            $paymentStatusColors = ['completed'=>'success','pending'=>'warning','failed'=>'danger','refunded'=>'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $paymentStatusColors[$payment->status] ?? 'secondary' }}">{{ ucfirst($payment->status) }}</span>
                                    </td>
                                    <td class="text-end fw-bold">AED {{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar: Pricing & Actions --}}
        <div class="col-lg-4">
            {{-- Price Breakdown --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class='bx bx-money me-1'></i> Price Breakdown</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Room Price / Night</td>
                            <td class="text-end">AED {{ number_format($booking->room_price_per_night, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Subtotal <small class="text-muted">({{ $booking->num_nights }} night{{ $booking->num_nights != 1 ? 's' : '' }} x {{ $booking->num_rooms }} room{{ $booking->num_rooms != 1 ? 's' : '' }})</small></td>
                            <td class="text-end">AED {{ number_format($booking->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax <small class="text-muted">({{ $booking->tax_percentage }}%)</small></td>
                            <td class="text-end">AED {{ number_format($booking->tax_amount, 2) }}</td>
                        </tr>
                        @if($booking->tourism_fee)
                        <tr>
                            <td>Tourism Fee</td>
                            <td class="text-end">AED {{ number_format($booking->tourism_fee, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="fw-bold fs-6">Total</td>
                            <td class="text-end fw-bold fs-6 text-success">AED {{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class='bx bx-transfer me-1'></i> Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <x-form.select name="status" :options="['pending'=>'Pending','confirmed'=>'Confirmed','cancelled'=>'Cancelled','refunded'=>'Refunded']" :selected="old('status', $booking->status)" />
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><i class='bx bx-check me-1'></i> Update Status</button>
                    </form>
                </div>
            </div>

            {{-- Cash Payment --}}
            @if(!in_array($booking->status, ['cancelled', 'refunded']) && !$booking->payments->where('status', 'completed')->count())
            <div class="card mt-4 border-success">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0 text-success"><i class='bx bx-money me-1'></i> Record Cash Payment</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Record cash payment received from the guest.</p>
                    <form action="{{ route('admin.bookings.cash-payment', $booking) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <x-form.select name="payment_method" label="Payment Method" :options="['cash'=>'Cash','bank_transfer'=>'Bank Transfer','card'=>'Card (POS)','cheque'=>'Cheque']" selected="cash" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Amount (AED)</label>
                            <input type="number" name="amount" class="form-control form-control-sm" step="0.01" value="{{ $booking->total_amount }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Notes <span class="text-muted">(optional)</span></label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Receipt #123, Transfer ref...">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100"><i class='bx bx-check me-1'></i> Confirm Payment</button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Cancel Booking --}}
            @if(!in_array($booking->status, ['cancelled', 'refunded']))
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger bg-opacity-10">
                    <h6 class="mb-0 text-danger"><i class='bx bx-x-circle me-1'></i> Cancel Booking</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">This action will cancel the booking and notify the guest.</p>
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class='bx bx-x me-1'></i> Cancel Booking
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Cancel Modal --}}
    @if(!in_array($booking->status, ['cancelled', 'refunded']))
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel"><i class='bx bx-error text-danger me-2'></i>Cancel Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to cancel booking <strong>#{{ $booking->reference_number }}</strong>?</p>
                        <x-form.textarea name="cancellation_reason" label="Cancellation Reason" :value="old('cancellation_reason')" rows="3" placeholder="Provide a reason for cancellation..." />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" data-confirm="Are you sure you want to cancel this booking?"><i class='bx bx-x me-1'></i> Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-admin-layout>
