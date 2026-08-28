<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[#0D0D0D] border border-[#D4AF37]/40 rounded-md font-semibold text-xs text-[#F7E7B2] uppercase tracking-widest shadow-sm hover:bg-[#1A1A1A] focus:outline-none focus:ring-2 focus:ring-[#D4AF37] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

