<x-app-layout>
    <x-slot name="header">{{ __('Content') }}</x-slot>

    <div
        data-vue="ContentManager"
        data-props='@json(["sections" => $sections])'>
    </div>
</x-app-layout>
