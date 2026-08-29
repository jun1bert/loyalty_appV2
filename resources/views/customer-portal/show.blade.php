<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                Customer Account
            </p>

            <h1 class="page-title">
                My Profile
            </h1>
        </div>
    </x-slot>

    @php
        $membership = $customer->loyaltyMembership;
        $plan = $membership?->loyaltyPlan;
        $birthDate = $customer->birth_date
            ? \Carbon\Carbon::parse($customer->birth_date)->format('Y-m-d')
            : null;
    @endphp

    @if(session('status') === 'customer-profile-updated')
        <div class="mb-6 rounded-lg border border-[#D4AF37]/30 bg-[#D4AF37]/10 px-4 py-3 text-sm text-[#F7E7B2]">
            Profile updated.
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="theme-card p-6 xl:col-span-1">
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                Edit Profile
            </p>

            <form method="POST" action="{{ route('customer.portal.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                @method('patch')

                <div class="flex items-center gap-4">
                    @if($customer->photo_path)
                        <span class="customer-avatar-md">
                            <img
                                src="{{ Storage::url($customer->photo_path) }}"
                                alt="{{ $customer->first_name }} {{ $customer->last_name }}"
                            >
                        </span>
                    @else
                        <div class="customer-avatar-md">
                            {{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <p class="font-medium text-[#F7E7B2] truncate">
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        </p>
                        <p class="text-xs text-[#C9B46B] mt-1 truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>

                <div>
                    <x-input-label for="first_name" value="First Name" />
                    <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $customer->first_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>

                <div>
                    <x-input-label for="last_name" value="Last Name" />
                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $customer->last_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>

                <div>
                    <x-input-label for="phone" value="Mobile Number" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $customer->phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="birth_date" value="Birth Date" />
                    <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $birthDate)" />
                    <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                </div>

                <div>
                    <x-input-label for="photo" value="Profile Photo" />
                    <input
                        id="photo"
                        name="photo"
                        type="file"
                        accept="image/*"
                        class="mt-1 block w-full rounded-lg border border-[#3A321F] bg-[#111111] px-3 py-2 text-sm text-[#F7E7B2] file:mr-3 file:rounded-md file:border-0 file:bg-[#D4AF37] file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-black"
                    >
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>

                <button type="submit" class="btn-primary">
                    Save Profile
                </button>
            </form>
        </div>

        <div class="xl:col-span-2 space-y-6">
            <div class="theme-card p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                    Plan Availed
                </p>

                @if($membership)
                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs text-[#C9B46B]">Plan</p>
                            <p class="mt-1 text-xl font-semibold text-[#F7E7B2]">{{ $plan?->name ?? 'Loyalty Plan' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-[#C9B46B]">Membership No.</p>
                            <p class="mt-1 font-medium text-[#F7E7B2]">{{ $membership->membership_code }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-[#C9B46B]">Discount</p>
                            <p class="mt-1 font-medium text-[#F7E7B2]">{{ number_format($plan?->discount_percentage ?? 0, 0) }}%</p>
                        </div>

                        <div>
                            <p class="text-xs text-[#C9B46B]">Status</p>
                            <p class="mt-1 font-medium text-[#F7E7B2]">{{ ucfirst($membership->status) }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-[#C9B46B]">Activated</p>
                            <p class="mt-1 font-medium text-[#F7E7B2]">{{ $membership->activated_at?->format('M d, Y') ?? 'Not available' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-[#C9B46B]">Valid Until</p>
                            <p class="mt-1 font-medium text-[#F7E7B2]">{{ $membership->expires_at?->format('M d, Y') ?? 'No expiration' }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-[#C9B46B]">
                        You do not have an active loyalty plan yet.
                    </p>
                @endif
            </div>

            <div class="theme-card p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                    Transactions
                </p>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#3A321F] text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.14em] text-[#C9B46B]">
                                <th class="py-3 pr-4 font-semibold">Date</th>
                                <th class="py-3 pr-4 font-semibold">Plan</th>
                                <th class="py-3 pr-4 font-semibold">Discount</th>
                                <th class="py-3 pr-4 font-semibold text-right">Total</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#3A321F] text-[#F7E7B2]">
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="py-3 pr-4 whitespace-nowrap">
                                        {{ $transaction->transaction_date?->format('M d, Y') ?? 'Not dated' }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        {{ $transaction->membership?->loyaltyPlan?->name ?? 'Loyalty Plan' }}
                                    </td>
                                    <td class="py-3 pr-4 whitespace-nowrap">
                                        PHP {{ number_format($transaction->discount_amount + $transaction->promo_discount_amount, 2) }}
                                    </td>
                                    <td class="py-3 pr-4 text-right whitespace-nowrap">
                                        PHP {{ number_format($transaction->total_amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-[#C9B46B]">
                                        No transactions yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
