<section class="section__faq" aria-labelledby="faq-title">
  <div class="container section__faq--container" data-faq-single="true">
    @if (!empty($title ?? null))
      <h2 id="faq-title" class="section__faq--title">{{ $title }}</h2>
    @endif

    @if (!empty($description ?? null))
      <p class="section__faq--description">{{ $description }}</p>
    @endif

    @if (!empty($faqs ?? []))
      <div class="section__faq--accordion" role="list">
        @foreach ($faqs as $i => $item)
          @php
            $qid = 'faq-q-' . $i;
            $aid = 'faq-a-' . $i;
          @endphp

          <details class="faq__item" @if($i===0) open @endif role="listitem">
            <summary
              class="faq__question"
              id="{{ $qid }}"
              aria-controls="{{ $aid }}"
              role="button"
              aria-expanded="{{ $i===0 ? 'true' : 'false' }}"
            >
              <span>{{ $item['q'] ?? '' }}</span>
              <svg class="faq__chevron" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" />
              </svg>
            </summary>

            <div class="faq__answer" id="{{ $aid }}" role="region" aria-labelledby="{{ $qid }}">
              <p>{{ $item['a'] ?? '' }}</p>
            </div>
          </details>
        @endforeach
      </div>
    @endif

    @if (!empty($cta['url'] ?? null))
      <div class="section__faq--cta">
        <p class="section__faq--cta-text">Still have questions?</p>
        <a class="button__primary" href="{{ $cta['url'] }}" title="{{ $cta['text'] ?? 'Contact us' }}">
          {{ $cta['text'] ?? 'Contact us' }}
        </a>
      </div>
    @endif
  </div>
</section>
