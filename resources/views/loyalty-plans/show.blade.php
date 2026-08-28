<x-app-layout>

    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                Loyalty Plans
            </p>

            <h1 class="page-title">
                Plan Details
            </h1>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">

        <div class="theme-card p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                        Loyalty Plan
                    </p>

                    <h2 class="font-serif text-3xl text-[#F7E7B2] mt-2">
                        {{ $loyaltyPlan->name }}
                    </h2>
                </div>

                @if($loyaltyPlan->is_active)
                    <span class="badge-active self-start">Active</span>
                @else
                    <span class="badge-inactive self-start">Inactive</span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Card Price
                    </p>

                    <p class="font-medium text-[#F7E7B2] mt-1">
                        PHP {{ number_format($loyaltyPlan->price, 2) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Discount
                    </p>

                    <p class="font-medium text-[#F7E7B2] mt-1">
                        {{ number_format($loyaltyPlan->discount_percentage, 2) }}%
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Validity
                    </p>

                    <p class="font-medium text-[#F7E7B2] mt-1">
                        {{ $loyaltyPlan->validity_months }}
                        {{ $loyaltyPlan->validity_months == 1 ? 'month' : 'months' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Minimum Spend
                    </p>

                    <p class="font-medium text-[#F7E7B2] mt-1">
                        PHP {{ number_format($loyaltyPlan->minimum_spend ?? 0, 2) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#C9B46B]">
                        Active Memberships
                    </p>

                    <p class="font-medium text-[#F7E7B2] mt-1">
                        {{ $loyaltyPlan->memberships()->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a
                href="{{ route('loyalty-plans.edit', $loyaltyPlan) }}"
                class="btn-primary text-center">
                Edit Loyalty Plan
            </a>

            <a
                href="{{ route('loyalty-plans.index') }}"
                class="btn-secondary text-center">
                Back to Loyalty Plans
            </a>
        </div>

    </div>

</x-app-layout>

