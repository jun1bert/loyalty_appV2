<div class="theme-card mt-6 p-6">

    <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
        Confirmation
    </p>

    <h3 class="text-xl mt-1">
        Complete Loyalty Transaction
    </h3>

    <p class="text-sm text-[#C9B46B] mt-2">
        Confirming will save this visit, services, prices,
        discount, and staff member to the transaction history.
    </p>

    <div class="mt-5 rounded-xl border border-[#3A321F] bg-[#0D0D0D] p-4 text-sm">
        <div class="flex justify-between gap-4">
            <span class="text-[#C9B46B]">Eligible subtotal</span>
            <span class="font-medium text-[#F7E7B2]">PHP {{ number_format($eligibleSubtotal, 2) }}</span>
        </div>

        <div class="mt-2 flex justify-between gap-4">
            <span class="text-[#C9B46B]">Minimum spend</span>
            <span class="font-medium text-[#F7E7B2]">PHP {{ number_format($minimumSpend, 2) }}</span>
        </div>

        @if(!$meetsMinimumSpend)
            <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                Discount not applied because eligible services did not reach the minimum spend.
            </p>
        @endif

        <div class="mt-2 flex justify-between gap-4">
            <span class="text-[#C9B46B]">Loyalty discount</span>
            <span class="font-medium text-[#D4AF37]">- PHP {{ number_format($discountAmount, 2) }}</span>
        </div>

        @if($promoCode)
            <div class="mt-2 flex justify-between gap-4">
                <span class="text-[#C9B46B]">Promo code {{ $promoCode->code }}</span>
                <span class="font-medium text-[#D4AF37]">- PHP {{ number_format($promoDiscountAmount, 2) }}</span>
            </div>
        @endif

        <div class="mt-4 flex justify-between gap-4 border-t border-[#3A321F] pt-4">
            <span class="font-semibold text-[#F7E7B2]">Amount due</span>
            <span class="font-serif text-2xl text-[#F7E7B2]">PHP {{ number_format($total, 2) }}</span>
        </div>
    </div>

    <form
        method="POST"
        action="{{ route('scanner.confirm') }}"
        class="mt-6">

        @csrf

        <input
            type="hidden"
            name="membership_id"
            value="{{ $membership->id }}"
        >

        @foreach($services as $service)
            <input
                type="hidden"
                name="services[]"
                value="{{ $service->id }}"
            >

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

        <div class="flex flex-col sm:flex-row gap-3">

            <button
                type="submit"
                class="btn-primary text-center">

                Confirm Transaction

            </button>

            <a
                href="{{ route('scanner.index') }}"
                class="btn-secondary text-center">

                Cancel

            </a>

        </div>

    </form>

</div>

