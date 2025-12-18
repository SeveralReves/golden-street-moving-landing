@extends('layouts.default')

@section('content')

    @include('components.hero', [
        'title' => 'Veniam consectetur sit incididunt labore.',
        'description' => 'Ut elit dolor velit reprehenderit ipsum nulla nostrud fugiat id id deserunt adipisicing aliquip. Deserunt nulla laboris culpa ipsum commodo veniam sint laboris excepteur dolor ea magna id amet.',
        'button' => [
            'url' => '#',
            'title' => 'See More'
        ],
    ])


    @include('components.section-booking', [
        'title' => 'Book Your Move Online',
        'description' => 'Get a free quote in just a few simple steps.',
        'button' => [
            'url' => '#booking',
            'title' => 'Book your date now'
        ],
        'wp_action' => 'booking'
    ])

    @include('components.section-faq', [
        'title' => 'Frequently Asked Questions',
        'description' => "Have questions? We've got answers.",
        'cta' => [
            'text' => 'Contact Us',
            'url'  => '#booking'
        ],
        'faqs' => [
            [
            'q' => 'How is the price for my move determined?',
            'a' => 'Our pricing is based on an hourly rate which includes the truck, equipment and moving crew. We give you a detailed quote upfront with no hidden fees.'
            ],
            [
            'q' => 'How far in advance should I schedule my move?',
            'a' => 'We recommend 2–4 weeks in advance to secure your preferred date. For peak season, book earlier.'
            ],
            [
            'q' => 'What is included in your standard moving service?',
            'a' => 'Truck, crew, loading/unloading, basic protection blankets, and standard furniture assembly/disassembly.'
            ],
            [
            'q' => 'What kind of insurance coverage do you offer?',
            'a' => 'Basic valuation is included. Full-value protection is available upon request.'
            ],
            [
            'q' => 'What is your policy on rescheduling or cancellation?',
            'a' => 'You can reschedule up to 48 hours before the job without fees. See full policy in your quote.'
            ],
        ]
    ])




{{-- <div class="container">
    <p>This is the user content</p>
    
    <div
        data-vue="ExampleComponent"
        data-props='@json(["postId" => 123, "initial" => false])'>
    </div>

</div> --}}
@stop