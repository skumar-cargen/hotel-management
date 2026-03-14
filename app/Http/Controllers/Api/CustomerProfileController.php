<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerBookingResource;
use App\Models\Booking;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerProfileController extends Controller
{
    use ApiResponses;

    public function show(Request $request)
    {
        $customer = $request->user();

        return $this->successResponse([
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'nationality' => $customer->nationality,
            'avatar_url' => $this->avatarUrl($customer),
            'has_password' => $customer->hasPassword(),
            'is_google_user' => $customer->isGoogleUser(),
            'email_verified_at' => $customer->email_verified_at?->toIso8601String(),
            'created_at' => $customer->created_at?->toIso8601String(),
        ]);
    }

    public function update(Request $request)
    {
        $customer = $request->user();

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
        ]);

        $customer->update($validated);

        return $this->successResponse([
            'message' => 'Profile updated successfully.',
            'customer' => [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'phone' => $customer->phone,
                'nationality' => $customer->nationality,
                'avatar_url' => $this->avatarUrl($customer),
            ],
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $customer = $request->user();

        // Delete old avatar if exists
        if ($customer->avatar && Storage::disk('public')->exists($customer->avatar)) {
            Storage::disk('public')->delete($customer->avatar);
        }

        $path = $request->file('avatar')->store('customers/avatars', 'public');
        $customer->update(['avatar' => $path]);

        return $this->successResponse([
            'message' => 'Avatar uploaded successfully.',
            'avatar_url' => $this->avatarUrl($customer->fresh()),
        ]);
    }

    public function changePassword(Request $request)
    {
        $customer = $request->user();

        $rules = [
            'new_password' => 'required|string|min:8|confirmed',
        ];

        // If customer already has a password, require current password
        if ($customer->hasPassword()) {
            $rules['current_password'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Verify current password if customer has one
        if ($customer->hasPassword()) {
            if (! Hash::check($validated['current_password'], $customer->password)) {
                return $this->errorResponse('Current password is incorrect.', 422);
            }
        }

        $customer->update(['password' => $validated['new_password']]);

        return $this->successResponse(['message' => 'Password changed successfully.']);
    }

    public function bookings(Request $request)
    {
        $customer = $request->user();
        $domain = $this->domain();

        $query = $customer->bookings()
            ->where('domain_id', $domain->id)
            ->with(['hotel', 'roomType'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate($request->input('per_page', 10));

        return $this->paginatedResponse(
            CustomerBookingResource::collection($bookings)
        );
    }

    public function bookingDetail(Request $request, string $reference)
    {
        $customer = $request->user();
        $domain = $this->domain();

        $booking = Booking::where('reference_number', $reference)
            ->where('customer_id', $customer->id)
            ->where('domain_id', $domain->id)
            ->with(['hotel', 'roomType', 'payments'])
            ->first();

        if (! $booking) {
            return $this->errorResponse('Booking not found.', 404);
        }

        return $this->successResponse(new CustomerBookingResource($booking));
    }

    private function avatarUrl($customer): ?string
    {
        if (! $customer->avatar) {
            return null;
        }

        // Google avatar URLs are already absolute
        if (str_starts_with($customer->avatar, 'http')) {
            return $customer->avatar;
        }

        return url('storage/'.$customer->avatar);
    }

    public function deleteAccount(Request $request)
    {
        $customer = $request->user();

        // Revoke all tokens
        $customer->tokens()->delete();

        // Soft delete the customer
        $customer->update(['is_active' => false]);
        $customer->delete();

        return $this->successResponse(['message' => 'Your account has been deleted.']);
    }
}
