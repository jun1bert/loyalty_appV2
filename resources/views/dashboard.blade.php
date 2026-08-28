<x-app-layout>

    <x-slot name="header">

        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                Overview
            </p>

            <h1 class="page-title">
                Dashboard
            </h1>
        </div>

    </x-slot>


    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Customers --}}
        <div class="theme-card p-6">

            <p class="text-sm text-[#C9B46B]">
                Total Customers
            </p>

            <p class="font-serif text-4xl text-[#F7E7B2] mt-3">
                {{ number_format($totalCustomers) }}
            </p>

            <p class="text-xs text-[#D4AF37] mt-2">
                Loyalty customers
            </p>

        </div>


        {{-- Memberships --}}
        <div class="theme-card p-6">

            <p class="text-sm text-[#C9B46B]">
                Active Memberships
            </p>

            <p class="font-serif text-4xl text-[#F7E7B2] mt-3">
                {{ number_format($activeMemberships) }}
            </p>

            <p class="text-xs text-[#D4AF37] mt-2">
                Currently active
            </p>

        </div>


        {{-- Services --}}
        <div class="theme-card p-6">

            <p class="text-sm text-[#C9B46B]">
                Services
            </p>

            <p class="font-serif text-4xl text-[#F7E7B2] mt-3">
                {{ number_format($activeServices) }}
            </p>

            <p class="text-xs text-[#D4AF37] mt-2">
                Active services
            </p>

        </div>


        {{-- Discounts --}}
        <div class="theme-card p-6">

            <p class="text-sm text-[#C9B46B]">
                Discounts Given
            </p>

            <p class="font-serif text-4xl text-[#F7E7B2] mt-3">
                PHP {{ number_format($discountsThisMonth, 2) }}
            </p>

            <p class="text-xs text-[#D4AF37] mt-2">
                This month
            </p>

        </div>

    </div>


    {{-- Welcome --}}
    <div class="theme-card mt-6 p-8">

        <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
            Martinis & Manicures
        </p>

        <h2 class="text-2xl mt-2">
            Loyalty Management
        </h2>

        <p class="text-sm text-[#C9B46B] mt-3 max-w-2xl leading-6">
            Manage loyalty memberships, customer discounts,
            services and transactions from your dashboard.
        </p>

    </div>

</x-app-layout>
