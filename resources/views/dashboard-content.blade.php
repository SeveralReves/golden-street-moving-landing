<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Landing Page Content') }}
        </h2>
    </x-slot>

    <div
        data-vue="ContentManager"
        data-props='@json(["sections" => $sections])'>
    </div>
</x-app-layout>
