<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyMembership;
use App\Models\LoyaltyTransaction;
use App\Models\Service;

class DashboardController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()->hasRole('customer')) {
            return redirect()->route('customer.portal');
        }

        if (auth()->user()->isStaff()) {
            return redirect()->route('scanner.index');
        }

        return view('dashboard', [
            'totalCustomers' => Customer::count(),
            'activeMemberships' => LoyaltyMembership::where('status', 'active')->count(),
            'activeServices' => Service::where('is_active', true)->count(),
            'discountsThisMonth' => LoyaltyTransaction::whereBetween('transaction_date', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])->sum('discount_amount'),
        ]);
    }
}
