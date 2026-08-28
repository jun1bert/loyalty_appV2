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
