<x-guest-layout>
    <div class="relative text-center">
        <a href="/" class="mx-auto block w-40 sm:w-48">
            <img
                src="{{ asset('images/martinis-logo.png') }}"
                alt="Martinis and Manicures"
                class="h-auto w-full brightness-0 invert sepia saturate-[2] hue-rotate-[2deg]"
            >
        </a>

        <div class="auth-divider my-5 sm:my-6">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="m12 2 2.3 7.7L22 12l-7.7 2.3L12 22l-2.3-7.7L2 12l7.7-2.3L12 2Z"></path>
            </svg>
        </div>

        <h1 class="gold-foil-text font-serif text-3xl font-semibold sm:text-4xl">
            Welcome Back
        </h1>
        <p class="mt-2 text-sm text-[#E8D8C3] sm:text-base">
            Sign in to your management portal
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="relative mt-6 space-y-4 sm:space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-[#F6F0E8]">
                Email
            </label>

            <div class="auth-input-wrap">
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a2 2 0 0 1-2.06 0L2 7"></path>
                </svg>
                <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="mb-2 block text-sm font-semibold text-[#F6F0E8]">
                Password
            </label>

            <div class="auth-input-wrap">
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect width="18" height="11" x="3" y="11" rx="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input id="password" class="auth-input" type="password" name="password" placeholder="Password" required autocomplete="current-password">
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#C7AD8A]/60 bg-black/30 text-[#C7AD8A] shadow-sm focus:ring-[#C7AD8A]" name="remember">
                <span class="ms-2 text-sm font-medium text-[#E8D8C3]">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-semibold text-[#E8D8C3] hover:text-[#F6F0E8] focus:outline-none focus:ring-2 focus:ring-[#C7AD8A] focus:ring-offset-2" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary flex w-full items-center justify-center gap-3 px-5 py-3 text-sm uppercase tracking-[0.22em] sm:py-3.5 sm:text-base">
            <span>{{ __('Sign In') }}</span>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14"></path>
                <path d="m12 5 7 7-7 7"></path>
            </svg>
        </button>
    </form>

    <div class="relative mt-6 text-center">
        <div class="auth-divider mb-4">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path>
                <path d="m9 12 2 2 4-4"></path>
            </svg>
        </div>

        <p class="text-sm text-[#B9A68F]">
            Secure Management Portal
        </p>
    </div>
</x-guest-layout>


