<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#C7AD8A] mb-1">
                    Loyalty Management
                </p>

                <h1 class="page-title">
                    Customers
                </h1>
            </div>

            <a
                href="{{ route('customers.create') }}"
                class="btn-primary inline-flex items-center justify-center">

                + Add Customer

            </a>

        </div>

    </x-slot>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-[#3A321F]
                    bg-[#080808] px-4 py-3 text-sm text-[#E8D8C3]">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('customers.index') }}" class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input
            type="search"
            name="search"
            value="{{ $search }}"
            placeholder="Search customers, phone, membership, or plan"
            class="theme-input sm:max-w-md"
        >

        <div class="flex gap-2">
            <button type="submit" class="btn-primary">
                Search
            </button>

            @if($search !== '')
                <a href="{{ route('customers.index') }}" class="btn-secondary">
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
                            Discount
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Expires
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

                    @forelse($customers as $customer)

                        @php
                            $membership = $customer->loyaltyMembership;
                        @endphp

                        <tr class="border-t border-[#3A321F]
                                   hover:bg-[#151515]/60 transition">

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">
                                    @if($customer->photo_path)
                                        <span class="customer-avatar-sm">
                                            <img
                                                src="{{ Storage::url($customer->photo_path) }}"
                                                alt="{{ $customer->first_name }} {{ $customer->last_name }}"
                                            >
                                        </span>
                                    @else
                                        <div class="customer-avatar-sm">
                                            {{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div>
                                        <p class="font-medium text-[#F6F0E8]">
                                            {{ $customer->first_name }}
                                            {{ $customer->last_name }}
                                        </p>

                                        @if($customer->phone)
                                            <p class="text-xs text-[#B9A68F] mt-1">
                                                {{ $customer->phone }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                            </td>

                            <td class="px-6 py-4">

                                @if($membership)

                                    <p class="font-medium text-[#F6F0E8]">
                                        {{ $membership->membership_code }}
                                    </p>

                                    <p class="text-xs text-[#B9A68F] mt-1">
                                        {{ $membership->loyaltyPlan?->name }}
                                    </p>

                                @else

                                    <span class="text-[#B9A68F]">
                                        No membership
                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-4">

                                @if($membership?->loyaltyPlan)

                                    <span class="font-medium text-[#C7AD8A]">
                                        {{ number_format($membership->loyaltyPlan->discount_percentage, 0) }}%
                                    </span>

                                @else
                                    —
                                @endif

                            </td>

                            <td class="px-6 py-4 text-[#E8D8C3]">

                                @if($membership?->expires_at)
                                    {{ $membership->expires_at->format('M d, Y') }}
                                @else
                                    —
                                @endif

                            </td>

                            <td class="px-6 py-4">

                                @if($membership && $membership->status === 'active')

                                    <span class="badge-active">
                                        Active
                                    </span>

                                @elseif($membership)

                                    <span class="badge-inactive">
                                        {{ ucfirst($membership->status) }}
                                    </span>

                                @else

                                    <span class="badge-inactive">
                                        None
                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-4">

                                <div class="flex justify-end gap-4">

                                    <a
                                        href="{{ route('customers.show', $customer) }}"
                                        class="text-[#C7AD8A] hover:text-[#E8D8C3] font-medium">
                                        View
                                    </a>

                                    <a
                                        href="{{ route('customers.edit', $customer) }}"
                                        class="text-[#C7AD8A] hover:text-[#E8D8C3] font-medium">
                                        Edit
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6"
                                class="px-6 py-12 text-center text-[#B9A68F]">
                                {{ $search !== '' ? 'No customers match your search.' : 'No customers have been registered yet.' }}
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>


