@php
    $wp_action = isset($wp_action) ? $wp_action : 'success';
@endphp

<section class="section__booking">
  <div class="container">
    <div class="section__booking--header">
      @if (isset($title) && !empty($title))
        <h2 class="section__booking--title">
          {{ $title }}
        </h2>
      @endif
      @if (isset($description) && !empty($description))
        <p class="section__booking--description lead">
          {{ $description }}
        </p>
      @endif
    </div>
  </div>
  <div
      data-vue="Booking"
      data-props='@json(["wp_action" => $wp_action])'>
  </div>
</section>