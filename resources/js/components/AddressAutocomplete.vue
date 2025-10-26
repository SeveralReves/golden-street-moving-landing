<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { loadPlaces, loadMaps } from '../lib/google' // tu wrapper que hace importLibrary una sola vez

const props = defineProps<{
  modelValue?: any
  placeholder?: string
  countries?: string[]           // ['ve','nl','us']
  // includedPrimaryTypes?: string[] // opcional, p.ej. ['establishment']
  biasCenter?: { lat: number; lng: number } | null
}>()

const emit = defineEmits(['update:modelValue', 'place-changed', 'blur'])

const containerRef = ref<HTMLDivElement|null>(null)
let el: any | null = null
let circle: any | null = null
let onSelect: any | null = null

const componentMap: Record<string, string> = {
  street_number: 'number',
  route: 'street',
  locality: 'city',
  administrative_area_level_1: 'state',
  administrative_area_level_2: 'county',
  country: 'country',
  postal_code: 'postal'
}

function normalizePlace(place: any) {
  const out: any = {
    formatted: place?.formattedAddress || place?.formatted_address || '',
    place_id: place?.id || place?.place_id || null,
    street: '', number: '', city: '', state: '', county: '',
    country: '', postal: '',
    lat: place?.location?.lat ?? null,
    lng: place?.location?.lng ?? null,
    raw: place?.toJSON ? place.toJSON() : place
  }

  // addressComponents (API nueva) o address_components (por compatibilidad)
  const comps = place?.addressComponents ?? place?.address_components ?? []
  for (const c of comps) {
    const t = c.types?.[0]
    const key = componentMap[t]
    if (key) out[key] = c.longText ?? c.long_name
  }
  return out
}

async function init() {
  const [{ PlaceAutocompleteElement }, { Circle }] = await Promise.all([
    loadPlaces(),
    loadMaps() // para bounds del sesgo
  ])

  el = new PlaceAutocompleteElement()

  // placeholder
  el.placeholder = props.placeholder ?? 'Type an address'

  if (props.countries?.length) {
    el.includedRegionCodes = props.countries
  }

  // ✅ (Opcional) limitar por tipo primario (negocios, etc.)
  // if (props.includedPrimaryTypes?.length) {
  //   el.includedPrimaryTypes = props.includedPrimaryTypes
  // }

  // Sesgo de ubicación opcional
  if (props.biasCenter) {
    circle = new Circle({ center: props.biasCenter, radius: 20000 })
    el.locationBias = circle.getBounds()
  }

  // Valor inicial (texto)
  el.value = props.modelValue?.formatted || ''

  // Evento correcto en el widget nuevo
  onSelect = async (ev: any) => {
    const { placePrediction } = ev
    // Convierte la predicción en Place y pide los campos que necesitas
    const place = placePrediction.toPlace()
    await place.fetchFields({
      fields: [
        'formattedAddress',
        'addressComponents',
        'location',
        'id',
        'types',
        'displayName'
      ]
    })
    const normalized = normalizePlace(place)
    emit('update:modelValue', normalized)
    emit('place-changed', normalized)
  }
  el.addEventListener('gmp-select', onSelect)

  containerRef.value?.appendChild(el)
}

onMounted(() => {
  init().catch(e => console.error('Places init error:', e))
})

onBeforeUnmount(() => {
  if (el && onSelect) el.removeEventListener('gmp-select', onSelect)
  if (el && el.parentNode) el.parentNode.removeChild(el)
  el = null
  circle = null
})

watch(() => props.modelValue, (v) => {
  if (el) el.value = v?.formatted || ''
})
</script>

<template>
  <div class="address-autocomplete">
    <div ref="containerRef" class="input-text"></div>
  </div>
</template>

<style scoped>
.input-text { width: 100%; min-height: 42px; display: block; color: red;}
</style>
