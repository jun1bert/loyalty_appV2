<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\LoyaltyMembership;
use App\Models\LoyaltyPlan;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltyTransactionItem;
use App\Models\PromoCode;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VisualDemoSeeder extends Seeder
{
    public function run(): void
    {
        $plan = LoyaltyPlan::firstOrCreate(
            ['name' => 'Black Gold Elite'],
            [
                'price' => 2500,
                'discount_percentage' => 15,
                'minimum_spend' => 500,
                'validity_months' => 12,
                'is_active' => true,
            ]
        );

        $customer = Customer::firstOrCreate(
            [
                'first_name' => 'Isabella',
                'last_name' => 'Valencia',
            ],
            [
                'phone' => '09171234567',
                'birth_date' => '1996-08-14',
            ]
        );

        $membership = LoyaltyMembership::firstOrCreate(
            ['membership_code' => 'MM-VISUAL-001'],
            [
                'customer_id' => $customer->id,
                'loyalty_plan_id' => $plan->id,
                'qr_token' => Str::random(64),
                'activated_at' => now(),
                'expires_at' => now()->addYear(),
                'status' => 'active',
            ]
        );

        $package = Service::firstOrCreate(
            ['name' => 'Gold Hydrafacial Package - 5 Sessions'],
            [
                'price' => 8500,
                'is_package' => true,
                'session_count' => 5,
                'discount_eligible' => true,
                'is_active' => true,
            ]
        );

        $single = Service::firstOrCreate(
            ['name' => 'Luxury Gel Manicure'],
            [
                'price' => 950,
                'is_package' => false,
                'session_count' => null,
                'discount_eligible' => true,
                'is_active' => true,
            ]
        );

        $promoCode = PromoCode::firstOrCreate(
            ['code' => 'OWNER20'],
            [
                'name' => 'Owner Special',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'starts_at' => now()->subDay(),
                'expires_at' => now()->addMonth(),
                'usage_limit' => 100,
                'is_active' => true,
            ]
        );

        $transaction = LoyaltyTransaction::firstOrCreate(
            [
                'customer_id' => $customer->id,
                'loyalty_membership_id' => $membership->id,
                'promo_code' => $promoCode->code,
            ],
            [
                'processed_by' => null,
                'promo_code_id' => $promoCode->id,
                'subtotal' => 9450,
                'eligible_subtotal' => 9450,
                'discount_percentage' => 15,
                'discount_amount' => 1417.50,
                'promo_discount_amount' => 1606.50,
                'total_amount' => 6426,
                'transaction_date' => now(),
            ]
        );

        LoyaltyTransactionItem::firstOrCreate(
            [
                'loyalty_transaction_id' => $transaction->id,
                'service_name' => $package->name,
            ],
            [
                'service_id' => $package->id,
                'original_price' => 8500,
                'session_count' => 5,
                'sessions_redeemed' => null,
                'is_package_redemption' => false,
                'discount_eligible' => true,
                'discount_amount' => 1275,
                'final_price' => 7225,
            ]
        );

        LoyaltyTransactionItem::firstOrCreate(
            [
                'loyalty_transaction_id' => $transaction->id,
                'service_name' => $single->name,
            ],
            [
                'service_id' => $single->id,
                'original_price' => 950,
                'session_count' => null,
                'sessions_redeemed' => null,
                'is_package_redemption' => false,
                'discount_eligible' => true,
                'discount_amount' => 142.50,
                'final_price' => 807.50,
            ]
        );
    }
}
