<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyMembership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\LoyaltyTransaction;

class CustomerAuthController extends Controller
{
    private function customerPhotoUrl($customer): ?string
    {
        return $customer->photo_path
            ? url(Storage::url($customer->photo_path))
            : null;
    }

    public function activate(Request $request)
    {
        $validated = $request->validate([
            'membership_code' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|max:2048',
        ]);

        $membership = LoyaltyMembership::with('customer')
            ->where('membership_code', $validated['membership_code'])
            ->first();

        if (!$membership) {
            return response()->json([
                'message' => 'Invalid membership code.',
            ], 404);
        }

        $customer = $membership->customer;

        if (!$customer) {
            return response()->json([
                'message' => 'Customer record not found.',
            ], 404);
        }

        if ($membership->status !== 'active') {
            return response()->json([
                'message' => 'This membership is not active.',
            ], 403);
        }

        if (
            $membership->expires_at &&
            $membership->expires_at->isPast()
        ) {
            return response()->json([
                'message' => 'This membership has expired.',
            ], 403);
        }

        if ($customer->phone !== $validated['phone']) {
            return response()->json([
                'message' => 'The phone number does not match our records.',
            ], 422);
        }

        if ($customer->user_id) {
            return response()->json([
                'message' => 'This membership already has an activated account.',
            ], 409);
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('customer-photos', 'public')
            : null;

        try {
            $user = DB::transaction(function () use ($validated, $customer, $photoPath) {

                $user = User::create([
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'customer',
                ]);

                $updates = [
                    'user_id' => $user->id,
                ];

                if ($photoPath) {
                    $updates['photo_path'] = $photoPath;
                }

                $customer->update($updates);

                return $user;
            });
        } catch (\Throwable $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $e;
        }

        $token = $user->createToken('customer-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Account activated successfully.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])
            ->where('role', 'customer')
            ->first();

        if (
            !$user ||
            !Hash::check($validated['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('customer-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function membership(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'customer') {
        return response()->json([
            'message' => 'Unauthorized.',
        ], 403);
    }

    $customer = $user->customer()
        ->with('loyaltyMembership.loyaltyPlan')
        ->first();

    if (!$customer) {
        return response()->json([
            'message' => 'Customer profile not found.',
        ], 404);
    }

    $membership = $customer->loyaltyMembership;

    if (!$membership) {
        return response()->json([
            'message' => 'Loyalty membership not found.',
        ], 404);
    }

    return response()->json([
        'customer' => [
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'phone' => $customer->phone,
            'photo_url' => $this->customerPhotoUrl($customer),
        ],

        'membership' => [
            'membership_code' => $membership->membership_code,
            'status' => $membership->status,
            'activated_at' => $membership->activated_at?->toISOString(),
            'expires_at' => $membership->expires_at?->toISOString(),
            'qr_token' => $membership->qr_token,

            'plan' => [
                'name' => $membership->loyaltyPlan?->name,
                'discount_percentage' =>
                    $membership->loyaltyPlan?->discount_percentage,
                'minimum_spend' =>
                    $membership->loyaltyPlan?->minimum_spend,
            ],
        ],
    ]);
}

public function updateProfile(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'customer') {
        return response()->json([
            'message' => 'Unauthorized.',
        ], 403);
    }

    $customer = $user->customer;

    if (!$customer) {
        return response()->json([
            'message' => 'Customer profile not found.',
        ], 404);
    }

    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
        'photo' => 'nullable|image|max:2048',
    ]);

    $oldPhotoPath = $customer->photo_path;
    $newPhotoPath = null;

    if ($request->hasFile('photo')) {
        $newPhotoPath = $request->file('photo')->store('customer-photos', 'public');
        $validated['photo_path'] = $newPhotoPath;
    }

    unset($validated['photo']);

    try {
        $customer->update($validated);

        $user->update([
            'name' => $customer->first_name . ' ' . $customer->last_name,
        ]);
    } catch (\Throwable $e) {
        if ($newPhotoPath) {
            Storage::disk('public')->delete($newPhotoPath);
        }

        throw $e;
    }

    if ($newPhotoPath && $oldPhotoPath) {
        Storage::disk('public')->delete($oldPhotoPath);
    }

    return response()->json([
        'message' => 'Profile updated successfully.',
        'customer' => [
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'phone' => $customer->phone,
            'photo_url' => $this->customerPhotoUrl($customer),
        ],
    ]);
}

public function transactions(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'customer') {
        return response()->json([
            'message' => 'Unauthorized.',
        ], 403);
    }

    $customer = $user->customer;

    if (!$customer) {
        return response()->json([
            'message' => 'Customer profile not found.',
        ], 404);
    }

    $transactions = LoyaltyTransaction::with([
        'items'
    ])
        ->where('customer_id', $customer->id)
        ->latest('transaction_date')
        ->get();

    return response()->json([
        'transactions' => $transactions->map(function ($transaction) {

            return [
                'id' => $transaction->id,

                'transaction_date' =>
                    $transaction->transaction_date?->toISOString(),

                'subtotal' =>
                    $transaction->subtotal,

                'discount_percentage' =>
                    $transaction->discount_percentage,

                'discount_amount' =>
                    $transaction->discount_amount,

                'promo_code' =>
                    $transaction->promo_code,

                'promo_discount_amount' =>
                    $transaction->promo_discount_amount,

                'total_amount' =>
                    $transaction->total_amount,

                'items' => $transaction->items->map(function ($item) {
                    return [
                        'service_name' =>
                            $item->service_name,

                        'original_price' =>
                            $item->original_price,

                        'session_count' =>
                            $item->session_count,

                        'sessions_redeemed' =>
                            $item->sessions_redeemed,

                        'is_package_redemption' =>
                            $item->is_package_redemption,

                        'discount_eligible' =>
                            $item->discount_eligible,

                        'discount_amount' =>
                            $item->discount_amount,

                        'final_price' =>
                            $item->final_price,
                    ];
                }),
            ];
        }),
    ]);
}
}
