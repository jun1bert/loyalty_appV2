<x-app-layout>

    <x-slot name="header">

        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#C7AD8A] mb-1">
                Services
            </p>

            <h1 class="page-title">
                Add Service
            </h1>
        </div>

    </x-slot>


    <div class="max-w-2xl">

        <div class="theme-card p-6 sm:p-8">

            <form method="POST"
                  action="{{ route('services.store') }}"
                  class="space-y-6">

                @csrf


                <div>

                    <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                        Service Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="theme-input"
                        placeholder="Example: Classic Manicure"
                        required
                    >

                    @error('name')
                        <p class="text-red-600 text-xs mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div>

                    <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                        Price
                    </label>

                    <div class="relative">

                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#B9A68F]">
                            ₱
                        </span>

                        <input
                            type="number"
                            name="price"
                            value="{{ old('price') }}"
                            step="0.01"
                            min="0"
                            class="theme-input pl-8"
                            placeholder="0.00"
                            required
                        >

                    </div>

                    @error('price')
                        <p class="text-red-600 text-xs mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div class="border-t border-[#3A321F] pt-5 space-y-4">

                    <label class="flex items-center gap-3 cursor-pointer">

                        <input
                            type="checkbox"
                            name="is_package"
                            value="1"
                            class="rounded border-[#8B765B] text-[#C7AD8A] focus:ring-[#C7AD8A]"
                            {{ old('is_package') ? 'checked' : '' }}
                        >

                        <div>

                            <p class="text-sm font-medium text-[#F6F0E8]">
                                Multi-session Package
                            </p>

                            <p class="text-xs text-[#B9A68F]">
                                Discount is applied once when the full package is paid.
                            </p>

                        </div>

                    </label>

                    <div>
                        <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                            Sessions Included
                        </label>

                        <input
                            type="number"
                            name="session_count"
                            value="{{ old('session_count') }}"
                            min="2"
                            step="1"
                            class="theme-input"
                            placeholder="Example: 5"
                        >

                        @error('session_count')
                            <p class="text-red-600 text-xs mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer">

                        <input
                            type="checkbox"
                            name="discount_eligible"
                            value="1"
                            class="rounded border-[#8B765B] text-[#C7AD8A] focus:ring-[#C7AD8A]"
                            {{ old('discount_eligible', true) ? 'checked' : '' }}
                        >

                        <div>

                            <p class="text-sm font-medium text-[#F6F0E8]">
                                Loyalty Discount Eligible
                            </p>

                            <p class="text-xs text-[#B9A68F]">
                                Members can receive their loyalty discount for this service.
                            </p>

                        </div>

                    </label>


                    <label class="flex items-center gap-3 cursor-pointer">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="rounded border-[#8B765B] text-[#C7AD8A] focus:ring-[#C7AD8A]"
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >

                        <div>

                            <p class="text-sm font-medium text-[#F6F0E8]">
                                Active Service
                            </p>

                            <p class="text-xs text-[#B9A68F]">
                                Active services can be used in loyalty transactions.
                            </p>

                        </div>

                    </label>

                </div>


                <div class="flex items-center gap-3 pt-4">

                    <button type="submit"
                            class="btn-primary">
                        Save Service
                    </button>

                    <a href="{{ route('services.index') }}"
                       class="btn-secondary">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>

