import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import 'slick-carousel';
import $ from 'jquery';


// importa los CSS desde JS
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';


window.$ = $;
window.jQuery = $;

// vue
import { initGoogle } from './lib/google';

initGoogle(import.meta.env.VITE_GOOGLE_MAPS_API_KEY); //

import { createApp } from 'vue'

// Registro perezoso (lazy) por nombre de componente:
const components = {
  ExampleComponent: () => import('./components/ExampleComponent.vue'),
  Booking: () => import('./components/Booking.vue'),
}

document.addEventListener('DOMContentLoaded', () => {
  // Busca todos los nodos Blade que pidan un componente Vue
  document.querySelectorAll('[data-vue]').forEach(async (el) => {
    const name = el.getAttribute('data-vue')
    const loader = components[name]
    if (!loader) return

    // Props desde data-props (JSON)
    let props = {}
    const raw = el.getAttribute('data-props')
    if (raw) {
      try { props = JSON.parse(raw) } catch (_) {}
    }

    const Comp = (await loader()).default
    const app = createApp(Comp, props)
    app.mount(el)
  })
   document.querySelectorAll('.section__faq--container[data-faq-single="true"]').forEach(function (wrap) {
      wrap.querySelectorAll('details.faq__item').forEach(function (det) {
        det.addEventListener('toggle', function () {
          const open = det.open;
          // Actualiza aria-expanded del summary
          const summary = det.querySelector('.faq__question');
          if (summary) summary.setAttribute('aria-expanded', open ? 'true' : 'false');

          if (open) {
            wrap.querySelectorAll('details.faq__item[open]').forEach(function (other) {
              if (other !== det) other.removeAttribute('open');
              const s = other.querySelector('.faq__question');
              if (s) s.setAttribute('aria-expanded', 'false');
            });
          }
        });
      });
    });

    const $slider = $('.js-reviews-slider');
    if (!$slider.length || typeof $.fn.slick !== 'function') return;

    $slider.slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      infinite: true,
      arrows: true,
      dots: true,
      appendArrows: $('.reviews__nav'),
      appendDots: $('.js-reviews-dots'),
      prevArrow: $('.reviews__arrow--prev'),
      nextArrow: $('.reviews__arrow--next'),
      autoplay: false,
      responsive: [
        { breakpoint: 1280, settings: { slidesToShow: 3 } },
        { breakpoint: 992,  settings: { slidesToShow: 2 } },
        { breakpoint: 576,  settings: { slidesToShow: 1 } },
      ]
    });
    
})
