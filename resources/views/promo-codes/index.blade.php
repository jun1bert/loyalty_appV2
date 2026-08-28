<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="premium-eyebrow mb-1">Loyalty Management</p>
                <h1 class="page-title">Promo Codes</h1>
            </div>

            <a href="{{ route('promo-codes.create') }}" class="btn-primary inline-flex justify-center">
                + Add Promo Code
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-[#D4AF37]/30 bg-[#0D0D0D] px-4 py-3 text-sm text-[#E8DDAA]">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-lg border border-red-500/30 bg-red-950/20 px-4 py-3 text-sm text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="theme-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="theme-table-header">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Code</th>
                        <th class="px-6 py-4 text-left font-semibold">Discount</th>
                        <th class="px-6 py-4 text-left font-semibold">Validity</th>
                        <th class="px-6 py-4 text-left font-semibold">Uses</th>
                        <th class="px-6 py-4 text-left font-semibold">Status</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promoCodes as $promoCode)
                        <tr class="border-t border-[#3A321F] hover:bg-[#1A1A1A]/60">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-[#F7E7B2]">{{ $promoCode->code }}</p>
                                @if($promoCode->name)
                                    <p class="mt-1 text-xs text-[#C9B46B]">{{ $promoCode->name }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-[#E8DDAA]">
                                @if($promoCode->discount_type === 'percentage')
                                    {{ number_format($promoCode->discount_value, 0) }}%
                                @else
                                    PHP {{ number_format($promoCode->discount_value, 2) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-[#E8DDAA]">
                                {{ $promoCode->starts_at?->format('M d, Y') ?? 'Anytime' }}
                                -
                                {{ $promoCode->expires_at?->format('M d, Y') ?? 'No expiry' }}
                            </td>
                            <td class="px-6 py-4 text-[#E8DDAA]">
                                {{ number_format($promoCode->transactions_count) }}
                                @if($promoCode->usage_limit)
                                    / {{ number_format($promoCode->usage_limit) }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $promoCode->isAvailable() ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $promoCode->isAvailable() ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-4">
                                    <a href="{{ route('promo-codes.edit', $promoCode) }}" class="font-medium text-[#D4AF37] hover:text-[#F2C94C]">Edit</a>
                                    <form method="POST" action="{{ route('promo-codes.destroy', $promoCode) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-200 hover:text-red-100" onclick="return confirm('Delete this promo code?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[#C9B46B]">
                                No promo codes have been created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
