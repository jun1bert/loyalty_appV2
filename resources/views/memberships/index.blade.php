<x-app-layout>

    <x-slot name="header">

        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#C7AD8A] mb-1">
                Loyalty Management
            </p>

            <h1 class="page-title">
                Memberships
            </h1>
        </div>

    </x-slot>


    <form method="GET" action="{{ route('memberships.index') }}" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="Search memberships, customers, phone, plan, or status"
            class="theme-input sm:max-w-md"
        >

        <div class="flex gap-2">
            <button type="submit" class="btn-primary">
                Search
            </button>

            @if($search !== '')
                <a href="{{ route('memberships.index') }}" class="btn-secondary">
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
                            Customer
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Membership
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Plan
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Discount
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Activated
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Expires
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Status
                        </th>

                        <th class="px-6 py-4 text-right font-semibold">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($memberships as $membership)

                        <tr class="border-t border-[#3A321F]
                                   hover:bg-[#151515]/60 transition">

                            <td class="px-6 py-4">

                                <p class="font-medium text-[#F6F0E8]">
                                    {{ $membership->customer->first_name }}
                                    {{ $membership->customer->last_name }}
                                </p>

                                @if($membership->customer->phone)

                                    <p class="text-xs text-[#B9A68F] mt-1">
                                        {{ $membership->customer->phone }}
                                    </p>

                                @endif

                            </td>


                            <td class="px-6 py-4">

                                <p class="font-medium text-[#F6F0E8]">
                                    {{ $membership->membership_code }}
                                </p>

                            </td>


                            <td class="px-6 py-4 text-[#E8D8C3]">

                                {{ $membership->loyaltyPlan?->name ?? '—' }}

                            </td>


                            <td class="px-6 py-4">

                                <span class="font-medium text-[#C7AD8A]">

                                    {{ number_format(
                                        $membership->loyaltyPlan?->discount_percentage ?? 0,
                                        0
                                    ) }}%

                                </span>

                            </td>


                            <td class="px-6 py-4 text-[#E8D8C3]">

                                {{ $membership->activated_at
                                    ? $membership->activated_at->format('M d, Y')
                                    : '—' }}

                            </td>


                            <td class="px-6 py-4 text-[#E8D8C3]">

                                {{ $membership->expires_at
                                    ? $membership->expires_at->format('M d, Y')
                                    : 'No Expiration' }}

                            </td>


                            <td class="px-6 py-4">

                                @if(
                                    $membership->status === 'active'
                                    &&
                                    (!$membership->expires_at || $membership->expires_at->isFuture())
                                )

                                    <span class="badge-active">
                                        Active
                                    </span>

                                @elseif(
                                    $membership->expires_at
                                    &&
                                    $membership->expires_at->isPast()
                                )

                                    <span class="badge-inactive">
                                        Expired
                                    </span>

                                @else

                                    <span class="badge-inactive">
                                        {{ ucfirst($membership->status) }}
                                    </span>

                                @endif

                            </td>


                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route('memberships.show', $membership) }}"
                                    class="text-[#C7AD8A]
                                           hover:text-[#E8D8C3]
                                           font-medium">

                                    View

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-6 py-12 text-center text-[#B9A68F]">

                                {{ $search !== '' ? 'No memberships match your search.' : 'No memberships found.' }}

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>


