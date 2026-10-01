<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendar') }}
        </h2>
    </x-slot>

    <div
        data-vue="MoveCalendar"
        data-props='@json(["quotes" => $quotes, "config" => $config])'>
    </div>
</x-app-layout>
