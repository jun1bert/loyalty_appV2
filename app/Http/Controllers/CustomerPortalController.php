<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $customer = $request->user()
            ->customer()
            ->with(['loyaltyMembership.loyaltyPlan'])
            ->first();

        if (! $customer) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Customer profile not found.');
        }

        $transactions = $customer->transactions()
            ->with(['membership.loyaltyPlan', 'items.service'])
            ->latest('transaction_date')
            ->limit(15)
            ->get();

        return view('customer-portal.show', compact('customer', 'transactions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $customer = $request->user()->customer;

        if (! $customer) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Customer profile not found.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($validated['photo']);

        if ($request->hasFile('photo')) {
            if ($customer->photo_path) {
                Storage::disk('public')->delete($customer->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('customer-photos', 'public');
        }

        $customer->update($validated);

        $customer->refresh();

        $request->user()->update([
            'name' => $customer->first_name . ' ' . $customer->last_name,
        ]);

        return redirect()
            ->route('customer.portal')
            ->with('status', 'customer-profile-updated');
    }
}
