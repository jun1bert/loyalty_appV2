<x-app-layout>

    <x-slot name="header">

        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#D4AF37] mb-1">
                Services
            </p>

            <h1 class="page-title">
                Edit Service
            </h1>
        </div>

    </x-slot>


    <div class="max-w-2xl">

        <div class="theme-card p-6 sm:p-8">

            <form method="POST"
                  action="{{ route('services.update', $service) }}"
                  class="space-y-6">

                @csrf
                @method('PUT')


                <div>

                    <label class="block text-sm font-medium text-[#F7E7B2] mb-2">
                        Service Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $service->name) }}"
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

                    <label class="block text-sm font-medium text-[#F7E7B2] mb-2">
                        Price
                    </label>

                    <div class="relative">

                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#C9B46B]">
                            ₱
                        </span>

                        <input
                            type="number"
                            name="price"
                            value="{{ old('price', $service->price) }}"
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
                            class="rounded border-[#B8860B] text-[#D4AF37] focus:ring-[#D4AF37]"
                            {{ old('is_package', $service->is_package) ? 'checked' : '' }}
                        >

                        <div>

                            <p class="text-sm font-medium text-[#F7E7B2]">
                                Multi-session Package
                            </p>

                            <p class="text-xs text-[#C9B46B]">
                                Discount is applied once when the full package is paid.
                            </p>

                        </div>

                    </label>

                    <div>
                        <label class="block text-sm font-medium text-[#F7E7B2] mb-2">
                            Sessions Included
                        </label>

                        <input
                            type="number"
                            name="session_count"
                            value="{{ old('session_count', $service->session_count) }}"
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
                            class="rounded border-[#B8860B] text-[#D4AF37] focus:ring-[#D4AF37]"
                            {{ old('discount_eligible', $service->discount_eligible) ? 'checked' : '' }}
                        >

                        <div>

                            <p class="text-sm font-medium text-[#F7E7B2]">
                                Loyalty Discount Eligible
                            </p>

                            <p class="text-xs text-[#C9B46B]">
                                Members can receive their loyalty discount for this service.
                            </p>

                        </div>

                    </label>


                    <label class="flex items-center gap-3 cursor-pointer">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="rounded border-[#B8860B] text-[#D4AF37] focus:ring-[#D4AF37]"
                            {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                        >

                        <div>

                            <p class="text-sm font-medium text-[#F7E7B2]">
                                Active Service
                            </p>

                            <p class="text-xs text-[#C9B46B]">
                                Active services can be used in loyalty transactions.
                            </p>

                        </div>

                    </label>

                </div>


                <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-4">

                    <button
                        type="submit"
                        class="btn-primary text-center">
                        Update Service
                    </button>

                    <a
                        href="{{ route('services.index') }}"
                        class="btn-secondary text-center">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
