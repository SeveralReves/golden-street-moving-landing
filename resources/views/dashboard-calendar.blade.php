<x-app-layout>
    <x-slot name="header">{{ __('Calendar') }}</x-slot>

    <div
        data-vue="MoveCalendar"
        data-props='@json(["quotes" => $quotes, "config" => $config])'>
    </div>
</x-app-layout>
