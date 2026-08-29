@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--champagne)] mb-1">
                Customer Account
            </p>

            <h1 class="page-title">
                My Profile
            </h1>
        </div>
    </x-slot>

    @php
        $membership = $customer->loyaltyMembership;
        $plan = $membership?->loyaltyPlan;
        $birthDate = $customer->birth_date
            ? \Carbon\Carbon::parse($customer->birth_date)->format('Y-m-d')
            : null;
    @endphp

    @if(session('status') === 'customer-profile-updated')
        <div class="mb-6 rounded-lg border border-[var(--champagne)]/30 bg-[var(--champagne)]/10 px-4 py-3 text-sm text-[var(--ink)]">
            Profile updated.
        </div>
    @endif

    <div class="customer-portal-grid">
        <div class="theme-card customer-portal-card">
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--champagne)]">
                Edit Profile
            </p>

            <form method="POST" action="{{ route('customer.portal.update') }}" enctype="multipart/form-data" class="customer-portal-form">
                @csrf
                @method('patch')

                <div class="flex items-center gap-4">
                    @if($customer->photo_path)
                        <span class="customer-avatar-md">
                            <img
                                src="{{ Storage::url($customer->photo_path) }}"
                                alt="{{ $customer->first_name }} {{ $customer->last_name }}"
                            >
                        </span>
                    @else
                        <div class="customer-avatar-md">
                            {{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <p class="font-medium text-[var(--ink)] truncate">
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        </p>
                        <p class="text-xs text-[var(--muted)] mt-1 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>

                <div class="customer-field">
                    <x-input-label for="first_name" value="First Name" />
                    <x-text-input id="first_name" name="first_name" type="text" :value="old('first_name', $customer->first_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>

                <div class="customer-field">
                    <x-input-label for="last_name" value="Last Name" />
                    <x-text-input id="last_name" name="last_name" type="text" :value="old('last_name', $customer->last_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>

                <div class="customer-field">
                    <x-input-label for="phone" value="Mobile Number" />
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $customer->phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="customer-field">
                    <x-input-label for="birth_date" value="Birth Date" />
                    <x-text-input id="birth_date" name="birth_date" type="date" :value="old('birth_date', $birthDate)" />
                    <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                </div>

                <div class="customer-field">
                    <x-input-label for="photo" value="Profile Photo" />
                    <input
                        id="photo"
                        name="photo"
                        type="file"
                        accept="image/*"
                        class="customer-file-input"
                    >
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>

                <button type="submit" class="btn-primary">
                    Save Profile
                </button>
            </form>
        </div>

        <div class="space-y-8">
            <div class="theme-card customer-portal-card">
                <p class="text-xs uppercase tracking-[0.2em] text-[var(--champagne)]">
                    Plan Availed
                </p>

                @if($membership)
                    <div class="customer-plan-layout">
                        <div class="customer-plan-details">
                            <div>
                                <p class="text-xs text-[var(--muted)]">Plan</p>
                                <p class="mt-1 text-xl font-semibold text-[var(--ink)]">{{ $plan?->name ?? 'Loyalty Plan' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-[var(--muted)]">Membership No.</p>
                                <p class="mt-1 font-medium text-[var(--ink)] break-words">{{ $membership->membership_code }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-[var(--muted)]">Discount</p>
                                <p class="mt-1 font-medium text-[var(--ink)]">{{ number_format($plan?->discount_percentage ?? 0, 0) }}%</p>
                            </div>

                            <div>
                                <p class="text-xs text-[var(--muted)]">Status</p>
                                <p class="mt-1 font-medium text-[var(--ink)]">{{ ucfirst($membership->status) }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-[var(--muted)]">Activated</p>
                                <p class="mt-1 font-medium text-[var(--ink)]">{{ $membership->activated_at?->format('M d, Y') ?? 'Not available' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-[var(--muted)]">Valid Until</p>
                                <p class="mt-1 font-medium text-[var(--ink)]">{{ $membership->expires_at?->format('M d, Y') ?? 'No expiration' }}</p>
                            </div>
                        </div>

                        <div class="customer-plan-qr">
                            <div class="customer-qr-box">
                                {!! QrCode::size(160)->generate($membership->qr_token) !!}
                            </div>
                            <p class="mt-3 text-xs uppercase tracking-[0.14em] text-[var(--champagne)]">
                                Membership QR
                            </p>
                            <p class="mt-1 text-xs text-[var(--muted)]">
                                Show this to staff.
                            </p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-[var(--muted)]">
                        You do not have an active loyalty plan yet.
                    </p>
                @endif
            </div>

            <div class="theme-card customer-portal-card">
                <p class="text-xs uppercase tracking-[0.2em] text-[var(--champagne)]">
                    Transactions
                </p>

                <div class="mt-6 overflow-x-auto">
                    <table class="customer-transactions-table">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.14em] text-[var(--muted)]">
                                <th>Date</th>
                                <th>Plan</th>
                                <th>Discount</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody class="text-[var(--ink)]">
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="whitespace-nowrap">
                                        {{ $transaction->transaction_date?->format('M d, Y') ?? 'Not dated' }}
                                    </td>
                                    <td>
                                        {{ $transaction->membership?->loyaltyPlan?->name ?? 'Loyalty Plan' }}
                                    </td>
                                    <td class="amount">
                                        PHP {{ number_format($transaction->discount_amount + $transaction->promo_discount_amount, 2) }}
                                    </td>
                                    <td class="amount">
                                        PHP {{ number_format($transaction->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-[var(--muted)]">
                                        No transactions yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

