<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::withCount('transactions')
            ->latest()
            ->get();

        return view('promo-codes.index', compact('promoCodes'));
    }

    public function create()
    {
        return view('promo-codes.create');
    }

    public function store(Request $request)
    {
        PromoCode::create($this->validatedPromoCode($request));

        return redirect()
            ->route('promo-codes.index')
            ->with('success', 'Promo code created successfully.');
    }

    public function edit(PromoCode $promoCode)
    {
        return view('promo-codes.edit', compact('promoCode'));
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $promoCode->update($this->validatedPromoCode($request, $promoCode));

        return redirect()
            ->route('promo-codes.index')
            ->with('success', 'Promo code updated successfully.');
    }

    public function destroy(PromoCode $promoCode)
    {
        if ($promoCode->transactions()->exists()) {
            return redirect()
                ->route('promo-codes.index')
                ->with('error', 'Promo codes with transaction history cannot be deleted.');
        }

        $promoCode->delete();

        return redirect()
            ->route('promo-codes.index')
            ->with('success', 'Promo code deleted successfully.');
    }

    private function validatedPromoCode(Request $request, ?PromoCode $promoCode = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('promo_codes', 'code')->ignore($promoCode),
            ],
            'name' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['discount_type'] === 'percentage') {
            $request->validate([
                'discount_value' => 'numeric|max:100',
            ]);
        }

        return $validated;
    }
}
