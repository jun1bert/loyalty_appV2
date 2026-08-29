<x-app-layout>

    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#C7AD8A] mb-1">
                Customers
            </p>

            <h1 class="page-title">
                Edit Customer
            </h1>
        </div>
    </x-slot>

    <div class="max-w-3xl">

        <div class="theme-card p-6 sm:p-8">

            <form method="POST"
                  action="{{ route('customers.update', $customer) }}"
                  enctype="multipart/form-data"
                  class="space-y-6">

                @csrf
                @method('PUT')

                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-[#C7AD8A] mb-4">
                        Customer Information
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                                First Name
                            </label>

                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name', $customer->first_name) }}"
                                class="theme-input"
                                required
                            >

                            @error('first_name')
                                <p class="text-red-600 text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                                Last Name
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name', $customer->last_name) }}"
                                class="theme-input"
                                required
                            >

                            @error('last_name')
                                <p class="text-red-600 text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                                Mobile Number
                            </label>

                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone', $customer->phone) }}"
                                class="theme-input"
                            >

                            @error('phone')
                                <p class="text-red-600 text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                                Birth Date
                            </label>

                            <input
                                type="date"
                                name="birth_date"
                                value="{{ old('birth_date', $customer->birth_date) }}"
                                class="theme-input"
                            >

                            @error('birth_date')
                                <p class="text-red-600 text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-[#F6F0E8] mb-2">
                                Customer Photo
                                <span class="text-[#B9A68F] font-normal">(optional)</span>
                            </label>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                @if($customer->photo_path)
                                    <span class="customer-avatar-md">
                                        <img
                                            src="{{ Storage::url($customer->photo_path) }}"
                                            alt="{{ $customer->first_name }} {{ $customer->last_name }}"
                                        >
                                    </span>
                                @endif

                                <div class="flex-1">
                                    <input
                                        type="file"
                                        name="photo"
                                        accept="image/*"
                                        class="theme-input file:mr-4 file:rounded-lg file:border-0 file:bg-[#C7AD8A] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#080808]"
                                    >

                                    <p class="text-xs text-[#B9A68F] mt-2">
                                        Upload a new photo to replace the current one. Maximum size: 2 MB.
                                    </p>
                                </div>
                            </div>

                            @error('photo')
                                <p class="text-red-600 text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                </div>

                @if($customer->loyaltyMembership)
                    <div class="rounded-xl bg-[#3A321F]/50 p-5">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#C7AD8A]">
                            Membership
                        </p>

                        <p class="font-serif text-xl text-[#F6F0E8] mt-2">
                            {{ $customer->loyaltyMembership->membership_code }}
                        </p>

                        <p class="text-sm text-[#D8C98A] mt-2">
                            Membership plan changes are managed separately from customer contact details.
                        </p>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3">

                    <button
                        type="submit"
                        class="btn-primary text-center">
                        Update Customer
                    </button>

                    <a
                        href="{{ route('customers.show', $customer) }}"
                        class="btn-secondary text-center">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>


