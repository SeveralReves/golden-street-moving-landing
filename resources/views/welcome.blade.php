@extends('layouts.default')

@section('content')

<p>This is the user content</p>

         {{-- Isla Vue con props JSON --}}
        <div
            data-vue="ExampleComponent"
            data-props='@json(["postId" => 123, "initial" => false])'>
        </div>
@stop