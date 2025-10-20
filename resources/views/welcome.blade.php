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

    @include('components.section-booking', [
        'title' => 'Book Your Move Online',
        'description' => 'Get a free quote in just a few simple steps.',
        'button' => [
            'url' => '#',
            'title' => 'Book your date now'
        ],
        'wp_action' => 'booking'
    ])

{{-- <div class="container">
    <p>This is the user content</p>
    
    <div
        data-vue="ExampleComponent"
        data-props='@json(["postId" => 123, "initial" => false])'>
    </div>

</div> --}}
@stop