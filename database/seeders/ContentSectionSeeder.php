<?php

namespace Database\Seeders;

use App\Models\ContentSection;
use Illuminate\Database\Seeder;

class ContentSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'key' => ContentSection::HERO,
                'title' => 'Your move is easy, safe, and on time.',
                'description' => 'We are a professional moving service that ensures a stress-free experience from start to finish. Our team of experts handles everything with care and precision.',
                'image' => asset('images/hero-1.jpeg'),
                'items' => null,
            ],
            [
                'key' => ContentSection::SERVICES,
                'title' => 'Our Moving Services',
                'description' => null,
                'image' => null,
                'items' => [
                    [
                        'title' => 'Residential Moving',
                        'description' => 'Seamless home transitions, handled with care and precision by our expert team.',
                        'image' => asset('images/cards/services-1.jpeg'),
                    ],
                    [
                        'title' => 'Corporate Moving',
                        'description' => 'Efficiency and minimal business disruption for your office relocation.',
                        'image' => asset('images/cards/services-2.png'),
                    ],
                    [
                        'title' => 'Packing Services',
                        'description' => 'Professional packing and unpacking to save you time and protect your belongings.',
                        'image' => asset('images/cards/services-3.png'),
                    ],
                ],
            ],
            [
                'key' => ContentSection::FAQ,
                'title' => 'Frequently Asked Questions',
                'description' => "Have questions? We've got answers.",
                'image' => null,
                'items' => [
                    [
                        'question' => 'How is the price for my move determined?',
                        'answer' => 'Our pricing is based on an hourly rate which includes the truck, equipment and moving crew. We give you a detailed quote upfront with no hidden fees.',
                    ],
                    [
                        'question' => 'How far in advance should I schedule my move?',
                        'answer' => 'We recommend 2–4 weeks in advance to secure your preferred date. For peak season, book earlier.',
                    ],
                    [
                        'question' => 'What is included in your standard moving service?',
                        'answer' => 'Truck, crew, loading/unloading, basic protection blankets, and standard furniture assembly/disassembly.',
                    ],
                    [
                        'question' => 'What kind of insurance coverage do you offer?',
                        'answer' => 'Basic valuation is included. Full-value protection is available upon request.',
                    ],
                    [
                        'question' => 'What is your policy on rescheduling or cancellation?',
                        'answer' => 'You can reschedule up to 48 hours before the job without fees. See full policy in your quote.',
                    ],
                ],
            ],
            [
                'key' => ContentSection::TRANSFERS,
                'title' => 'Out-of-state transfers',
                'description' => 'Enjoy complete coverage, real-time tracking, and the peace of mind that your belongings are in good hands.',
                'image' => asset('images/truck.jpeg'),
                'items' => null,
            ],
        ];

        foreach ($sections as $section) {
            ContentSection::updateOrCreate(['key' => $section['key']], $section);
        }
    }
}
