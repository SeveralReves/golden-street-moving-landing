@extends('layouts.default')

@section('content')

    @include('components.hero', [
        'title' => 'Your move is easy, safe, and on time.',
        'description' => 'We are a professional moving service that ensures a stress-free experience from start to finish. Our team of experts handles everything with care and precision.',
        'button' => [
            'url' => '#booking',
            'title' => 'Book your date now'
        ],
    ])

    @include('components.section-benefits', [
        'title' => 'Your Smoothest Move, Guaranteed',
        'cards' => [
            [
                'title' => 'Fully Insured & Secure',
                'description' => 'All moves are protected and items are handled with the utmost care.',
                'icon' => asset('/images/icons/shield.svg')
            ],
            [
                'title' => 'Always On Time',
                'description' => 'Reliable and punctual service you can count on, every time.',
                'icon' => asset('/images/icons/clock.svg')
            ],
            [
                'title' => 'Friendly, Professional Team',
                'description' => 'Our trained and courteous staff are here to help make your move a breeze.',
                'icon' => asset('/images/icons/smile-outlined.svg')
            ],
        ]
    ])

    @include('components.section-services', [
        'title' => 'Our Moving Services',
        'cards' => [
            [
                'title' => 'Residential Moving',
                'description' => 'Seamless home transitions, handled with care and precision by our expert team.',
                'image' => asset('/images/cards/services-1.jpeg'),
                'button' => [
                    'title' => 'Request this service',
                    'url' => '#booking'
                ]
            ],
            [
                'title' => 'Corporate Moving',
                'description' => 'Efficiency and minimal business disruption for your office relocation.',
                'image' => asset('/images/cards/services-2.png'),
                'button' => [
                    'title' => 'Request this service',
                    'url' => '#booking'
                ]
            ],
            [
                'title' => 'Packing Services',
                'description' => 'Professional packing and unpacking to save you time and protect your belongings.',
                'image' => asset('/images/cards/services-3.png'),
                'button' => [
                    'title' => 'Request this service',
                    'url' => '#booking'
                ]
            ],
        ]
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

    @include('components.banner', [
        'title' => 'Out-of-state transfers',
        'description' => 'Enjoy complete coverage, real-time tracking, and the peace of mind that your belongings are in good hands.'
    ])
    @include('components.section-reviews', [
        'title' => "Don't just take our word for it",
        'description' => "See what our happy customers say about their moving experience.",
        'cta' => [
            'text' => 'Book Your Move Now',
            'url'  => '#booking'
        ],
        'reviews' => [
            [
                'name' => 'Maribel Garcia',
                'avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocLMgSkWXUY-f5sROhxj0PxpkKZZsY2uYI9dbL01bBJUKbR1ig=w36-h36-p-rp-mo-br100',
                'rating' => 5,
                'text' => "I did my move with them and they gave me specialized, professional service. I definitely recommend working with this company. 👍",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2026-08-22'
            ],
            [
                'name' => 'Mateo Arteta',
                'avatar' => 'https://lh3.googleusercontent.com/a-/ALV-UjWJr-QwnmwKle3a0bIkfTyQ3NUSkZfw6k5gGLV9D-swgFl4-wzH=w36-h36-p-rp-mo-ba12-br100',
                'rating' => 5,
                'text' => "I had an incredible experience with Golden Streets Moving Company! Aaby and his team are top-notch professionals. From start to finish, they treated my belongings with the utmost care and attention. I was impressed by how efficient they were.",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2025-08-15'
            ],
            [
                'name' => 'Paramount Insurance and Multiservice',
                'avatar' => 'https://lh3.googleusercontent.com/a-/ALV-UjX_b2BnYjt4JhddLZw1zUvxOGk4orJmS7IMNpLmeHszjw32kpw=w36-h36-p-rp-mo-br100',
                'rating' => 5,
                'text' => "I recently hired Golden Streets Moving Company for my office move, and I was extremely impressed! From start to finish, the team was professional, efficient, and incredibly careful with all my office equipment.",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2025-07-01'
            ],
            [
                'name' => 'Alejandro Tarquino',
                'avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocLAd-Rp4pF8qr6Z9wdwrgFF4wtbzBNqcHHCNXO3EVc64ytPkA=w36-h36-p-rp-mo-br100',
                'rating' => 5,
                'text' => "I had an incredible experience with their moving service! They came to my home and did excellent work, handling everything with great attention to detail, professionalism, and care.",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2025-06-10'
            ],
            [
                'name' => 'Nive Gupta',
                'avatar' => 'https://lh3.googleusercontent.com/a-/ALV-UjWXjUAya0GOSvPiqnO8s4S_WcRblIc7lHKTa3-h5EHBjoo2Ct7C=w36-h36-p-rp-mo-ba12-br100',
                'rating' => 5,
                'text' => "I highly recommend Aaby and his team! They took great care moving our belongings. They were friendly, fast, efficient, and communication was excellent, especially since we had several stops. Thank you so much! I would definitely contact them again for any future move.",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2025-05-20'
            ],
            [
                'name' => 'Aaby Moreno',
                'avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocKCCymzc5bvmQC9-MOf8YGU3_45WsjDiKL4SqDyaatsJBDVmg=w36-h36-p-rp-mo-br100',
                'rating' => 5,
                'text' => "The best moving company in the Gwinnett area!",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2025-04-15'
            ],
            [
                'name' => 'Nagarjuna Talla',
                'avatar' => 'https://lh3.googleusercontent.com/a-/ALV-UjVjJXg9Lmyd6gI0ocV0SQEv2rCXLP661G3R1TUIuScblocb8PZv=w36-h36-p-rp-mo-br100',
                'rating' => 5,
                'text' => "Very kind, hardworking, very professional, and very patient. I will definitely call them again.",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2025-03-10'
            ],
            [
                'name' => 'Daniel Botello',
                'avatar' => 'https://lh3.googleusercontent.com/a/ACg8ocILdTHxsh0OwUqpSw_k5jZlASxRIzFZpAfsVIl5Pq9jhGKwxg=w36-h36-p-rp-mo-br100',
                'rating' => 5,
                'text' => "The best movers in the area!",
                'source' => 'Google',
                'url' => 'https://maps.app.goo.gl/EPmYRj3aPqK8v6dm8',
                'date' => '2025-02-05'
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