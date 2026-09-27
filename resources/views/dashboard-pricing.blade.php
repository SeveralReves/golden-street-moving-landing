<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pricing') }}
        </h2>
    </x-slot>

    <div
        data-vue="PricingDashboard"
        data-props='@json(["settings" => $settings])'>
    </div>
</x-app-layout>
