<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[var(--champagne)] mb-1">
                Confirmation
            </p>

            <h1 class="page-title">
                Complete Loyalty Transaction
            </h1>
        </div>
    </x-slot>

    @php
        $customer = $membership->customer;
        $plan = $membership->loyaltyPlan;
    @endphp

    <div class="max-w-6xl">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px] xl:items-start">
            <div class="space-y-6">
                <div class="premium-card rounded-2xl p-6 text-[var(--ink)] sm:p-8">
                    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            @if($customer->photo_path)
                                <span class="customer-avatar-md border-white/30">
                                    <img
                                        src="{{ Storage::url($customer->photo_path) }}"
                                        alt="{{ $customer->first_name }} {{ $customer->last_name }}"
                                    >
                                </span>
                            @else
                                <div class="customer-avatar-md border-white/30 bg-white/15 text-white">
                                    {{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <p class="premium-eyebrow">
                                    Active Loyalty Member
                                </p>

                                <h2 class="mt-2 font-serif text-3xl">
                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                </h2>

                                <p class="mt-1 text-sm text-[var(--muted)]">
                                    {{ $membership->membership_code }} | {{ $plan->name }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-[var(--champagne)]/30 bg-black/40 px-4 py-3 text-left sm:text-right">
                            <p class="text-xs uppercase tracking-[0.14em] text-[var(--muted)]">
                                Loyalty Discount
                            </p>
                            <p class="mt-1 font-serif text-3xl text-[var(--ink)]">
                                {{ number_format($discountPercentage, 0) }}%
                            </p>
                        </div>
                    </div>
                </div>

                <div class="theme-card p-6 sm:p-8">
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-[0.2em] text-[var(--champagne)]">
                            Selected Services
                        </p>

                        <h2 class="mt-1 font-serif text-2xl text-[var(--ink)]">
                            Review Visit Details
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-separate border-spacing-0 text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-[0.14em] text-[var(--muted)]">
                                    <th class="border-b border-[#3A321F] pb-3 pr-6 font-semibold">Service</th>
                                    <th class="border-b border-[#3A321F] pb-3 pr-6 font-semibold">Type</th>
                                    <th class="border-b border-[#3A321F] pb-3 text-right font-semibold">Price</th>
                                </tr>
                            </thead>

                            <tbody class="text-[var(--ink)]">
                                @foreach($services as $service)
                                    @php
                                        $packageMode = $packageModes[$service->id] ?? 'purchase';
                                        $isRedemption = $service->is_package && $packageMode === 'redeem';
                                    @endphp

                                    <tr>
                                        <td class="border-b border-[#3A321F]/80 py-4 pr-6">
                                            <p class="font-medium">{{ $service->name }}</p>

                                            @if($service->is_package)
                                                <p class="mt-1 text-xs text-[var(--muted)]">
                                                    {{ $service->session_count }} session package
                                                    @if($isRedemption)
                                                        | {{ $sessionsRedeemed[$service->id] ?? 1 }} used
                                                    @endif
                                                </p>
                                            @endif
                                        </td>

                                        <td class="border-b border-[#3A321F]/80 py-4 pr-6 text-[var(--muted)]">
                                            @if($isRedemption)
                                                Prepaid redemption
                                            @elseif($service->discount_eligible)
                                                Discount eligible
                                            @else
                                                Not discount eligible
                                            @endif
                                        </td>

                                        <td class="border-b border-[#3A321F]/80 py-4 text-right font-medium whitespace-nowrap">
                                            PHP {{ number_format($servicePrices[$service->id], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="theme-card p-6 sm:p-7 xl:sticky xl:top-24">
                <p class="text-xs uppercase tracking-[0.2em] text-[var(--champagne)]">
                    Payment Summary
                </p>

                <p class="mt-2 text-sm text-[var(--muted)]">
                    Confirming saves this visit, services, prices, discounts, and staff member to transaction history.
                </p>

                <div class="mt-6 space-y-3 rounded-xl border border-[#3A321F] bg-[#080808] p-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-[var(--muted)]">Subtotal</span>
                        <span class="font-medium text-[var(--ink)]">PHP {{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-[var(--muted)]">Eligible subtotal</span>
                        <span class="font-medium text-[var(--ink)]">PHP {{ number_format($eligibleSubtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-[var(--muted)]">Minimum spend</span>
                        <span class="font-medium text-[var(--ink)]">PHP {{ number_format($minimumSpend, 2) }}</span>
                    </div>

                    @if(!$meetsMinimumSpend)
                        <p class="rounded-lg border border-amber-300/30 bg-amber-950/30 px-3 py-2 text-xs font-semibold text-amber-200">
                            Discount not applied because eligible services did not reach the minimum spend.
                        </p>
                    @endif

                    <div class="flex justify-between gap-4">
                        <span class="text-[var(--muted)]">Loyalty discount</span>
                        <span class="font-medium text-[var(--champagne)]">- PHP {{ number_format($discountAmount, 2) }}</span>
                    </div>

                    @if($promoCode)
                        <div class="flex justify-between gap-4">
                            <span class="text-[var(--muted)]">Promo code {{ $promoCode->code }}</span>
                            <span class="font-medium text-[var(--champagne)]">- PHP {{ number_format($promoDiscountAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex items-end justify-between gap-4 border-t border-[#3A321F] pt-4">
                        <span class="font-semibold text-[var(--ink)]">Amount due</span>
                        <span class="font-serif text-3xl text-[var(--ink)]">PHP {{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('scanner.confirm') }}" class="mt-6">
                    @csrf

                    <input type="hidden" name="membership_id" value="{{ $membership->id }}">

                    @foreach($services as $service)
                        <input type="hidden" name="services[]" value="{{ $service->id }}">

                        @if((float) $service->price <= 0)
                            <input
                                type="hidden"
                                name="custom_prices[{{ $service->id }}]"
                                value="{{ number_format($servicePrices[$service->id], 2, '.', '') }}"
                            >
                        @endif

                        @if($service->is_package)
                            <input
                                type="hidden"
                                name="package_modes[{{ $service->id }}]"
                                value="{{ $packageModes[$service->id] ?? 'purchase' }}"
                            >

                            <input
                                type="hidden"
                                name="sessions_redeemed[{{ $service->id }}]"
                                value="{{ $sessionsRedeemed[$service->id] ?? 1 }}"
                            >
                        @endif
                    @endforeach

                    @if($promoCode)
                        <input type="hidden" name="promo_code" value="{{ $promoCode->code }}">
                    @endif

                    <div class="grid gap-3">
                        <button type="submit" class="btn-primary text-center">
                            Confirm Transaction
                        </button>

                        <a href="{{ route('scanner.index') }}" class="btn-secondary text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
