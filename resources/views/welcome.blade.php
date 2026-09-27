@extends('layouts.default')

@section('content')

    @include('components.hero', [
        'title' => $hero['title'],
        'description' => $hero['description'],
        'image' => $hero['image'],
        'button' => [
            'url' => '#booking',
            'title' => 'Book your date now'
        ],
        'stats' => [
            [
                'icon' => 'stars',
                'count' => 5,
                'title' => '5.0 rating',
                'subtitle' => 'on Google',
            ],
            [
                'icon' => 'clock',
                'title' => 'Same-day movers',
                'subtitle' => 'available',
            ],
            [
                'icon' => 'shield',
                'title' => 'Free wrapping protection',
                'subtitle' => 'in every move',
            ],
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
        'title' => $services['title'],
        'description' => $services['description'],
        'cards' => $services['cards'],
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
        'title' => $faq['title'],
        'description' => $faq['description'],
        'image' => $faq['image'] ?? null,
        'cta' => [
            'text' => 'Contact Us',
            'url'  => '#booking'
        ],
        'faqs' => $faq['faqs'],
    ])

    @include('components.banner', [
        'title' => $transfers['title'],
        'description' => $transfers['description'],
        'image' => $transfers['image'],
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