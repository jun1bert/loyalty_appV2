<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="premium-eyebrow mb-1">Promo Codes</p>
            <h1 class="page-title">Add Promo Code</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="theme-card p-6 sm:p-8">
            <form method="POST" action="{{ route('promo-codes.store') }}" class="space-y-6">
                @csrf
                @include('promo-codes.form', ['promoCode' => null])
            </form>
        </div>
    </div>
</x-app-layout>
