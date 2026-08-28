<x-app-layout>

    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                Loyalty Verification
            </p>

            <h1 class="page-title">
                Membership Verified
            </h1>
        </div>
    </x-slot>

    @php
        $customer = $membership->customer;
        $plan = $membership->loyaltyPlan;
    @endphp

    <div class="max-w-4xl">

        <div class="premium-card rounded-2xl p-7 text-[#F7E7B2] sm:p-8">
            <div class="relative flex flex-col sm:flex-row sm:justify-between gap-5">
                <div>
                    <p class="premium-eyebrow">
                        Martinis & Manicures
                    </p>

                    <h2 class="gold-foil-text font-serif text-3xl mt-2">
                        Active Loyalty Member
                    </h2>
                </div>

                <span class="self-start rounded-full border border-[#D4AF37]/50 bg-black/60
                             text-[#F7E7B2] px-4 py-1.5
                             text-xs font-semibold shadow-lg shadow-black/30">
                    ACTIVE
                </span>
            </div>

            <div class="relative mt-8 flex items-center gap-4">
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
                    <p class="text-sm text-[#C9B46B]">
                        Customer
                    </p>

                    <p class="text-2xl font-medium mt-1">
                        {{ $customer->first_name }}
                        {{ $customer->last_name }}
                    </p>
                </div>
            </div>

            <div class="relative grid grid-cols-1 sm:grid-cols-3 gap-5 mt-8 border-t border-[#D4AF37]/20 pt-6">
                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Membership
                    </p>

                    <p class="font-medium mt-1">
                        {{ $membership->membership_code }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Discount
                    </p>

                    <p class="text-2xl font-semibold mt-1">
                        {{ number_format($plan->discount_percentage, 0) }}%
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Valid Until
                    </p>

                    <p class="font-medium mt-1">
                        {{ $membership->expires_at
                            ? $membership->expires_at->format('M d, Y')
                            : 'No Expiration' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="theme-card mt-6 p-6 sm:p-8">
            <div class="mb-6">
                <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                    Services
                </p>

                <h3 class="font-serif text-2xl text-[#F7E7B2] mt-1">
                    Select Customer Services
                </h3>

                <p class="text-sm text-[#C9B46B] mt-2">
                    Select all services received during this visit.
                    Variable-price services require the actual amount before checkout.
                </p>
            </div>

            <form method="POST" action="{{ route('scanner.calculate') }}">
                @csrf

                <input type="hidden" name="membership_id" value="{{ $membership->id }}">

                <div class="space-y-3">
                    @forelse($services as $service)
                        <label
                            class="flex flex-col gap-4 rounded-xl border border-[#3A321F]
                                   bg-[#0D0D0D] p-4 sm:p-5 cursor-pointer
                                   hover:border-[#B8860B] hover:bg-[#1A1A1A]
                                   transition">

                            <div class="flex items-center justify-between gap-5">
                                <div class="flex items-center gap-4">
                                    <input
                                        type="checkbox"
                                        name="services[]"
                                        value="{{ $service->id }}"
                                        class="h-5 w-5 rounded border-[#B8860B]
                                               text-[#D4AF37] focus:ring-[#D4AF37]"
                                    >

                                    <div>
                                        <p class="font-medium text-[#F7E7B2]">
                                            {{ $service->name }}
                                        </p>

                                        @if($service->discount_eligible)
                                            <p class="text-xs text-[#D4AF37] mt-1">
                                                Counts toward minimum spend
                                            </p>
                                        @else
                                            <p class="text-xs text-[#C9B46B] mt-1">
                                                Not eligible for loyalty discount
                                            </p>
                                        @endif

                                        @if($service->is_package)
                                            <p class="text-xs text-[#C9B46B] mt-1">
                                                Package: {{ $service->session_count }} sessions
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="font-semibold text-[#F7E7B2] whitespace-nowrap">
                                        @if((float) $service->price > 0)
                                            PHP {{ number_format($service->price, 2) }}
                                        @else
                                            Variable price
                                        @endif
                                    </p>

                                    @if($service->discount_eligible)
                                        <p class="text-xs text-[#D4AF37] mt-1">
                                            -{{ number_format($plan->discount_percentage, 0) }}%
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if($service->is_package)
                                <div class="grid gap-3 sm:ml-9 sm:grid-cols-2">
                                    <div>
                                        <label
                                            for="package_mode_{{ $service->id }}"
                                            class="block text-xs font-semibold uppercase tracking-[0.14em] text-[#C9B46B]">
                                            Package Use
                                        </label>

                                        <select
                                            id="package_mode_{{ $service->id }}"
                                            name="package_modes[{{ $service->id }}]"
                                            class="theme-input mt-2">
                                            <option value="purchase" {{ old('package_modes.' . $service->id, 'purchase') === 'purchase' ? 'selected' : '' }}>
                                                Charge full package today
                                            </option>
                                            <option value="redeem" {{ old('package_modes.' . $service->id) === 'redeem' ? 'selected' : '' }}>
                                                Redeem prepaid session
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            for="sessions_redeemed_{{ $service->id }}"
                                            class="block text-xs font-semibold uppercase tracking-[0.14em] text-[#C9B46B]">
                                            Sessions Used
                                        </label>

                                        <input
                                            id="sessions_redeemed_{{ $service->id }}"
                                            type="number"
                                            name="sessions_redeemed[{{ $service->id }}]"
                                            value="{{ old('sessions_redeemed.' . $service->id, 1) }}"
                                            min="1"
                                            max="{{ $service->session_count }}"
                                            step="1"
                                            class="theme-input mt-2"
                                        >
                                    </div>
                                </div>
                            @endif

                            @if((float) $service->price <= 0)
                                <div class="sm:ml-9">
                                    <label
                                        for="custom_price_{{ $service->id }}"
                                        class="block text-xs font-semibold uppercase tracking-[0.14em] text-[#C9B46B]">
                                        Actual Price
                                    </label>

                                    <input
                                        id="custom_price_{{ $service->id }}"
                                        type="number"
                                        name="custom_prices[{{ $service->id }}]"
                                        value="{{ old('custom_prices.' . $service->id) }}"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="mt-2 w-full sm:max-w-xs rounded-lg border-[#D4AF37]/40
                                               bg-black/40 text-[#F7E7B2]
                                               focus:border-[#D4AF37]
                                               focus:ring-[#D4AF37]"
                                    >
                                </div>
                            @endif
                        </label>
                    @empty
                        <div class="rounded-xl bg-[#1A1A1A] p-8 text-center">
                            <p class="text-[#C9B46B]">
                                No active services available.
                            </p>
                        </div>
                    @endforelse
                </div>

                @error('services')
                    <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                        <p class="text-red-600 text-sm">
                            Please select at least one service.
                        </p>
                    </div>
                @enderror

                @error('custom_prices')
                    <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3">
                        <p class="text-red-600 text-sm">
                            {{ $message }}
                        </p>
                    </div>
                @enderror

                <div class="mt-6 rounded-xl border border-[#D4AF37]/25 bg-black/20 p-4">
                    <label
                        for="promo_code"
                        class="block text-xs font-semibold uppercase tracking-[0.14em] text-[#D4AF37]">
                        Promo Code
                    </label>

                    <input
                        id="promo_code"
                        type="text"
                        name="promo_code"
                        value="{{ old('promo_code') }}"
                        placeholder="Optional owner or influencer code"
                        class="theme-input mt-2 uppercase"
                    >

                    @error('promo_code')
                        <p class="mt-2 text-xs text-red-300">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                @if($services->isNotEmpty())
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3
                                border-t border-[#3A321F] mt-6 pt-6">
                        <button type="submit" class="btn-primary text-center">
                            Calculate Discount
                        </button>

                        <a href="{{ route('scanner.index') }}" class="btn-secondary text-center">
                            Cancel
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

</x-app-layout>

