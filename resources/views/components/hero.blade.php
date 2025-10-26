@php
  $image = Vite::asset('resources/images/hero-1.jpeg');
    
@endphp

<div class="hero">
  <picture class="hero__picture">
    <img src="{{ $image }}" alt="golden hero moving" title="golden hero moving"  loading="lazy" class="hero__image">
  </picture>
  <div class="hero__overlay"></div>
  <div class="hero__content container">
      @if (isset($title) && !empty($title))
        <h1 class="hero__title">
          {{$title}}
        </h1>
      @endif
      @if (isset($description) && !empty($description))
        <p class="hero__description lead">
          {{ $description }}
        </p>  
      @endif
      @if (isset($button['url']) && !empty($button['url']))
        <a href="{{ $button['url'] }}" title="{{ $button['title'] ?? 'Ver más' }}" class="hero__button button__primary">
          {{ $button['title'] ?? 'Ver más' }}
        </a>
      @endif
  </div>
</div>