import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


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
})
