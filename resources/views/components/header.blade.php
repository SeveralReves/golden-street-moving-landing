@php
    $logo = Vite::asset('resources/images/logo-1.png');

    $menu = [
      [
        'title' => 'Home',
        'url' => '#'
      ],
      [
        'title' => 'Services',
        'url' => '#'
      ],
      [
        'title' => 'Areas',
        'url' => '#'
      ],
      [
        'title' => 'Testimonials',
        'url' => '#'
      ],
      [
        'title' => 'Books',
        'url' => '#'
      ],
      [
        'title' => 'Contact',
        'url' => '#'
      ],
    ];

    $button = [
      'title' => 'Book your move',
      'url' => '#'
    ]
@endphp

<header class="header">
  <div class="container header__container">
    <a href="/" class="header__logo-link">
      <img src="{{ $logo }}" alt="logo header" title="logo header" loading="lazy" class="header__logo" width="160" height="40">
    </a>
    <nav class="header__nav">
      <ul class="header__ul">
        @foreach ($menu as $item)
          <li class="header__li">
            <a href="{{ $item['url'] }}" title="{{ $item['title'] }}" class="header__link">{{ $item['title'] }}</a>
          </li>
        @endforeach
      </ul>
    </nav>
    <a href="{{ $button['url'] }}" title="{{ $button['title'] }}" class="button__primary">
      {{ $button['title'] }}
    </a>
  </div>
</header>