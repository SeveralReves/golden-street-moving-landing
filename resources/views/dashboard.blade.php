<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    
  <div
      data-vue="BookingTable"
      data-props='@json(["quotes" => $quotes])'>
  </div>
</x-app-layout>
