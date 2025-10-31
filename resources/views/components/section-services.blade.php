<section class="section__services">
  <div class="container section__services--container">
    @if (isset($title) && !empty($title))
      <h2 class="section__services--title"  data-aos="fade-up" data-aos-duration="1500">
        {{ $title }}
      </h2>
    @endif
    <div class="section__services--content">
      @foreach ($cards as $item)
        <article class="card__service"  data-aos="fade-up" data-aos-duration="1500">
          <picture class="card__service--picture">
            <img src="{{ $item['image'] }}" alt="image {{ $item['title'] }}" title="image {{ $item['title'] }}" class="card__service--image">
          </picture>
          <div class="card__service--content">
            @if (isset($item['title']) && !empty($item['title']))
              <h3 class="card__service--title">
                {{ $item['title'] }}
              </h3>
            @endif
            @if (isset($item['description']) && !empty($item['description']))
                <p class="card__service--description">
                  {{ $item['description'] }}
                </p>
            @endif
            @if (isset($item['button']['url']) && !empty($item['button']['url']))
                <a href="{{ $item['button']['url'] }}" title="{{ $item['button']['title'] }}" class="card__service--button button__primary">
                  {{ $item['button']['title'] }}
                </a>
            @endif
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>