<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                    Membership
                </p>

                <h1 class="page-title">
                    Loyalty Plans
                </h1>
            </div>

            <a href="{{ route('loyalty-plans.create') }}"
               class="btn-primary inline-flex items-center justify-center">

                + Add Loyalty Plan

            </a>

        </div>

    </x-slot>


    @if(session('success'))

        <div class="mb-5 rounded-lg border border-[#3A321F]
                    bg-[#0D0D0D] px-4 py-3 text-sm text-[#E8DDAA]">

            {{ session('success') }}

        </div>

    @endif

    <form method="GET" action="{{ route('loyalty-plans.index') }}" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="Search loyalty plans"
            class="theme-input sm:max-w-sm"
        >

        <div class="flex gap-2">
            <button type="submit" class="btn-primary">
                Search
            </button>

            @if($search !== '')
                <a href="{{ route('loyalty-plans.index') }}" class="btn-secondary">
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
                            Plan
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Card Price
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Discount
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Minimum Spend
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Validity
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Status
                        </th>

                        <th class="px-6 py-4 text-right font-semibold">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($plans as $plan)

                        <tr class="border-t border-[#3A321F]
                                   hover:bg-[#1A1A1A]/60 transition">

                            <td class="px-6 py-4 font-medium text-[#F7E7B2]">

                                {{ $plan->name }}

                            </td>


                            <td class="px-6 py-4 text-[#E8DDAA]">

                                ₱{{ number_format($plan->price, 2) }}

                            </td>


                            <td class="px-6 py-4">

                                <span class="font-medium text-[#D4AF37]">

                                    {{ number_format($plan->discount_percentage, 0) }}%

                                </span>

                            </td>


                            <td class="px-6 py-4 text-[#E8DDAA]">

                                PHP {{ number_format($plan->minimum_spend ?? 0, 2) }}

                            </td>


                            <td class="px-6 py-4 text-[#E8DDAA]">

                                {{ $plan->validity_months }}

                                {{ $plan->validity_months == 1 ? 'month' : 'months' }}

                            </td>


                            <td class="px-6 py-4">

                                @if($plan->is_active)

                                    <span class="badge-active">
                                        Active
                                    </span>

                                @else

                                    <span class="badge-inactive">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td class="px-6 py-4">

                                <div class="flex justify-end items-center gap-4">

                                    <a
                                        href="{{ route('loyalty-plans.edit', $plan) }}"
                                        class="text-[#D4AF37] hover:text-[#F2C94C] font-medium">

                                        Edit

                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route('loyalty-plans.destroy', $plan) }}">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            onclick="return confirm('Delete this loyalty plan?')"
                                            class="text-red-500 hover:text-red-700 font-medium">

                                            Delete

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="px-6 py-12 text-center text-[#C9B46B]">

                                {{ $search !== '' ? 'No loyalty plans match your search.' : 'No loyalty plans have been created yet.' }}

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>

