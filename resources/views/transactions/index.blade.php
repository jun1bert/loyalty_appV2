<x-app-layout>

    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                Loyalty Management
            </p>

            <h1 class="page-title">
                Transactions
            </h1>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('transactions.index') }}" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="Search transaction ID, customer, membership, or staff"
            class="theme-input sm:max-w-md"
        >

        <div class="flex gap-2">
            <button type="submit" class="btn-primary">
                Search
            </button>

            @if($search !== '')
                <a href="{{ route('transactions.index') }}" class="btn-secondary">
                    Clear
                </a>
            @endif
        </div>
    </form>

    <div class="theme-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="theme-table-header">

                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">
                            Transaction
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Customer
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Subtotal
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Discount
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Total
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Processed By
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Date
                        </th>

                        <th class="px-6 py-4 text-right font-semibold">
                            Action
                        </th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($transactions as $transaction)

                        <tr class="border-t border-[#3A321F] hover:bg-[#1A1A1A]/60">

                            <td class="px-6 py-4 font-medium text-[#F7E7B2]">
                                #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-6 py-4">

                                <p class="font-medium text-[#F7E7B2]">
                                    {{ $transaction->customer->first_name }}
                                    {{ $transaction->customer->last_name }}
                                </p>

                                <p class="text-xs text-[#C9B46B] mt-1">
                                    {{ $transaction->membership->membership_code }}
                                </p>

                            </td>

                            <td class="px-6 py-4 text-[#E8DDAA]">
                                ₱{{ number_format($transaction->subtotal, 2) }}
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-medium text-[#D4AF37]">
                                    - ₱{{ number_format($transaction->discount_amount, 2) }}
                                </p>

                                <p class="text-xs text-[#C9B46B]">
                                    {{ number_format($transaction->discount_percentage, 0) }}%
                                </p>

                                @if($transaction->promo_code)
                                    <p class="text-xs text-[#C9B46B]">
                                        + {{ $transaction->promo_code }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-6 py-4 font-semibold text-[#F7E7B2]">
                                ₱{{ number_format($transaction->total_amount, 2) }}
                            </td>

                            <td class="px-6 py-4 text-[#E8DDAA]">
                                {{ $transaction->processedBy?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-[#E8DDAA]">
                                {{ $transaction->transaction_date->format('M d, Y h:i A') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a
                                    href="{{ route('transactions.show', $transaction) }}"
                                    class="text-[#D4AF37] hover:text-[#F2C94C] font-medium">
                                    View
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8"
                                class="px-6 py-12 text-center text-[#C9B46B]">
                                {{ $search !== '' ? 'No transactions match your search.' : 'No loyalty transactions yet.' }}
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>

