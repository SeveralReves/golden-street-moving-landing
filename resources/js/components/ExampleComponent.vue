<script setup>
import { ref } from 'vue'
const props = defineProps({
  postId: { type: Number, required: false },
  initial: { type: Boolean, default: false },
})
const isFav = ref(props.initial)

const toggle = async () => {
  // Aquí harías tu llamada a la API de Laravel si quieres persistir:
  // await axios.post(`/posts/${props.postId}/favorite`)
  isFav.value = !isFav.value
}
</script>

<template>
  <button @click="toggle" class="btn">
    {{ isFav ? '★ Favorito' : '☆ Marcar favorito' }}
  </button>
</template>

<style scoped>
.btn { padding: .5rem 1rem; border-radius: .5rem; }
</style>
