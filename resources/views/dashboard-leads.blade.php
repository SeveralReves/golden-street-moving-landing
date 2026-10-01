<x-app-layout>
    <x-slot name="header">{{ __('Leads') }}</x-slot>

    <div
        data-vue="BookingTable"
        data-props='@json(["quotes" => $quotes])'>
    </div>
</x-app-layout>
