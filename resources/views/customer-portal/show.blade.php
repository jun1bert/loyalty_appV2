@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

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

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(320px,410px)_1fr] gap-8 items-start">
        <div class="theme-card p-6 sm:p-7 xl:col-span-1">
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                Edit Profile
            </p>

            <form method="POST" action="{{ route('customer.portal.update') }}" enctype="multipart/form-data" class="mt-7 space-y-6">
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

                <div class="space-y-2">
                    <x-input-label for="first_name" value="First Name" />
                    <x-text-input id="first_name" name="first_name" type="text" class="block w-full bg-[#0B0B0B] text-[#F7E7B2] placeholder:text-[#C9B46B]/60 border-[#D4AF37]/35 focus:border-[#D4AF37] focus:ring-[#D4AF37]/25" :value="old('first_name', $customer->first_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="last_name" value="Last Name" />
                    <x-text-input id="last_name" name="last_name" type="text" class="block w-full bg-[#0B0B0B] text-[#F7E7B2] placeholder:text-[#C9B46B]/60 border-[#D4AF37]/35 focus:border-[#D4AF37] focus:ring-[#D4AF37]/25" :value="old('last_name', $customer->last_name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="phone" value="Mobile Number" />
                    <x-text-input id="phone" name="phone" type="text" class="block w-full bg-[#0B0B0B] text-[#F7E7B2] placeholder:text-[#C9B46B]/60 border-[#D4AF37]/35 focus:border-[#D4AF37] focus:ring-[#D4AF37]/25" :value="old('phone', $customer->phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="birth_date" value="Birth Date" />
                    <x-text-input id="birth_date" name="birth_date" type="date" class="block w-full bg-[#0B0B0B] text-[#F7E7B2] placeholder:text-[#C9B46B]/60 border-[#D4AF37]/35 focus:border-[#D4AF37] focus:ring-[#D4AF37]/25 [color-scheme:dark]" :value="old('birth_date', $birthDate)" />
                    <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="photo" value="Profile Photo" />
                    <input
                        id="photo"
                        name="photo"
                        type="file"
                        accept="image/*"
                        class="block w-full rounded-lg border border-[#D4AF37]/35 bg-[#0B0B0B] px-3 py-2.5 text-sm text-[#F7E7B2] file:mr-3 file:rounded-md file:border-0 file:bg-[#D4AF37] file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-black focus:border-[#D4AF37] focus:outline-none focus:ring-2 focus:ring-[#D4AF37]/25"
                    >
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>

                <button type="submit" class="btn-primary">
                    Save Profile
                </button>
            </form>
        </div>

        <div class="xl:col-span-1 space-y-8">
            <div class="theme-card p-6 sm:p-7">
                <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                    Plan Availed
                </p>

                @if($membership)
                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-[1fr_220px] gap-8 items-start">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <p class="text-xs text-[#C9B46B]">Plan</p>
                                <p class="mt-1 text-xl font-semibold text-[#F7E7B2]">{{ $plan?->name ?? 'Loyalty Plan' }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-[#C9B46B]">Membership No.</p>
                                <p class="mt-1 font-medium text-[#F7E7B2] break-words">{{ $membership->membership_code }}</p>
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

                        <div class="rounded-xl border border-[#D4AF37]/30 bg-[#0B0B0B] p-4 text-center">
                            <div class="mx-auto w-fit rounded-lg bg-white p-3">
                                {!! QrCode::size(160)->generate($membership->qr_token) !!}
                            </div>
                            <p class="mt-3 text-xs uppercase tracking-[0.14em] text-[#D4AF37]">
                                Membership QR
                            </p>
                            <p class="mt-1 text-xs text-[#C9B46B]">
                                Show this to staff.
                            </p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-[#C9B46B]">
                        You do not have an active loyalty plan yet.
                    </p>
                @endif
            </div>

            <div class="theme-card p-6 sm:p-7">
                <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37]">
                    Transactions
                </p>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full table-fixed border-separate border-spacing-0 text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.14em] text-[#C9B46B]">
                                <th class="w-1/4 border-b border-[#3A321F] pb-3 pr-6 font-semibold">Date</th>
                                <th class="w-1/4 border-b border-[#3A321F] pb-3 pr-6 font-semibold">Plan</th>
                                <th class="w-1/4 border-b border-[#3A321F] pb-3 pr-6 font-semibold">Discount</th>
                                <th class="w-1/4 border-b border-[#3A321F] pb-3 text-right font-semibold">Total</th>
                            </tr>
                        </thead>

                        <tbody class="text-[#F7E7B2]">
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="border-b border-[#3A321F]/80 py-4 pr-6 whitespace-nowrap">
                                        {{ $transaction->transaction_date?->format('M d, Y') ?? 'Not dated' }}
                                    </td>
                                    <td class="border-b border-[#3A321F]/80 py-4 pr-6">
                                        {{ $transaction->membership?->loyaltyPlan?->name ?? 'Loyalty Plan' }}
                                    </td>
                                    <td class="border-b border-[#3A321F]/80 py-4 pr-6 whitespace-nowrap">
                                        PHP {{ number_format($transaction->discount_amount + $transaction->promo_discount_amount, 2) }}
                                    </td>
                                    <td class="border-b border-[#3A321F]/80 py-4 text-right whitespace-nowrap">
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
