<x-app-layout>

    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                Transaction Details
            </p>

            <h1 class="page-title">
                Transaction #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">

        {{-- CUSTOMER / MEMBERSHIP --}}
        <div class="theme-card p-6 sm:p-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <div>
                    <p class="text-xs text-[#C9B46B]">Customer</p>
                    <p class="font-medium text-[#F7E7B2] mt-1">
                        {{ $transaction->customer->first_name }}
                        {{ $transaction->customer->last_name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">Membership</p>
                    <p class="font-medium text-[#F7E7B2] mt-1">
                        {{ $transaction->membership->membership_code }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">Processed By</p>
                    <p class="font-medium text-[#F7E7B2] mt-1">
                        {{ $transaction->processedBy?->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">Date</p>
                    <p class="font-medium text-[#F7E7B2] mt-1">
                        {{ $transaction->transaction_date->format('M d, Y h:i A') }}
                    </p>
                </div>

            </div>

        </div>


        {{-- SERVICES --}}
        <div class="theme-card overflow-hidden">

            <div class="px-6 py-5 border-b border-[#3A321F]">
                <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                    Services
                </p>

                <h2 class="text-xl mt-1">
                    Transaction Items
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="theme-table-header">
                        <tr>
                            <th class="px-6 py-4 text-left">Service</th>
                            <th class="px-6 py-4 text-left">Original Price</th>
                            <th class="px-6 py-4 text-left">Eligible</th>
                            <th class="px-6 py-4 text-left">Discount</th>
                            <th class="px-6 py-4 text-right">Final Price</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($transaction->items as $item)

                            <tr class="border-t border-[#3A321F]">

                                <td class="px-6 py-4 font-medium text-[#F7E7B2]">
                                    <p>{{ $item->service_name }}</p>
                                    @if($item->session_count)
                                        <p class="mt-1 text-xs text-[#C9B46B]">
                                            {{ $item->is_package_redemption ? 'Prepaid session redeemed' : 'Package purchased' }}
                                            @if($item->is_package_redemption)
                                                - {{ $item->sessions_redeemed }} of {{ $item->session_count }} sessions
                                            @else
                                                - {{ $item->session_count }} sessions
                                            @endif
                                        </p>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    ₱{{ number_format($item->original_price, 2) }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($item->discount_eligible)
                                        <span class="badge-active">Yes</span>
                                    @else
                                        <span class="badge-inactive">No</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-[#D4AF37]">
                                    - ₱{{ number_format($item->discount_amount, 2) }}
                                </td>

                                <td class="px-6 py-4 text-right font-semibold text-[#F7E7B2]">
                                    ₱{{ number_format($item->final_price, 2) }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        {{-- TOTALS --}}
        <div class="theme-card p-6 sm:p-8">

            <div class="space-y-3 max-w-md ml-auto">

                <div class="flex justify-between text-sm">
                    <span class="text-[#C9B46B]">
                        Subtotal
                    </span>

                    <span class="font-medium text-[#F7E7B2]">
                        ₱{{ number_format($transaction->subtotal, 2) }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-[#C9B46B]">
                        Discount Eligible Amount
                    </span>

                    <span class="font-medium text-[#F7E7B2]">
                        ₱{{ number_format($transaction->eligible_subtotal, 2) }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">

                    <span class="text-[#C9B46B]">
                        Loyalty Discount
                        ({{ number_format($transaction->discount_percentage, 0) }}%)
                    </span>

                    <span class="font-medium text-[#D4AF37]">
                        - ₱{{ number_format($transaction->discount_amount, 2) }}
                    </span>

                </div>

                @if($transaction->promo_code)
                    <div class="flex justify-between text-sm">
                        <span class="text-[#C9B46B]">
                            Promo Code
                            ({{ $transaction->promo_code }})
                        </span>

                        <span class="font-medium text-[#D4AF37]">
                            - PHP {{ number_format($transaction->promo_discount_amount, 2) }}
                        </span>
                    </div>
                @endif

                <div class="flex justify-between items-end border-t border-[#3A321F] pt-5 mt-4">

                    <span class="font-medium text-[#F7E7B2]">
                        Amount Paid
                    </span>

                    <span class="font-serif text-3xl text-[#F7E7B2]">
                        ₱{{ number_format($transaction->total_amount, 2) }}
                    </span>

                </div>

            </div>

        </div>


        <div>
            <a
                href="{{ route('transactions.index') }}"
                class="btn-secondary inline-flex">
                ← Back to Transactions
            </a>
        </div>

    </div>

</x-app-layout>
