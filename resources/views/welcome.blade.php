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

    @include('components.section-benefits', [
        'title' => 'Your Smoothest Move, Guaranteed',
        'cards' => [
            [
                'title' => 'Fully Insured & Secure',
                'description' => 'All moves are protected and items are handled with the utmost care.',
                'icon' => Vite::asset('resources/images/icons/shield.svg')
            ],
            [
                'title' => 'Always On Time',
                'description' => 'Reliable and punctual service you can count on, every time.',
                'icon' => Vite::asset('resources/images/icons/clock.svg')
            ],
            [
                'title' => 'Friendly, Professional Team',
                'description' => 'Our trained and courteous staff are here to help make your move a breeze.',
                'icon' => Vite::asset('resources/images/icons/smile-outlined.svg')
            ],
        ]
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