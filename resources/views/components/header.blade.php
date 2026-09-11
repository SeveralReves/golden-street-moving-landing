@php
    $logo = asset('/images/logo-1.png');

    $menu = [
      [
        'title' => 'Home',
        'url' => '/'
      ],
      [
        'title' => 'Services',
        'url' => '/#services'
      ],
      [
        'title' => 'Testimonials',
        'url' => '/#testimonials'
      ],
      [
        'title' => 'Booking',
        'url' => '/#booking'
      ],
      [
        'title' => 'FAQ',
        'url' => '/#faq'
      ],
      [
        'title' => 'Contact',
        'url' => 'mailto:infogoldenstreets@gmail.com'
      ],
    ];

    $button = [
      'title' => 'Book your move',
      'url' => '/#booking',
    ];

    $phone = [
      'display' => '(770) 589-9512',
      'tel' => '+17705899512',
    ];
@endphp

<header class="header" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
  <div class="container header__container">
    <a href="/" class="header__logo-link">
      <img src="{{ $logo }}" alt="logo header" title="logo header" loading="lazy" class="header__logo" width="160" height="40">
    </a>
    <nav class="header__nav" :class="{ 'header__nav--open': mobileOpen }" id="primary-nav">
      <ul class="header__ul">
        @foreach ($menu as $item)
          <li class="header__li">
            <a href="{{ $item['url'] }}" title="{{ $item['title'] }}" class="header__link" @click="mobileOpen = false">{{ $item['title'] }}</a>
          </li>
        @endforeach
      </ul>
    </nav>
    <div class="header__actions">
      <a href="tel:{{ $phone['tel'] }}" title="Call {{ $phone['display'] }}" class="header__phone">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="header__phone-icon">
          <path d="M4.5 4h3.5l1.5 4.5-2 1.5a12 12 0 0 0 6.5 6.5l1.5-2 4.5 1.5V19a2 2 0 0 1-2 2A16.5 16.5 0 0 1 2.5 6.5 2 2 0 0 1 4.5 4Z" />
        </svg>
        <span class="header__phone-number">{{ $phone['display'] }}</span>
      </a>
      <a href="{{ $button['url'] }}" title="{{ $button['title'] }}" class="button__primary header__cta">
        {{ $button['title'] }}
      </a>
      <button
        type="button"
        class="header__toggle"
        @click="mobileOpen = !mobileOpen"
        :aria-expanded="mobileOpen.toString()"
        aria-controls="primary-nav"
        aria-label="Toggle navigation menu"
      >
        <span class="header__toggle-bar"></span>
        <span class="header__toggle-bar"></span>
        <span class="header__toggle-bar"></span>
      </button>
    </div>
  </div>
</header>