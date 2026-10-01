<x-app-layout>
    <x-slot name="header">{{ __('Pricing') }}</x-slot>

    <div
        data-vue="PricingDashboard"
        data-props='@json(["settings" => $settings])'>
    </div>
</x-app-layout>
