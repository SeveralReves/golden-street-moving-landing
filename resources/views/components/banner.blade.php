<section class="banner">
    <div class="banner__container">
        <div class="banner__text">
            @if (!empty($title ?? null))
                <h2 class="banner__title">{{ $title }}</h2>
            @endif
            @if (!empty($description ?? null))
                <p class="banner__subtitle">{{ $description }}</p>
            @endif
        </div>

        <div class="banner__image">
            <img src="{{ !empty($image ?? null) ? $image : asset('images/truck.jpeg') }}" alt="{{ $title ?? 'Camión de mudanza' }}">
        </div>
    </div>
</section>
