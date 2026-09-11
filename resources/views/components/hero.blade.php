@php
  $image = asset('/images/hero-1.jpeg');

  $icons = [
    'stars' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 .587l3.668 7.431 8.207 1.193-5.938 5.79 1.402 8.166L12 18.896l-7.339 3.271 1.402-8.166L.125 9.211l8.207-1.193z"/></svg>',
    'clock' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>',
    'shield' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/></svg>',
  ];
@endphp

<div class="hero">
  <picture class="hero__picture">
    <img src="{{ $image }}" alt="golden hero moving" title="golden hero moving" loading="eager" fetchpriority="high" class="hero__image">
  </picture>
  <div class="hero__overlay"></div>
  <div class="hero__content container">
      @if (isset($title) && !empty($title))
        <h1 class="hero__title" data-aos="fade-up" data-aos-duration="1500">
          {{$title}}
        </h1>
      @endif
      @if (isset($description) && !empty($description))
        <p class="hero__description lead" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1500">
          {{ $description }}
        </p>  
      @endif
      @if (isset($button['url']) && !empty($button['url']))
        <div class="" data-aos="fade-up" data-aos-delay="500" data-aos-duration="1500">
            <a href="{{ $button['url'] }}" title="{{ $button['title'] ?? 'Ver más' }}" class="hero__button button__primary" >
              {{ $button['title'] ?? 'Ver más' }}
            </a>
        </div>
      @endif
      @if (!empty($stats ?? null))
        <ul class="hero__stats" data-aos="fade-up" data-aos-delay="700" data-aos-duration="1500">
          @foreach ($stats as $stat)
            <li class="hero__stat">
              @if (($stat['icon'] ?? null) === 'stars')
                <span class="hero__stat-stars" aria-hidden="true">
                  @for ($i = 0; $i < ($stat['count'] ?? 5); $i++)
                    {!! $icons['stars'] !!}
                  @endfor
                </span>
              @elseif (isset($icons[$stat['icon'] ?? '']))
                <span class="hero__stat-icon" aria-hidden="true">{!! $icons[$stat['icon']] !!}</span>
              @endif
              <div class="hero__stat-text">
                <span class="hero__stat-title">{{ $stat['title'] }}</span>
                @if (!empty($stat['subtitle'] ?? null))
                  <span class="hero__stat-subtitle">{{ $stat['subtitle'] }}</span>
                @endif
              </div>
            </li>
          @endforeach
        </ul>
      @endif
  </div>
</div>