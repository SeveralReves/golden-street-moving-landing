<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin, { Draggable } from '@fullcalendar/interaction'

const props = defineProps({
  quotes: { type: Array, default: () => [] },
  config: { type: Object, default: () => ({}) },
})

const STATUS_COLORS = {
  scheduled: '#2563eb',
  in_progress: '#d97706',
  completed: '#16a34a',
  cancelled: '#9ca3af',
}
const STATUS_LABELS = {
  scheduled: 'Scheduled',
  in_progress: 'In progress',
  completed: 'Completed',
  cancelled: 'Cancelled',
}

const calendarRef = ref(null)
const sidebarRef = ref(null)
const pendingQuotes = ref([...props.quotes])
const search = ref('')
const saving = ref(false)
const modalOpen = ref(false)

const filteredQuotes = computed(() => {
  const term = search.value.trim().toLowerCase()
  if (!term) return pendingQuotes.value
  return pendingQuotes.value.filter((q) =>
    [q.name, q.phone, q.origin, q.destination].some((v) => (v || '').toLowerCase().includes(term))
  )
})

const pad = (n) => String(n).padStart(2, '0')
// Naive local "YYYY-MM-DDTHH:mm" — what <input type="datetime-local"> and the API both use.
const toLocal = (d) =>
  `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
const addHours = (d, h) => new Date(d.getTime() + h * 3600 * 1000)

const emptyForm = () => ({
  id: null,
  moving_quote_id: null,
  title: '',
  customer_name: '',
  customer_phone: '',
  origin_address: '',
  destination_address: '',
  start_at: '',
  end_at: '',
  crew_size: 2,
  status: 'scheduled',
  notes: '',
})
const form = reactive(emptyForm())
const linkedQuote = computed(() => props.quotes.find((q) => q.id === form.moving_quote_id))

function openModal(data) {
  Object.assign(form, emptyForm(), data)
  modalOpen.value = true
}

function startFromQuote(quote, date) {
  // Morning leads default to 8:00, afternoon ones to 13:00.
  const base = date ? new Date(date) : quote.preferred_date ? new Date(`${quote.preferred_date}T00:00:00`) : new Date()
  if (base.getHours() === 0 && base.getMinutes() === 0) {
    base.setHours(quote.schedule === 'afternoon' ? 13 : 8, 0, 0, 0)
  }
  return base
}

function scheduleQuote(quote, date = null) {
  const start = startFromQuote(quote, date)
  openModal({
    moving_quote_id: quote.id,
    title: quote.name || quote.phone || 'Move',
    customer_name: quote.name || '',
    customer_phone: quote.phone || '',
    origin_address: quote.origin || '',
    destination_address: quote.destination || '',
    start_at: toLocal(start),
    end_at: toLocal(addHours(start, quote.hours || 4)),
  })
}

function newEvent(start, end) {
  openModal({ start_at: toLocal(start), end_at: toLocal(end || addHours(start, 4)) })
}

function eventToForm(e) {
  const p = e.extendedProps
  openModal({
    id: Number(e.id),
    moving_quote_id: p.moving_quote_id,
    title: e.title,
    customer_name: p.customer_name || '',
    customer_phone: p.customer_phone || '',
    origin_address: p.origin_address || '',
    destination_address: p.destination_address || '',
    start_at: toLocal(e.start),
    end_at: toLocal(e.end || addHours(e.start, 4)),
    crew_size: p.crew_size,
    status: p.status,
    notes: p.notes || '',
  })
}

const api = () => calendarRef.value.getApi()
const refresh = () => api().refetchEvents()

// Sends the request; on a capacity conflict asks whether to double-book anyway.
async function send(method, url, payload) {
  try {
    return await axios({ method, url, data: payload })
  } catch (error) {
    if (error.response?.status === 409) {
      const names = (error.response.data.conflicts || []).map((c) => `• ${c.title}`).join('<br>')
      const { isConfirmed } = await Swal.fire({
        icon: 'warning',
        title: 'Schedule conflict',
        html: `${error.response.data.message}<br><br>${names}`,
        showCancelButton: true,
        confirmButtonText: 'Schedule anyway',
      })
      if (isConfirmed) return send(method, url, { ...payload, force: true })
      return null
    }
    throw error
  }
}

function reportError(error) {
  const errors = error.response?.data?.errors
  Swal.fire({
    icon: 'error',
    title: 'Error',
    text: errors ? Object.values(errors).flat().join(' ') : error.response?.data?.message || 'Something went wrong.',
  })
}

function syncPending(quoteId, scheduled) {
  if (!quoteId) return
  if (scheduled) pendingQuotes.value = pendingQuotes.value.filter((q) => q.id !== quoteId)
}

async function save() {
  saving.value = true
  try {
    const payload = { ...form }
    delete payload.id
    const res = form.id
      ? await send('put', `/api/move-events/${form.id}`, payload)
      : await send('post', '/api/move-events', payload)
    if (!res) return
    if (['scheduled', 'in_progress'].includes(res.data.event.status)) syncPending(form.moving_quote_id, true)
    modalOpen.value = false
    refresh()
  } catch (error) {
    reportError(error)
  } finally {
    saving.value = false
  }
}

async function remove() {
  const { isConfirmed } = await Swal.fire({
    icon: 'warning',
    title: 'Delete this event?',
    showCancelButton: true,
    confirmButtonText: 'Delete',
  })
  if (!isConfirmed) return
  try {
    await axios.delete(`/api/move-events/${form.id}`)
    modalOpen.value = false
    // The lead may be unscheduled again; reload so the sidebar is accurate.
    window.location.reload()
  } catch (error) {
    reportError(error)
  }
}

async function moveOrResize(info) {
  const payload = { start_at: toLocal(info.event.start), end_at: toLocal(info.event.end || addHours(info.event.start, 4)) }
  try {
    const res = await send('put', `/api/move-events/${info.event.id}`, payload)
    if (!res) info.revert()
  } catch (error) {
    info.revert()
    reportError(error)
  }
}

const calendarOptions = {
  plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
  },
  height: 'auto',
  nowIndicator: true,
  selectable: true,
  editable: true,
  droppable: true,
  dayMaxEvents: 3,
  slotMinTime: props.config.dayStart || '06:00:00',
  slotMaxTime: props.config.dayEnd || '21:00:00',
  eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
  events: async (info, success, failure) => {
    try {
      const { data } = await axios.get('/api/move-events', {
        params: { start: info.startStr, end: info.endStr },
      })
      success(
        data.map((e) => ({
          id: e.id,
          title: e.title,
          start: e.start,
          end: e.end,
          color: STATUS_COLORS[e.status],
          classNames: e.status === 'cancelled' ? ['line-through', 'opacity-60'] : [],
          extendedProps: e,
        }))
      )
    } catch (error) {
      failure(error)
    }
  },
  select: (info) => {
    const view = info.view.type
    const start = info.start
    // A bare day click in month view gets a sensible 8:00 start.
    if (view === 'dayGridMonth') start.setHours(8, 0, 0, 0)
    newEvent(start, view === 'dayGridMonth' ? addHours(start, 4) : info.end)
    api().unselect()
  },
  eventClick: (info) => eventToForm(info.event),
  eventDrop: moveOrResize,
  eventResize: moveOrResize,
  drop: (info) => {
    const quote = pendingQuotes.value.find((q) => q.id === Number(info.draggedEl.dataset.quoteId))
    if (quote) scheduleQuote(quote, info.date)
  },
}

onMounted(async () => {
  await nextTick()
  new Draggable(sidebarRef.value, {
    itemSelector: '[data-quote-id]',
    eventData: () => ({ create: false }),
  })
})

const formatDate = (d) => (d ? new Date(`${d}T00:00:00`).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) : 'No date')
</script>

<template>
  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-[320px_1fr]">
      <!-- Leads waiting for a slot -->
      <aside class="bg-white shadow-sm sm:rounded-lg p-4 h-fit">
        <div class="flex items-center justify-between mb-3">
          <h3 class="!text-base font-semibold text-gray-800">Quotes to schedule</h3>
          <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">{{ pendingQuotes.length }}</span>
        </div>
        <input v-model="search" type="search" placeholder="Search name, phone, address…"
          class="w-full mb-3 rounded-md border-gray-300 text-sm" />
        <p class="text-xs text-gray-500 mb-3">Drag a quote onto the calendar or click “Schedule”.</p>

        <div ref="sidebarRef" class="space-y-2 max-h-[560px] overflow-y-auto">
          <div v-for="q in filteredQuotes" :key="q.id" :data-quote-id="q.id"
            class="border border-gray-200 rounded-md p-3 text-sm cursor-grab bg-gray-50 hover:border-blue-400">
            <div class="flex justify-between gap-2">
              <span class="font-medium text-gray-800">{{ q.name || q.phone || `Quote #${q.id}` }}</span>
              <span class="text-xs text-gray-500">{{ formatDate(q.preferred_date) }}</span>
            </div>
            <div class="text-xs text-gray-500 mt-1 truncate">{{ q.origin || '—' }} → {{ q.destination || '—' }}</div>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-gray-500 capitalize">{{ q.schedule }} · ~{{ q.hours }}h</span>
              <button type="button" class="text-xs font-semibold text-blue-600 hover:underline" @click="scheduleQuote(q)">
                Schedule
              </button>
            </div>
          </div>
          <p v-if="!filteredQuotes.length" class="text-sm text-gray-400 text-center py-6">Nothing to schedule.</p>
        </div>
      </aside>

      <!-- Calendar -->
      <section class="bg-white shadow-sm sm:rounded-lg p-4">
        <div class="flex flex-wrap items-center gap-4 mb-3 text-xs text-gray-600">
          <span v-for="(label, key) in STATUS_LABELS" :key="key" class="inline-flex items-center gap-1">
            <i class="inline-block w-3 h-3 rounded-sm" :style="{ background: STATUS_COLORS[key] }"></i>{{ label }}
          </span>
          <span class="ml-auto">Max {{ config.maxConcurrent }} simultaneous moves</span>
          <button type="button" class="bg-blue-600 text-white rounded-md px-3 py-1.5 font-semibold"
            @click="newEvent(new Date(new Date().setHours(8, 0, 0, 0)))">
            + New event
          </button>
        </div>
        <FullCalendar ref="calendarRef" :options="calendarOptions" />
      </section>
    </div>

    <!-- Create / edit modal -->
    <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="modalOpen = false">
      <form class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 space-y-3" @submit.prevent="save">
        <h3 class="!text-lg font-semibold text-gray-800">{{ form.id ? 'Edit move' : 'Schedule a move' }}</h3>

        <div>
          <label class="block text-sm font-medium text-gray-700">Linked quote</label>
          <select v-model="form.moving_quote_id" class="w-full rounded-md border-gray-300 text-sm">
            <option :value="null">New customer (no quote)</option>
            <option v-for="q in quotes" :key="q.id" :value="q.id">
              #{{ q.id }} · {{ q.name || q.phone }}
            </option>
          </select>
          <p v-if="linkedQuote" class="text-xs text-gray-500 mt-1">The quote will move to “schedule” automatically.</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input v-model="form.title" type="text" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Customer</label>
            <input v-model="form.customer_name" type="text" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input v-model="form.customer_phone" type="text" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700">Origin</label>
            <input v-model="form.origin_address" type="text" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700">Destination</label>
            <input v-model="form.destination_address" type="text" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Start</label>
            <input v-model="form.start_at" type="datetime-local" required class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">End</label>
            <input v-model="form.end_at" type="datetime-local" required class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Crew size</label>
            <input v-model.number="form.crew_size" type="number" min="1" max="20" class="w-full rounded-md border-gray-300 text-sm" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select v-model="form.status" class="w-full rounded-md border-gray-300 text-sm">
              <option v-for="(label, key) in STATUS_LABELS" :key="key" :value="key">{{ label }}</option>
            </select>
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea v-model="form.notes" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button v-if="form.id" type="button" class="text-sm text-red-600 hover:underline" @click="remove">Delete</button>
          <span v-else></span>
          <div class="flex gap-2">
            <button type="button" class="px-4 py-2 text-sm rounded-md border border-gray-300" @click="modalOpen = false">Cancel</button>
            <button type="submit" :disabled="saving" class="px-4 py-2 text-sm rounded-md bg-blue-600 text-white font-semibold disabled:opacity-50">
              {{ saving ? 'Saving…' : 'Save' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
