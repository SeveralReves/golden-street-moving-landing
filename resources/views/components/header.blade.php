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
    ]
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