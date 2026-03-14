<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Payment;
use App\Traits\ScopesByDomain;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{
    use ScopesByDomain;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Booking::query()->with(['hotel', 'domain']);
            $this->scopeBookingsForUser($query);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search_custom')) {
                $search = $request->search_custom;
                $query->where(function ($q) use ($search) {
                    $q->where('reference_number', 'like', "%{$search}%")
                        ->orWhere('guest_first_name', 'like', "%{$search}%")
                        ->orWhere('guest_last_name', 'like', "%{$search}%")
                        ->orWhere('guest_email', 'like', "%{$search}%");
                });
            }

            return DataTables::of($query)
                ->addColumn('guest_name', function ($booking) {
                    return e($booking->guest_first_name.' '.$booking->guest_last_name);
                })
                ->addColumn('hotel_name', function ($booking) {
                    return $booking->hotel->name ?? '-';
                })
                ->addColumn('status', function ($booking) {
                    $colors = [
                        'pending' => 'warning',
                        'paid' => 'info',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'secondary',
                    ];
                    $color = $colors[$booking->status] ?? 'secondary';

                    return '<span class="badge bg-'.$color.'">'.ucfirst($booking->status).'</span>';
                })
                ->editColumn('check_in_date', function ($booking) {
                    return $booking->check_in_date ? $booking->check_in_date->format('M d, Y') : '-';
                })
                ->addColumn('amount_formatted', function ($booking) {
                    return number_format($booking->total_amount, 2).' AED';
                })
                ->addColumn('action', function ($booking) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.bookings.show', $booking).'"><i class="bx bx-show me-2"></i>View Details</a></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $domains = $this->userDomains();
        $hotels = Hotel::active();
        if (! auth()->user()->isAdmin()) {
            $hotels->whereIn('id', auth()->user()->managedHotelIds());
        }
        $hotels = $hotels->get();

        return view('admin.bookings.index', compact('domains', 'hotels'));
    }

    public function show(Booking $booking)
    {
        $this->authorizeBooking($booking);
        $booking->load(['hotel', 'domain', 'roomType', 'payments']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,confirmed,cancelled,refunded',
            'cancellation_reason' => 'nullable|string',
        ]);

        if ($validated['status'] === 'cancelled') {
            $validated['cancelled_at'] = now();
        }

        if ($validated['status'] === 'confirmed') {
            $validated['confirmed_at'] = now();
        }

        $booking->update($validated);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated successfully.');
    }

    public function refund(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);
        $booking->update(['status' => 'refunded']);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Refund processed successfully.');
    }

    public function markCashPaid(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        if (in_array($booking->status, ['cancelled', 'refunded'])) {
            return back()->with('error', 'Cannot record payment for cancelled or refunded bookings.');
        }

        if ($booking->payments()->where('status', 'completed')->exists()) {
            return back()->with('error', 'Payment has already been recorded for this booking.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,card,cheque',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $prefixes = ['cash' => 'CASH', 'bank_transfer' => 'BT', 'card' => 'POS', 'cheque' => 'CHQ'];
        $prefix = $prefixes[$validated['payment_method']] ?? 'PAY';

        Payment::create([
            'booking_id' => $booking->id,
            'transaction_id' => $prefix.'-'.strtoupper(uniqid()),
            'payment_method' => $validated['payment_method'],
            'gateway' => 'manual',
            'amount' => $validated['amount'],
            'currency' => $booking->currency ?? 'AED',
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_response' => json_encode([
                'type' => 'cash',
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->user()->name,
                'recorded_at' => now()->toIso8601String(),
            ]),
        ]);

        return back()->with('success', 'Cash payment recorded successfully.');
    }

    public function export(Request $request)
    {
        return back()->with('success', 'Export started.');
    }
}
