<script setup>
import { ref, reactive, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const props = defineProps({
  settings: { type: Array, default: () => [] },
})

const GROUP_LABELS = {
  rates: 'Rates',
  base_hours: 'Base Hours by Move Size',
  access: 'Stairs & Elevator',
  services: 'Extra Services',
  travel: 'Travel',
  special_items: 'Special Items',
}

const UNIT_SUFFIX = {
  usd: '$',
  hours: 'hrs',
  miles: 'mi',
  percent: '%',
}

const activeTab = ref('pricing')
const saving = ref(false)

const rows = reactive(
  props.settings.map((s) => ({
    key: s.key,
    label: s.label,
    note: s.note,
    unit: s.unit,
    group: s.group,
    value: Number(s.value),
    confirmed: !!s.confirmed,
  }))
)

const groups = computed(() => {
  const byGroup = {}
  rows.forEach((row) => {
    if (!byGroup[row.group]) byGroup[row.group] = []
    byGroup[row.group].push(row)
  })
  return byGroup
})

const unconfirmedCount = computed(() => rows.filter((r) => !r.confirmed).length)

async function saveAll() {
  saving.value = true
  try {
    const payload = rows.map((r) => ({ key: r.key, value: r.value, confirmed: r.confirmed }))
    await axios.put('/api/pricing-settings', { settings: payload })

    Swal.fire({
      icon: 'success',
      title: 'Saved',
      text: 'Pricing settings updated. New leads and the simulator will use these values right away.',
      timer: 2500,
      showConfirmButton: false,
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.response?.data?.message || 'The pricing settings could not be saved.',
    })
  } finally {
    saving.value = false
  }
}

// --- Simulator ---
const floorOptions = [
  { label: 'Ground floor', value: 'ground' },
  { label: '1', value: '1' },
  { label: '2', value: '2' },
  { label: '3+', value: '3+' },
]

const specialItemOptions = [
  { label: 'Large fridge', value: 'large_fridge' },
  { label: 'Piano', value: 'piano' },
  { label: 'Pool table', value: 'pool_table' },
  { label: 'Jacuzzi / hot tub', value: 'jacuzzi' },
  { label: 'Safe', value: 'safe' },
]

const simForm = reactive({
  move_type: 'house',
  bedrooms: '2',
  origin_floor: 'ground',
  origin_elevator: false,
  destination_floor: 'ground',
  destination_elevator: false,
  packing_service: false,
  special_items: [],
  miles: '',
})

const simulating = ref(false)
const simResult = ref(null)
const simError = ref('')

async function runSimulation() {
  simulating.value = true
  simError.value = ''
  try {
    const { data } = await axios.post('/api/pricing-settings/simulate', {
      ...simForm,
      miles: simForm.miles === '' ? null : Number(simForm.miles),
    })
    simResult.value = data
  } catch (error) {
    console.error(error)
    simError.value = error.response?.data?.message || 'The simulation could not be run.'
  } finally {
    simulating.value = false
  }
}

function unconfirmedLabel(key) {
  const row = rows.find((r) => r.key === key)
  return row ? row.label : key
}
</script>

<template>
  <div class="max-w-5xl mx-auto p-4 sm:p-6 space-y-4">
    <div
      v-if="unconfirmedCount > 0"
      class="rounded-md bg-orange-50 border border-orange-200 text-orange-800 px-4 py-3 text-sm font-medium"
    >
      {{ unconfirmedCount }} value{{ unconfirmedCount === 1 ? '' : 's' }} still need confirming — the estimator isn't reliable until they're filled in.
    </div>
    <div v-else class="rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm font-medium">
      All pricing values are confirmed.
    </div>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
      <div class="border-b border-gray-200 px-6 pt-4">
        <nav class="-mb-px flex flex-wrap gap-4">
          <button
            type="button"
            @click="activeTab = 'pricing'"
            class="py-2 px-1 border-b-2 text-sm font-medium"
            :class="activeTab === 'pricing' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            Pricing
          </button>
          <button
            type="button"
            @click="activeTab = 'simulator'"
            class="py-2 px-1 border-b-2 text-sm font-medium"
            :class="activeTab === 'simulator' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            Simulator
          </button>
        </nav>
      </div>

      <!-- PRICING TAB -->
      <div v-if="activeTab === 'pricing'" class="p-6 space-y-8">
        <div v-for="(groupRows, groupKey) in groups" :key="groupKey">
          <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ GROUP_LABELS[groupKey] || groupKey }}</h3>
          <div class="space-y-3">
            <div
              v-for="row in groupRows"
              :key="row.key"
              class="flex flex-col sm:flex-row sm:items-center gap-3 border border-gray-200 rounded-md p-3"
            >
              <div class="flex-1">
                <div class="text-sm font-medium text-gray-800">{{ row.label }}</div>
                <div v-if="row.note" class="text-xs text-gray-500">{{ row.note }}</div>
              </div>

              <div class="flex items-center gap-2">
                <span v-if="UNIT_SUFFIX[row.unit] === '$'" class="text-gray-500 text-sm">$</span>
                <input
                  type="number"
                  step="0.01"
                  v-model.number="row.value"
                  class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-28 text-sm"
                />
                <span v-if="UNIT_SUFFIX[row.unit] !== '$'" class="text-gray-500 text-sm">{{ UNIT_SUFFIX[row.unit] }}</span>
              </div>

              <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                <input type="checkbox" v-model="row.confirmed" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="row.confirmed ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'"
                >
                  {{ row.confirmed ? 'Confirmed' : 'Needs confirming' }}
                </span>
              </label>
            </div>
          </div>
        </div>

        <div class="pt-2">
          <button
            type="button"
            @click="saveAll"
            :disabled="saving"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ saving ? 'Saving...' : 'Save changes' }}
          </button>
        </div>
      </div>

      <!-- SIMULATOR TAB -->
      <div v-else class="p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type of move</label>
            <select v-model="simForm.move_type" class="border-gray-300 rounded-md shadow-sm block w-full text-sm">
              <option value="house">House</option>
              <option value="apartment">Apartment</option>
              <option value="office">Office</option>
              <option value="labor_only">Labor only, no truck</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms</label>
            <select v-model="simForm.bedrooms" class="border-gray-300 rounded-md shadow-sm block w-full text-sm">
              <option value="studio">Studio</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4+">4+</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Origin floor</label>
            <select v-model="simForm.origin_floor" class="border-gray-300 rounded-md shadow-sm block w-full text-sm">
              <option v-for="opt in floorOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Elevator at origin</label>
            <select v-model="simForm.origin_elevator" class="border-gray-300 rounded-md shadow-sm block w-full text-sm">
              <option :value="true">Yes</option>
              <option :value="false">No</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destination floor</label>
            <select v-model="simForm.destination_floor" class="border-gray-300 rounded-md shadow-sm block w-full text-sm">
              <option v-for="opt in floorOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Elevator at destination</label>
            <select v-model="simForm.destination_elevator" class="border-gray-300 rounded-md shadow-sm block w-full text-sm">
              <option :value="true">Yes</option>
              <option :value="false">No</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Needs packing?</label>
            <select v-model="simForm.packing_service" class="border-gray-300 rounded-md shadow-sm block w-full text-sm">
              <option :value="true">Yes</option>
              <option :value="false">No</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Miles (optional)</label>
            <input type="number" min="0" v-model="simForm.miles" class="border-gray-300 rounded-md shadow-sm block w-full text-sm" />
            <p class="text-xs text-gray-500 mt-1">On the real form this is calculated automatically from the origin/destination ZIP codes; enter it manually here just to test the travel fee.</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Special items</label>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <label v-for="opt in specialItemOptions" :key="opt.value" class="flex items-center gap-2 text-sm">
              <input type="checkbox" :value="opt.value" v-model="simForm.special_items" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
              {{ opt.label }}
            </label>
          </div>
        </div>

        <button
          type="button"
          @click="runSimulation"
          :disabled="simulating"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"
        >
          {{ simulating ? 'Calculating...' : 'Calculate' }}
        </button>

        <p v-if="simError" class="text-sm text-red-600">{{ simError }}</p>

        <div v-if="simResult" class="border border-gray-200 rounded-md p-4 space-y-3">
          <div class="text-lg font-semibold text-gray-900">
            ${{ simResult.range_low }} – ${{ simResult.range_high }}
          </div>
          <div class="text-sm text-gray-600">
            {{ simResult.hours }} hrs @ ${{ simResult.rate }}/hr
          </div>

          <table class="w-full text-sm">
            <tbody>
              <tr v-for="(line, idx) in simResult.breakdown" :key="idx" class="border-t border-gray-100">
                <td class="py-1 text-gray-600">{{ line.label }}</td>
                <td class="py-1 text-right text-gray-900">${{ line.amount }}</td>
              </tr>
              <tr class="border-t border-gray-300 font-semibold">
                <td class="py-1">Total</td>
                <td class="py-1 text-right">${{ simResult.total }}</td>
              </tr>
            </tbody>
          </table>

          <div v-if="simResult.unconfirmed_used.length" class="rounded-md bg-orange-50 border border-orange-200 text-orange-800 px-3 py-2 text-xs">
            This calculation used {{ simResult.unconfirmed_used.length }} unconfirmed value(s):
            {{ simResult.unconfirmed_used.map(unconfirmedLabel).join(', ') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
