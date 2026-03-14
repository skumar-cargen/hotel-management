<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomerLoginRequest;
use App\Http\Requests\Api\CustomerRegisterRequest;
use App\Models\Customer;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class CustomerAuthController extends Controller
{
    use ApiResponses;

    public function register(CustomerRegisterRequest $request)
    {
        $validated = $request->validated();

        $customer = Customer::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'email_verified_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $customer->createToken('customer-api')->plainTextToken;

        return $this->successResponse([
            'customer' => $this->customerData($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(CustomerLoginRequest $request)
    {
        $validated = $request->validated();

        $customer = Customer::where('email', $validated['email'])->first();

        if (! $customer || ! $customer->hasPassword() || ! Hash::check($validated['password'], $customer->password)) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        if (! $customer->is_active) {
            return $this->errorResponse('Your account has been deactivated.', 403);
        }

        $customer->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $customer->createToken('customer-api')->plainTextToken;

        return $this->successResponse([
            'customer' => $this->customerData($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function google(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->id_token);
        } catch (\Exception $e) {
            return $this->errorResponse('Invalid Google token.', 401);
        }

        $customer = Customer::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($customer) {
            // Link Google ID if not already set
            if (! $customer->google_id) {
                $customer->update(['google_id' => $googleUser->getId()]);
            }

            if (! $customer->is_active) {
                return $this->errorResponse('Your account has been deactivated.', 403);
            }
        } else {
            // Create new customer from Google data
            $nameParts = explode(' ', $googleUser->getName(), 2);
            $customer = Customer::create([
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        }

        $customer->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $customer->createToken('customer-api')->plainTextToken;

        return $this->successResponse([
            'customer' => $this->customerData($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(['message' => 'Logged out successfully.']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(['message' => 'Password reset link sent to your email.']);
        }

        return $this->errorResponse('Unable to send reset link. Please check your email address.', 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Customer $customer, string $password) {
                $customer->update([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ]);

                // Revoke all tokens on password reset
                $customer->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(['message' => 'Password has been reset successfully.']);
        }

        return $this->errorResponse('Unable to reset password. The token may be invalid or expired.', 422);
    }

    private function customerData(Customer $customer): array
    {
        $avatarUrl = null;
        if ($customer->avatar) {
            $avatarUrl = str_starts_with($customer->avatar, 'http')
                ? $customer->avatar
                : url('storage/'.$customer->avatar);
        }

        return [
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'avatar_url' => $avatarUrl,
        ];
    }
}
