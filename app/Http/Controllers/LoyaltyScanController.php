<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyMembership;
use App\Models\PromoCode;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltyTransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoyaltyScanController extends Controller
{
    public function index()
    {
        return view('scanner.index');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        $membership = LoyaltyMembership::with([
            'customer',
            'loyaltyPlan',
        ])
        ->where('qr_token', $validated['qr_token'])
        ->first();

        if (!$membership) {
            return back()->with('error', 'Invalid loyalty QR code.');
        }

        if ($membership->status !== 'active') {
            return back()->with('error', 'This loyalty membership is not active.');
        }

        if (
            $membership->expires_at &&
            $membership->expires_at->isPast()
        ) {
            return back()->with('error', 'This loyalty membership has expired.');
        }

        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('scanner.result', compact('membership', 'services'));
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'membership_id' => 'required|exists:loyalty_memberships,id',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'custom_prices' => 'nullable|array',
            'custom_prices.*' => 'nullable|numeric|min:0',
            'package_modes' => 'nullable|array',
            'package_modes.*' => 'nullable|in:purchase,redeem',
            'sessions_redeemed' => 'nullable|array',
            'sessions_redeemed.*' => 'nullable|integer|min:1',
            'promo_code' => 'nullable|string|max:50',
        ]);

        $membership = LoyaltyMembership::with([
            'customer',
            'loyaltyPlan',
        ])->findOrFail($validated['membership_id']);

        if ($membership->status !== 'active') {
            return redirect()
                ->route('scanner.index')
                ->with('error', 'This loyalty membership is not active.');
        }

        if (
            $membership->expires_at &&
            $membership->expires_at->isPast()
        ) {
            return redirect()
                ->route('scanner.index')
                ->with('error', 'This loyalty membership has expired.');
        }

        $services = Service::whereIn('id', $validated['services'])
            ->where('is_active', true)
            ->get();

        if ($services->isEmpty()) {
            return redirect()
                ->route('scanner.index')
                ->with('error', 'No valid services were selected.');
        }

        $customPrices = $validated['custom_prices'] ?? [];
        $packageModes = $validated['package_modes'] ?? [];
        $sessionsRedeemed = $validated['sessions_redeemed'] ?? [];
        $servicePrices = $this->resolveServicePrices($services, $customPrices, $packageModes);

        if ($servicePrices === null) {
            return back()
                ->withInput()
                ->withErrors([
                    'custom_prices' => 'Please enter the actual price for each variable-price service.',
                ]);
        }

        $subtotal = $services->sum(
            fn (Service $service) => $servicePrices[$service->id]
        );

        $eligibleSubtotal = $services
            ->where('discount_eligible', true)
            ->sum(fn (Service $service) => $servicePrices[$service->id]);

        $discountPercentage = $membership
            ->loyaltyPlan
            ->discount_percentage;

        $minimumSpend = $membership
            ->loyaltyPlan
            ->minimum_spend ?? 0;

        $meetsMinimumSpend = $eligibleSubtotal >= $minimumSpend;

        $discountAmount = $meetsMinimumSpend
            ? $eligibleSubtotal * ($discountPercentage / 100)
            : 0;

        $promoCode = null;
        $promoDiscountAmount = 0;
        $totalAfterLoyaltyDiscount = $subtotal - $discountAmount;

        if (!empty($validated['promo_code'])) {
            $promoCode = $this->findAvailablePromoCode($validated['promo_code']);

            if (!$promoCode) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'promo_code' => 'This promo code is invalid, inactive, expired, or fully used.',
                    ]);
            }

            $promoDiscountAmount = $promoCode->discountFor($totalAfterLoyaltyDiscount);
        }

        $total = max(0, $totalAfterLoyaltyDiscount - $promoDiscountAmount);

        return view('scanner.checkout', compact(
            'membership',
            'services',
            'servicePrices',
            'packageModes',
            'sessionsRedeemed',
            'subtotal',
            'eligibleSubtotal',
            'discountPercentage',
            'minimumSpend',
            'meetsMinimumSpend',
            'discountAmount',
            'promoCode',
            'promoDiscountAmount',
            'total'
        ));
    }

    public function confirm(Request $request)
{
    $validated = $request->validate([
        'membership_id' => 'required|exists:loyalty_memberships,id',
        'services' => 'required|array|min:1',
        'services.*' => 'exists:services,id',
        'custom_prices' => 'nullable|array',
        'custom_prices.*' => 'nullable|numeric|min:0',
        'package_modes' => 'nullable|array',
        'package_modes.*' => 'nullable|in:purchase,redeem',
        'sessions_redeemed' => 'nullable|array',
        'sessions_redeemed.*' => 'nullable|integer|min:1',
        'promo_code' => 'nullable|string|max:50',
    ]);

    $membership = LoyaltyMembership::with([
        'customer',
        'loyaltyPlan',
    ])->findOrFail($validated['membership_id']);

    if ($membership->status !== 'active') {
        return redirect()
            ->route('scanner.index')
            ->with('error', 'This loyalty membership is not active.');
    }

    if (
        $membership->expires_at &&
        $membership->expires_at->isPast()
    ) {
        return redirect()
            ->route('scanner.index')
            ->with('error', 'This loyalty membership has expired.');
    }

    $services = Service::whereIn('id', $validated['services'])
        ->where('is_active', true)
        ->get();

    if ($services->isEmpty()) {
        return redirect()
            ->route('scanner.index')
            ->with('error', 'No valid services were selected.');
    }

    $customPrices = $validated['custom_prices'] ?? [];
    $packageModes = $validated['package_modes'] ?? [];
    $sessionsRedeemed = $validated['sessions_redeemed'] ?? [];
    $servicePrices = $this->resolveServicePrices($services, $customPrices, $packageModes);

    if ($servicePrices === null) {
        return redirect()
            ->route('scanner.index')
            ->with('error', 'A variable-price service was missing its actual price. Please scan again.');
    }

    $subtotal = $services->sum(
        fn (Service $service) => $servicePrices[$service->id]
    );

    $eligibleSubtotal = $services
        ->where('discount_eligible', true)
        ->sum(fn (Service $service) => $servicePrices[$service->id]);

    $discountPercentage = $membership
        ->loyaltyPlan
        ->discount_percentage;

    $minimumSpend = $membership
        ->loyaltyPlan
        ->minimum_spend ?? 0;

    $meetsMinimumSpend = $eligibleSubtotal >= $minimumSpend;

    $discountAmount = $meetsMinimumSpend
        ? $eligibleSubtotal * ($discountPercentage / 100)
        : 0;

    $promoCode = null;
    $promoDiscountAmount = 0;
    $totalAfterLoyaltyDiscount = $subtotal - $discountAmount;

    if (!empty($validated['promo_code'])) {
        $promoCode = $this->findAvailablePromoCode($validated['promo_code']);

        if (!$promoCode) {
            return redirect()
                ->route('scanner.index')
                ->with('error', 'The promo code is no longer available. Please scan again.');
        }

        $promoDiscountAmount = $promoCode->discountFor($totalAfterLoyaltyDiscount);
    }

    $total = max(0, $totalAfterLoyaltyDiscount - $promoDiscountAmount);

    $transaction = DB::transaction(function () use (
        $membership,
        $services,
        $servicePrices,
        $packageModes,
        $sessionsRedeemed,
        $subtotal,
        $eligibleSubtotal,
        $discountPercentage,
        $meetsMinimumSpend,
        $discountAmount,
        $promoCode,
        $promoDiscountAmount,
        $total
    ) {

        $transaction = LoyaltyTransaction::create([
            'customer_id' => $membership->customer_id,
            'loyalty_membership_id' => $membership->id,
            'processed_by' => Auth::id(),
            'promo_code_id' => $promoCode?->id,
            'promo_code' => $promoCode?->code,
            'subtotal' => $subtotal,
            'eligible_subtotal' => $eligibleSubtotal,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'promo_discount_amount' => $promoDiscountAmount,
            'total_amount' => $total,
            'transaction_date' => now(),
        ]);

        foreach ($services as $service) {

            $itemDiscount = 0;
            $servicePrice = $servicePrices[$service->id];
            $packageMode = $packageModes[$service->id] ?? 'purchase';
            $isPackageRedemption = $service->is_package && $packageMode === 'redeem';
            $redeemedSessions = $isPackageRedemption
                ? min((int) ($sessionsRedeemed[$service->id] ?? 1), (int) $service->session_count)
                : null;

            if ($service->discount_eligible && $meetsMinimumSpend && !$isPackageRedemption) {
                $itemDiscount =
                    $servicePrice * ($discountPercentage / 100);
            }

            LoyaltyTransactionItem::create([
                'loyalty_transaction_id' => $transaction->id,
                'service_id' => $service->id,
                'service_name' => $service->name,
                'original_price' => $servicePrice,
                'session_count' => $service->is_package ? $service->session_count : null,
                'sessions_redeemed' => $redeemedSessions,
                'is_package_redemption' => $isPackageRedemption,
                'discount_eligible' => $service->discount_eligible,
                'discount_amount' => $itemDiscount,
                'final_price' => $servicePrice - $itemDiscount,
            ]);
        }

        return $transaction;
    });

    return redirect()
        ->route('scanner.index')
        ->with(
            'success',
            'Transaction completed successfully. Transaction #' . $transaction->id
        );
}

    private function resolveServicePrices($services, array $customPrices, array $packageModes = []): ?array
    {
        $prices = [];

        foreach ($services as $service) {
            if ($service->is_package && ($packageModes[$service->id] ?? 'purchase') === 'redeem') {
                $prices[$service->id] = 0;
                continue;
            }

            if ((float) $service->price > 0) {
                $prices[$service->id] = (float) $service->price;
                continue;
            }

            $customPrice = $customPrices[$service->id] ?? null;

            if ($customPrice === null || $customPrice === '') {
                return null;
            }

            $prices[$service->id] = (float) $customPrice;
        }

        return $prices;
    }

    private function findAvailablePromoCode(string $code): ?PromoCode
    {
        $promoCode = PromoCode::where('code', strtoupper(trim($code)))->first();

        if (!$promoCode || !$promoCode->isAvailable()) {
            return null;
        }

        return $promoCode;
    }
}
