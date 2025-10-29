@php
  $items = $reviews ?? [];
  $count = count($items);
  $avg = $count ? round(array_sum(array_map(fn($r) => floatval($r['rating'] ?? 0), $items)) / $count, 1) : null;
@endphp

<section class="section__reviews" aria-labelledby="reviews-title">
  <div class="container section__reviews--container">
    @if (!empty($title ?? null))
      <h2 id="reviews-title" class="section__reviews--title">{{ $title }}</h2>
    @endif

    @if (!empty($description ?? null))
      <p class="section__reviews--description">{{ $description }}</p>
    @endif

    {{-- Slider --}}
    @if ($count)
      <div class="section__reviews--slider js-reviews-slider">
        @foreach ($items as $item)
          @php
            $rating = floatval($item['rating'] ?? 0);
            $full = floor($rating);
            $half = ($rating - $full) >= 0.5 ? 1 : 0;
            $empty = 5 - $full - $half;
          @endphp
          <article class="review__card" itemscope itemtype="https://schema.org/Review">
            <meta itemprop="datePublished" content="{{ $item['date'] ?? '' }}">
            <div class="review__header">
              <div class="review__avatar">
                <img loading="lazy" src="{{ $item['avatar'] ?? Vite::asset('resources/images/avatars/placeholder.jpg') }}"
                     alt="{{ $item['name'] ?? 'Reviewer' }}" />
              </div>
              <div class="review__person">
                <h3 class="review__name" itemprop="author" itemscope itemtype="https://schema.org/Person">
                  <span itemprop="name">{{ $item['name'] ?? 'Customer' }}</span>
                </h3>
                <div class="review__stars" aria-label="Rating: {{ $rating }} out of 5">
                  @for ($i=0;$i<$full;$i++)
                    <span class="star star--full" aria-hidden="true">
                      <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                        <path d="M12 .587l3.668 7.431 8.207 1.193-5.938 5.79 1.402 8.166L12 18.896l-7.339 3.271 1.402-8.166L.125 9.211l8.207-1.193z"/>
                      </svg>
                    </span>
                  @endfor
                  @if ($half)
                    <span class="star star--half" aria-hidden="true">
                      <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                        <defs>
                          <linearGradient id="halfGradient">
                            <stop offset="50%" stop-color="currentColor"/>
                            <stop offset="50%" stop-color="transparent"/>
                          </linearGradient>
                        </defs>
                        <path fill="url(#halfGradient)" stroke="currentColor" stroke-width="0.5"
                          d="M12 .587l3.668 7.431 8.207 1.193-5.938 5.79
                            1.402 8.166L12 18.896l-7.339 3.271
                            1.402-8.166L.125 9.211l8.207-1.193z"/>
                      </svg>
                    </span>
                  @endif
                  @for ($i=0;$i<$empty;$i++)
                    <span class="star star--empty" aria-hidden="true">
                      <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                          stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path d="M12 .587l3.668 7.431 8.207 1.193
                                -5.938 5.79 1.402 8.166L12 18.896
                                l-7.339 3.271 1.402-8.166L.125 9.211
                                l8.207-1.193z"/>
                      </svg>
                    </span>
                  @endfor
                  <meta itemprop="reviewRating" content="{{ $rating }}">
                </div>
                <span class="review__source">
                  {{ $item['source'] ?? 'Google' }}
                </span>
              </div>
            </div>

            <blockquote class="review__text" itemprop="reviewBody">
              “{{ $item['text'] ?? '' }}”
            </blockquote>

            @if (!empty($item['url'] ?? null))
              <a class="review__link" href="{{ $item['url'] }}" target="_blank" rel="noopener" title="See on Google">
                View on Google
              </a>
            @endif
          </article>
        @endforeach
      </div>

      {{-- Arrows & Dots --}}
      <div class="reviews__nav">
        <button class="reviews__arrow reviews__arrow--prev" type="button" aria-label="Previous">
          <span class="reviews__arrow reviews__arrow--prev" aria-label="Previous">
            <svg viewBox="0 0 24 24" width="22" height="22"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M15 18l-6-6 6-6" />
            </svg>
          </span>
        </button>
        <div class="reviews__dots js-reviews-dots" aria-hidden="true"></div>
        <button class="reviews__arrow reviews__arrow--next" type="button" aria-label="Next">
          <span class="reviews__arrow reviews__arrow--next" aria-label="Next">
            <svg viewBox="0 0 24 24" width="22" height="22"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M9 6l6 6-6 6" />
            </svg>
          </span>
        </button>
      </div>

      {{-- Agregado: JSON-LD para aggregate rating (SEO) --}}
      @if ($avg)
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "Golden Street Moving Company",
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $avg }}",
            "reviewCount": "{{ $count }}"
          }
        }
        </script>
      @endif
    @endif

    @if (!empty($cta['url'] ?? null))
      <div class="section__reviews--cta">
        <a class="button__primary" href="{{ $cta['url'] }}" title="{{ $cta['text'] ?? 'Book Now' }}">
          {{ $cta['text'] ?? 'Book Now' }}
        </a>
      </div>
    @endif
  </div>
</section>

{{-- SVG helpers inline (puedes moverlos a un partial si prefieres) --}}
@once
  @push('svg')
    @php
      function svg($name) {
        switch ($name) {
          case 'full':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 .587l3.668 7.431 8.207 1.193-5.938 5.79 1.402 8.166L12 18.896l-7.339 3.271 1.402-8.166L.125 9.211l8.207-1.193z"/></svg>';
          case 'half':
            return '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><defs><linearGradient id="half"><stop offset="50%" /><stop offset="50%" stop-color="transparent"/></linearGradient></defs><path d="M12 .587l3.668 7.431 8.207 1.193-5.938 5.79 1.402 8.166L12 18.896l-7.339 3.271 1.402-8.166L.125 9.211l8.207-1.193z" fill="currentColor"/><path d="M12 .587l3.668 7.431 8.207 1.193-5.938 5.79 1.402 8.166L12 18.896l-7.339 3.271 1.402-8.166L.125 9.211l8.207-1.193z" fill="white" transform="translate(12,0)" /></svg>';
          case 'empty':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 .587l3.668 7.431 8.207 1.193-5.938 5.79 1.402 8.166L12 18.896l-7.339 3.271 1.402-8.166L.125 9.211l8.207-1.193z"/></svg>';
          case 'chev':
            return '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>';
        }
      }
    @endphp
  @endpush
@endonce

