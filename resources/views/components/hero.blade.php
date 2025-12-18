@php
  // $image = asset('/images/hero-1.jpeg');
  $image = null;
    
@endphp

<div class="hero">
  <picture class="hero__picture">
    <img src="{{ $image }}" alt="golden hero moving" title="golden hero moving"  loading="lazy" class="hero__image">
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
  </div>
</div>