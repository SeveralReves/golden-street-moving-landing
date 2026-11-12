<script setup>
import { computed } from 'vue'

const props = defineProps({
  page: { type: Number, required: true },
  lastPage: { type: Number, required: true },
  total: { type: Number, default: 0 },
  perPage: { type: Number, default: 15 },
  disabled: { type: Boolean, default: false },
})
const emit = defineEmits(['change'])

const from = computed(() => (props.total ? (props.page - 1) * props.perPage + 1 : 0))
const to = computed(() => Math.min(props.page * props.perPage, props.total))

// Current page plus its neighbours, with the first/last page and ellipses.
const pages = computed(() => {
  const set = new Set([1, props.lastPage, props.page - 1, props.page, props.page + 1])
  const sorted = [...set].filter((n) => n >= 1 && n <= props.lastPage).sort((a, b) => a - b)
  const out = []
  sorted.forEach((n, i) => {
    if (i && n - sorted[i - 1] > 1) out.push('…')
    out.push(n)
  })
  return out
})

const go = (n) => {
  if (props.disabled || n < 1 || n > props.lastPage || n === props.page) return
  emit('change', n)
}
</script>

<template>
  <nav v-if="total > 0" class="admin__pager" aria-label="Pagination">
    <span class="admin__pager-info">Showing {{ from }}–{{ to }} of {{ total }}</span>
    <div v-if="lastPage > 1" class="admin__pager-controls">
      <button type="button" :disabled="disabled || page === 1" @click="go(page - 1)">Previous</button>
      <template v-for="(n, i) in pages" :key="i">
        <span v-if="n === '…'" class="admin__pager-gap">…</span>
        <button
          v-else
          type="button"
          :class="{ 'is-current': n === page }"
          :aria-current="n === page ? 'page' : undefined"
          :disabled="disabled"
          @click="go(n)"
        >{{ n }}</button>
      </template>
      <button type="button" :disabled="disabled || page === lastPage" @click="go(page + 1)">Next</button>
    </div>
  </nav>
</template>
