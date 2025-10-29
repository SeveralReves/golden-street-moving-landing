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

    @include('components.section-services', [
        'title' => 'Our Moving Services',
        'cards' => [
            [
                'title' => 'Residential Moving',
                'description' => 'Seamless home transitions, handled with care and precision by our expert team.',
                'image' => Vite::asset('resources/images/cards/services-1.jpeg'),
                'button' => [
                    'title' => 'Request this service',
                    'url' => '#'
                ]
            ],
            [
                'title' => 'Corporate Moving',
                'description' => 'Efficiency and minimal business disruption for your office relocation.',
                'image' => Vite::asset('resources/images/cards/services-2.png'),
                'button' => [
                    'title' => 'Request this service',
                    'url' => '#'
                ]
            ],
            [
                'title' => 'Packing Services',
                'description' => 'Professional packing and unpacking to save you time and protect your belongings.',
                'image' => Vite::asset('resources/images/cards/services-3.png'),
                'button' => [
                    'title' => 'Request this service',
                    'url' => '#'
                ]
            ],
            [
                'title' => 'Storage Solutions',
                'description' => 'Secure, flexible, and climate-controlled storage options for any need.',
                'image' => Vite::asset('resources/images/cards/services-4.png'),
                'button' => [
                    'title' => 'Request this service',
                    'url' => '#'
                ]
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

    @include('components.section-faq', [
        'title' => 'Frequently Asked Questions',
        'description' => "Have questions? We've got answers.",
        'cta' => [
            'text' => 'Contact Us',
            'url'  => '#contact'
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

    @include('components.section-reviews', [
        'title' => "Don't just take our word for it",
        'description' => "See what our happy customers say about their moving experience.",
        'cta' => [
            'text' => 'Book Your Move Now',
            'url'  => '#contact'
        ],
        'reviews' => [
            [
                'name' => 'John Doe',
                'avatar' => Vite::asset('resources/images/cards/review-1.png'),
                'rating' => 5,
                'text' => "The movers were professional and friendly. Highly recommend!",
                'source' => 'Google',
                'url' => 'https://g.page/tu-listing/review', // opcional
                'date' => '2025-08-02'
            ],
            [
                'name' => 'Jane Smith',
                'avatar' => Vite::asset('resources/images/cards/review-2.png'),
                'rating' => 4.5,
                'text' => "Everything arrived in perfect condition. Great service.",
                'source' => 'Google',
                'url' => 'https://g.page/tu-listing/review',
                'date' => '2025-07-15'
            ],
            [
                'name' => 'Peter Jones',
                'avatar' => Vite::asset('resources/images/cards/review-3.png'),
                'rating' => 4,
                'text' => "Smooth process from quote to final delivery.",
                'source' => 'Google',
                'url' => 'https://g.page/tu-listing/review',
                'date' => '2025-06-20'
            ],
            [
                'name' => 'Sarah Lee',
                'avatar' => Vite::asset('resources/images/cards/review-4.png'),
                'rating' => 5,
                'text' => "Incredibly careful with my fragile items. A+",
                'source' => 'Google',
                'url' => 'https://g.page/tu-listing/review',
                'date' => '2025-05-11'
            ],
            [
                'name' => 'John Doe',
                'avatar' => Vite::asset('resources/images/cards/review-1.png'),
                'rating' => 5,
                'text' => "The movers were professional and friendly. Highly recommend!",
                'source' => 'Google',
                'url' => 'https://g.page/tu-listing/review', // opcional
                'date' => '2025-08-02'
            ],
        ],
        // Opcional: controla el slider
        'slider' => [
            'autoplay' => true,
            'speed' => 4000
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