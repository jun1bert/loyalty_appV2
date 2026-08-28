<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg border border-[var(--desert-rock)] bg-[var(--desert-rock)] px-5 py-2.5 text-xs font-bold uppercase tracking-[0.16em] text-[var(--feather-white)] shadow-lg shadow-[#D4AF37]/20 transition hover:bg-[#F2C94C] focus:outline-none focus:ring-2 focus:ring-[var(--desert-rock)] focus:ring-offset-2 active:bg-[#B8860B]']) }}>
    {{ $slot }}
</button>


