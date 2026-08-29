<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-[#080808] border border-[#C7AD8A]/40 rounded-md font-semibold text-xs text-[#F6F0E8] uppercase tracking-widest shadow-sm hover:bg-[#151515] focus:outline-none focus:ring-2 focus:ring-[#C7AD8A] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>


