<section class="section__benefits">
  <div class="container section__benefits--container">
    @if (isset($title) && !empty($title))
      <h2 class="section__benefits--title"  data-aos="fade-up" data-aos-duration="1500">
        {{ $title }}
      </h2>
    @endif
    <div class="section__benefits--content">
      @foreach ($cards as $item)
        <article class="card__benefit"  data-aos="fade-up" data-aos-duration="1500">
          <i class="card__benefit--icon">
            <img src="{{ $item['icon'] }}" alt="icon {{ $item['title'] }}" title="icon {{ $item['title'] }}" width="40" height="40" class="card__benefit--image">
          </i>
          @if (isset($item['title']) && !empty($item['title']))
            <h3 class="card__benefit--title">
              {{ $item['title'] }}
            </h3>
          @endif
          @if (isset($item['description']) && !empty($item['description']))
              <p class="card__benefit--description">
                {{ $item['description'] }}
              </p>
          @endif
        </article>
      @endforeach
    </div>
  </div>
</section>