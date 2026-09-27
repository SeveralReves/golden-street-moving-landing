<?php

namespace App\Http\Controllers;

use App\Models\ContentSection;

class PageController extends Controller
{
    public function home()
    {
        $sections = ContentSection::whereIn('key', ContentSection::KEYS)->get()->keyBy('key');

        $hero = $this->mergeDefaults($sections->get(ContentSection::HERO), [
            'title' => 'Your move is easy, safe, and on time.',
            'description' => 'We are a professional moving service that ensures a stress-free experience from start to finish. Our team of experts handles everything with care and precision.',
            'image' => asset('images/hero-1.jpeg'),
        ]);

        $services = $this->mergeDefaults($sections->get(ContentSection::SERVICES), [
            'title' => 'Our Moving Services',
            'description' => null,
        ]);
        $services['cards'] = $this->itemsOrDefault($sections->get(ContentSection::SERVICES), [
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
        ]);

        $faq = $this->mergeDefaults($sections->get(ContentSection::FAQ), [
            'title' => 'Frequently Asked Questions',
            'description' => "Have questions? We've got answers.",
        ]);
        $faq['faqs'] = $this->itemsOrDefault($sections->get(ContentSection::FAQ), [
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
        ]);

        $transfers = $this->mergeDefaults($sections->get(ContentSection::TRANSFERS), [
            'title' => 'Out-of-state transfers',
            'description' => 'Enjoy complete coverage, real-time tracking, and the peace of mind that your belongings are in good hands.',
            'image' => asset('images/truck.jpeg'),
        ]);

        return view('welcome', compact('hero', 'services', 'faq', 'transfers'));
    }

    /**
     * Merge stored section fields over defaults, keeping the default whenever
     * the stored value is empty so an admin clearing a field never breaks the page.
     */
    private function mergeDefaults(?ContentSection $section, array $defaults): array
    {
        if (! $section) {
            return $defaults;
        }

        foreach ($defaults as $field => $default) {
            $value = $section->{$field} ?? null;
            if ($value !== null && $value !== '') {
                $defaults[$field] = $value;
            }
        }

        return $defaults;
    }

    private function itemsOrDefault(?ContentSection $section, array $default): array
    {
        $items = $section->items ?? null;

        return ! empty($items) ? $items : $default;
    }
}
