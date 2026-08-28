<?php

use App\Models\Customer;
use App\Models\LoyaltyMembership;
use App\Models\LoyaltyPlan;
use App\Models\LoyaltyTransaction;
use App\Models\PromoCode;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function managementUser(): User
{
    return User::factory()->create([
        'role' => 'management',
    ]);
}

test('customer show and edit pages render', function () {
    $user = managementUser();

    $plan = LoyaltyPlan::create([
        'name' => 'Classic Plan',
        'price' => 1000,
        'discount_percentage' => 10,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $customer = Customer::create([
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'phone' => '09123456789',
    ]);

    LoyaltyMembership::create([
        'customer_id' => $customer->id,
        'loyalty_plan_id' => $plan->id,
        'membership_code' => 'MM-TEST-0001',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('customers.show', $customer))
        ->assertOk()
        ->assertSee('Maria')
        ->assertSee('MM-TEST-0001');

    $this->actingAs($user)
        ->get(route('customers.edit', $customer))
        ->assertOk()
        ->assertSee('Edit Customer')
        ->assertSee('09123456789');
});

test('dashboard displays live summary counts', function () {
    $user = managementUser();

    $activePlan = LoyaltyPlan::create([
        'name' => 'Dashboard Plan',
        'price' => 1000,
        'discount_percentage' => 10,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $inactivePlan = LoyaltyPlan::create([
        'name' => 'Inactive Dashboard Plan',
        'price' => 1200,
        'discount_percentage' => 12,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $customerOne = Customer::create([
        'first_name' => 'Rina',
        'last_name' => 'Lopez',
    ]);

    $customerTwo = Customer::create([
        'first_name' => 'Maya',
        'last_name' => 'Cruz',
    ]);

    $activeMembership = LoyaltyMembership::create([
        'customer_id' => $customerOne->id,
        'loyalty_plan_id' => $activePlan->id,
        'membership_code' => 'MM-DASH-0001',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'active',
    ]);

    LoyaltyMembership::create([
        'customer_id' => $customerTwo->id,
        'loyalty_plan_id' => $inactivePlan->id,
        'membership_code' => 'MM-DASH-0002',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'inactive',
    ]);

    Service::create([
        'name' => 'Active Dashboard Service',
        'price' => 500,
        'discount_eligible' => true,
        'is_active' => true,
    ]);

    Service::create([
        'name' => 'Inactive Dashboard Service',
        'price' => 500,
        'discount_eligible' => true,
        'is_active' => false,
    ]);

    LoyaltyTransaction::create([
        'customer_id' => $customerOne->id,
        'loyalty_membership_id' => $activeMembership->id,
        'processed_by' => $user->id,
        'subtotal' => 1000,
        'eligible_subtotal' => 1000,
        'discount_percentage' => 10,
        'discount_amount' => 100,
        'total_amount' => 900,
        'transaction_date' => now(),
    ]);

    LoyaltyTransaction::create([
        'customer_id' => $customerOne->id,
        'loyalty_membership_id' => $activeMembership->id,
        'processed_by' => $user->id,
        'subtotal' => 1000,
        'eligible_subtotal' => 1000,
        'discount_percentage' => 10,
        'discount_amount' => 50,
        'total_amount' => 950,
        'transaction_date' => now()->subMonth(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Total Customers')
        ->assertSee('2')
        ->assertSee('Active Memberships')
        ->assertSee('1')
        ->assertSee('Services')
        ->assertSee('1')
        ->assertSee('PHP 100.00');
});

test('management can create promo codes', function () {
    $user = managementUser();

    $this->actingAs($user)
        ->post(route('promo-codes.store'), [
            'code' => 'owner20',
            'name' => 'Owner VIP',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'starts_at' => now()->subDay()->format('Y-m-d'),
            'expires_at' => now()->addWeek()->format('Y-m-d'),
            'usage_limit' => 50,
            'is_active' => '1',
        ])
        ->assertRedirect(route('promo-codes.index'));

    $promoCode = PromoCode::where('code', 'OWNER20')->firstOrFail();

    expect($promoCode->name)->toBe('Owner VIP')
        ->and($promoCode->discount_type)->toBe('percentage')
        ->and((float) $promoCode->discount_value)->toBe(20.0)
        ->and($promoCode->is_active)->toBeTrue();

    $this->actingAs($user)
        ->get(route('promo-codes.index'))
        ->assertOk()
        ->assertSee('OWNER20')
        ->assertSee('Owner VIP');
});

test('promo code applies an extra discount during checkout confirmation', function () {
    $user = managementUser();

    $plan = LoyaltyPlan::create([
        'name' => 'Promo Plan',
        'price' => 1000,
        'discount_percentage' => 10,
        'minimum_spend' => 0,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $customer = Customer::create([
        'first_name' => 'Promo',
        'last_name' => 'Customer',
    ]);

    $membership = LoyaltyMembership::create([
        'customer_id' => $customer->id,
        'loyalty_plan_id' => $plan->id,
        'membership_code' => 'MM-PROMO-001',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'active',
    ]);

    $service = Service::create([
        'name' => 'Promo Service',
        'price' => 1000,
        'discount_eligible' => true,
        'is_active' => true,
    ]);

    PromoCode::create([
        'code' => 'VIP100',
        'name' => 'VIP fixed discount',
        'discount_type' => 'fixed',
        'discount_value' => 100,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('scanner.calculate'), [
            'membership_id' => $membership->id,
            'services' => [$service->id],
            'promo_code' => 'vip100',
        ])
        ->assertOk()
        ->assertSee('Promo code VIP100')
        ->assertSee('PHP 800.00');

    $this->actingAs($user)
        ->post(route('scanner.confirm'), [
            'membership_id' => $membership->id,
            'services' => [$service->id],
            'promo_code' => 'VIP100',
        ])
        ->assertRedirect(route('scanner.index'));

    $transaction = LoyaltyTransaction::latest()->firstOrFail();

    expect($transaction->promo_code)->toBe('VIP100')
        ->and((float) $transaction->discount_amount)->toBe(100.0)
        ->and((float) $transaction->promo_discount_amount)->toBe(100.0)
        ->and((float) $transaction->total_amount)->toBe(800.0);
});

test('package prepaid session redemption does not discount the full package again', function () {
    $user = managementUser();

    $plan = LoyaltyPlan::create([
        'name' => 'Package Plan',
        'price' => 1000,
        'discount_percentage' => 10,
        'minimum_spend' => 0,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $customer = Customer::create([
        'first_name' => 'Package',
        'last_name' => 'Customer',
    ]);

    $membership = LoyaltyMembership::create([
        'customer_id' => $customer->id,
        'loyalty_plan_id' => $plan->id,
        'membership_code' => 'MM-PACK-001',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'active',
    ]);

    $service = Service::create([
        'name' => 'Five Session Package',
        'price' => 5000,
        'is_package' => true,
        'session_count' => 5,
        'discount_eligible' => true,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('scanner.confirm'), [
            'membership_id' => $membership->id,
            'services' => [$service->id],
            'package_modes' => [
                $service->id => 'redeem',
            ],
            'sessions_redeemed' => [
                $service->id => 1,
            ],
        ])
        ->assertRedirect(route('scanner.index'));

    $transaction = LoyaltyTransaction::with('items')->latest()->firstOrFail();
    $item = $transaction->items->first();

    expect((float) $transaction->subtotal)->toBe(0.0)
        ->and((float) $transaction->discount_amount)->toBe(0.0)
        ->and((float) $transaction->total_amount)->toBe(0.0)
        ->and($item->is_package_redemption)->toBeTrue()
        ->and($item->sessions_redeemed)->toBe(1)
        ->and($item->session_count)->toBe(5);
});

test('customer edit page updates customer details', function () {
    $user = managementUser();

    $customer = Customer::create([
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'phone' => '09123456789',
    ]);

    $this->actingAs($user)
        ->put(route('customers.update', $customer), [
            'first_name' => 'Ana',
            'last_name' => 'Reyes',
            'phone' => '09998887777',
            'birth_date' => '1995-05-10',
        ])
        ->assertRedirect(route('customers.index'));

    $customer->refresh();

    expect($customer->first_name)->toBe('Ana')
        ->and($customer->last_name)->toBe('Reyes')
        ->and($customer->phone)->toBe('09998887777');
});

test('customer photo can be uploaded during activation and replaced later', function () {
    Storage::fake('public');

    $user = managementUser();

    $plan = LoyaltyPlan::create([
        'name' => 'Photo Plan',
        'price' => 1000,
        'discount_percentage' => 10,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('customers.store'), [
            'first_name' => 'Lara',
            'last_name' => 'Dizon',
            'phone' => '09170000003',
            'birth_date' => '1994-03-12',
            'loyalty_plan_id' => $plan->id,
            'photo' => UploadedFile::fake()->image('lara.jpg'),
        ])
        ->assertRedirect(route('customers.index'));

    $customer = Customer::where('first_name', 'Lara')->firstOrFail();
    $originalPhotoPath = $customer->photo_path;

    expect($originalPhotoPath)->not->toBeNull();
    Storage::disk('public')->assertExists($originalPhotoPath);

    $this->actingAs($user)
        ->put(route('customers.update', $customer), [
            'first_name' => 'Lara',
            'last_name' => 'Dizon',
            'phone' => '09170000003',
            'birth_date' => '1994-03-12',
            'photo' => UploadedFile::fake()->image('lara-new.jpg'),
        ])
        ->assertRedirect(route('customers.index'));

    $customer->refresh();

    expect($customer->photo_path)->not->toBe($originalPhotoPath);
    Storage::disk('public')->assertMissing($originalPhotoPath);
    Storage::disk('public')->assertExists($customer->photo_path);
});

test('service show and edit pages render', function () {
    $user = managementUser();

    $service = Service::create([
        'name' => 'Classic Manicure',
        'price' => 500,
        'discount_eligible' => true,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('services.show', $service))
        ->assertOk()
        ->assertSee('Classic Manicure')
        ->assertSee('PHP 500.00');

    $this->actingAs($user)
        ->get(route('services.edit', $service))
        ->assertOk()
        ->assertSee('Edit Service')
        ->assertSee('Classic Manicure');
});

test('loyalty plan show and edit pages render', function () {
    $user = managementUser();

    $plan = LoyaltyPlan::create([
        'name' => 'Gold Plan',
        'price' => 1500,
        'discount_percentage' => 15,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('loyalty-plans.show', $plan))
        ->assertOk()
        ->assertSee('Gold Plan')
        ->assertSee('15.00%');

    $this->actingAs($user)
        ->get(route('loyalty-plans.edit', $plan))
        ->assertOk()
        ->assertSee('Edit Loyalty Plan')
        ->assertSee('Gold Plan');
});

test('membership show displays linked customer account email', function () {
    $user = managementUser();

    $customerUser = User::factory()->create([
        'email' => 'customer@example.com',
        'role' => 'customer',
    ]);

    $plan = LoyaltyPlan::create([
        'name' => 'Classic Plan',
        'price' => 1000,
        'discount_percentage' => 10,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $customer = Customer::create([
        'user_id' => $customerUser->id,
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'phone' => '09123456789',
    ]);

    $membership = LoyaltyMembership::create([
        'customer_id' => $customer->id,
        'loyalty_plan_id' => $plan->id,
        'membership_code' => 'MM-TEST-0002',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('memberships.show', $membership))
        ->assertOk()
        ->assertSee('customer@example.com')
        ->assertSee('MM-TEST-0002');
});

test('user management index handles users without timestamps', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $legacyUser = User::factory()->create([
        'name' => 'Legacy Staff',
        'role' => 'staff',
    ]);

    $legacyUser->timestamps = false;
    $legacyUser->created_at = null;
    $legacyUser->updated_at = null;
    $legacyUser->save();

    $this->actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Legacy Staff')
        ->assertSee('Not recorded');
});

test('management list pages can be searched', function () {
    $user = managementUser();

    $serviceMatch = Service::create([
        'name' => 'Signature Pedicure',
        'price' => 700,
        'discount_eligible' => true,
        'is_active' => true,
    ]);

    Service::create([
        'name' => 'Classic Manicure',
        'price' => 500,
        'discount_eligible' => true,
        'is_active' => true,
    ]);

    $planMatch = LoyaltyPlan::create([
        'name' => 'Platinum Rewards',
        'price' => 1800,
        'discount_percentage' => 15,
        'minimum_spend' => 350,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $planOther = LoyaltyPlan::create([
        'name' => 'Classic Rewards',
        'price' => 1000,
        'discount_percentage' => 10,
        'minimum_spend' => 350,
        'validity_months' => 12,
        'is_active' => true,
    ]);

    $customerMatch = Customer::create([
        'first_name' => 'Bianca',
        'last_name' => 'Cruz',
        'phone' => '09170000001',
    ]);

    $customerOther = Customer::create([
        'first_name' => 'Clara',
        'last_name' => 'Reyes',
        'phone' => '09170000002',
    ]);

    $membershipMatch = LoyaltyMembership::create([
        'customer_id' => $customerMatch->id,
        'loyalty_plan_id' => $planMatch->id,
        'membership_code' => 'MM-SEARCH-0001',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'active',
    ]);

    LoyaltyMembership::create([
        'customer_id' => $customerOther->id,
        'loyalty_plan_id' => $planOther->id,
        'membership_code' => 'MM-OTHER-0001',
        'qr_token' => (string) Str::uuid(),
        'activated_at' => now(),
        'expires_at' => now()->addYear(),
        'status' => 'active',
    ]);

    $staff = User::factory()->create([
        'name' => 'Nina Cashier',
        'role' => 'staff',
    ]);

    LoyaltyTransaction::create([
        'customer_id' => $customerMatch->id,
        'loyalty_membership_id' => $membershipMatch->id,
        'processed_by' => $staff->id,
        'subtotal' => 1000,
        'eligible_subtotal' => 1000,
        'discount_percentage' => 15,
        'discount_amount' => 150,
        'total_amount' => 850,
        'transaction_date' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('services.index', ['search' => 'Signature']))
        ->assertOk()
        ->assertSee($serviceMatch->name)
        ->assertDontSee('Classic Manicure');

    $this->actingAs($user)
        ->get(route('loyalty-plans.index', ['search' => 'Platinum']))
        ->assertOk()
        ->assertSee($planMatch->name)
        ->assertDontSee($planOther->name);

    $this->actingAs($user)
        ->get(route('customers.index', ['search' => 'Bianca']))
        ->assertOk()
        ->assertSee('Bianca')
        ->assertDontSee('Clara');

    $this->actingAs($user)
        ->get(route('memberships.index', ['search' => 'MM-SEARCH']))
        ->assertOk()
        ->assertSee('MM-SEARCH-0001')
        ->assertDontSee('MM-OTHER-0001');

    $this->actingAs($user)
        ->get(route('transactions.index', ['search' => 'Nina']))
        ->assertOk()
        ->assertSee('Nina Cashier')
        ->assertDontSee('No transactions match your search.');
});
