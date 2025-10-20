@extends('layouts.default')

@section('content')

    @include('components.hero', [
        'title' => 'Your move is easy, safe, and on time.',
        'description' => 'We are a professional moving service that ensures a stress-free experience from start to finish. Our team of experts handles everything with care and precision.',
        'button' => [
            'url' => '#',
            'title' => 'Book your date now'
        ],
    ])

{{-- <div class="container">
    <p>This is the user content</p>
    
    <div
        data-vue="ExampleComponent"
        data-props='@json(["postId" => 123, "initial" => false])'>
    </div>

</div> --}}
@stop