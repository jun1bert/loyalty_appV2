<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-medium text-[#F7E7B2]">Code</label>
        <input type="text" name="code" value="{{ old('code', $promoCode?->code) }}" class="theme-input uppercase" placeholder="OWNER20" required>
        @error('code') <p class="mt-2 text-xs text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-[#F7E7B2]">Name</label>
        <input type="text" name="name" value="{{ old('name', $promoCode?->name) }}" class="theme-input" placeholder="Owner VIP discount">
        @error('name') <p class="mt-2 text-xs text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-[#F7E7B2]">Discount Type</label>
        <select name="discount_type" class="theme-input" required>
            @php($selectedType = old('discount_type', $promoCode?->discount_type ?? 'percentage'))
            <option value="percentage" {{ $selectedType === 'percentage' ? 'selected' : '' }}>Percentage</option>
            <option value="fixed" {{ $selectedType === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
        </select>
        @error('discount_type') <p class="mt-2 text-xs text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-[#F7E7B2]">Discount Value</label>
        <input type="number" name="discount_value" value="{{ old('discount_value', $promoCode?->discount_value) }}" class="theme-input" min="0.01" step="0.01" placeholder="10" required>
        @error('discount_value') <p class="mt-2 text-xs text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-[#F7E7B2]">Start Date</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', $promoCode?->starts_at?->format('Y-m-d')) }}" class="theme-input">
        @error('starts_at') <p class="mt-2 text-xs text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-[#F7E7B2]">Expiry Date</label>
        <input type="date" name="expires_at" value="{{ old('expires_at', $promoCode?->expires_at?->format('Y-m-d')) }}" class="theme-input">
        @error('expires_at') <p class="mt-2 text-xs text-red-300">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-[#F7E7B2]">Usage Limit</label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit', $promoCode?->usage_limit) }}" class="theme-input" min="1" step="1" placeholder="Leave blank for unlimited">
        @error('usage_limit') <p class="mt-2 text-xs text-red-300">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-center gap-3 self-end rounded-xl border border-[#D4AF37]/25 bg-black/20 px-4 py-3">
        <input type="checkbox" name="is_active" value="1" class="rounded border-[#B8860B] text-[#D4AF37] focus:ring-[#D4AF37]" {{ old('is_active', $promoCode?->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-[#F7E7B2]">Active</span>
    </label>
</div>

<div class="flex flex-col gap-3 border-t border-[#3A321F] pt-6 sm:flex-row">
    <button type="submit" class="btn-primary text-center">
        {{ $promoCode ? 'Update Promo Code' : 'Create Promo Code' }}
    </button>

    <a href="{{ route('promo-codes.index') }}" class="btn-secondary text-center">
        Cancel
    </a>
</div>
