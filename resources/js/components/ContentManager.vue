<script setup>
import { ref, reactive, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const props = defineProps({
  sections: { type: Object, default: () => ({}) },
})

const TABS = [
  { key: 'hero', label: 'Hero', hasItems: false, itemFields: null },
  { key: 'services', label: 'Our Moving Services', hasItems: true, itemFields: ['title', 'description', 'image'] },
  { key: 'faq', label: 'Frequently Asked Questions', hasItems: true, itemFields: ['question', 'answer'] },
  { key: 'transfers', label: 'Out-of-state Transfers', hasItems: false, itemFields: null },
]

const activeTab = ref(TABS[0].key)
const currentTab = computed(() => TABS.find((t) => t.key === activeTab.value))
const saving = reactive({})

const forms = reactive({})
const imagePreviews = reactive({})
const imageFiles = reactive({})
const itemImageFiles = reactive({})

const emptyItem = (fields) => Object.fromEntries(fields.map((f) => [f, '']))

TABS.forEach((tab) => {
  const section = props.sections[tab.key] || {}

  forms[tab.key] = {
    title: section.title || '',
    description: section.description || '',
    items: tab.hasItems ? (section.items && section.items.length ? section.items.map((i) => ({ ...i })) : [emptyItem(tab.itemFields)]) : null,
  }

  imagePreviews[tab.key] = section.image || null
  imageFiles[tab.key] = null
  itemImageFiles[tab.key] = {}
})

const onImageChange = (key, event) => {
  const file = event.target.files[0]
  if (!file) return
  imageFiles[key] = file
  imagePreviews[key] = URL.createObjectURL(file)
}

const onItemImageChange = (key, index, event) => {
  const file = event.target.files[0]
  if (!file) return
  itemImageFiles[key][index] = file
  forms[key].items[index].image = URL.createObjectURL(file)
}

const addItem = (tab) => {
  forms[tab.key].items.push(emptyItem(tab.itemFields))
}

const removeItem = (tab, index) => {
  forms[tab.key].items.splice(index, 1)
  delete itemImageFiles[tab.key][index]
}

const save = async (tab) => {
  saving[tab.key] = true
  try {
    const form = forms[tab.key]
    const data = new FormData()
    data.append('_method', 'PUT')
    data.append('title', form.title || '')
    data.append('description', form.description || '')

    if (imageFiles[tab.key]) {
      data.append('image', imageFiles[tab.key])
    }

    if (tab.hasItems) {
      data.append('items', JSON.stringify(form.items))
      Object.entries(itemImageFiles[tab.key]).forEach(([index, file]) => {
        data.append(`item_image_${index}`, file)
      })
    }

    // Let axios set the multipart Content-Type itself so it includes the boundary,
    // otherwise Laravel can't parse the uploaded files.
    const { data: response } = await axios.post(`/api/content-sections/${tab.key}`, data)

    imageFiles[tab.key] = null
    itemImageFiles[tab.key] = {}
    if (response.section?.image) {
      imagePreviews[tab.key] = response.section.image
    }

    Swal.fire({
      icon: 'success',
      title: 'Saved',
      text: 'The section content was updated.',
      timer: 2000,
      showConfirmButton: false,
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.response?.data?.message || 'The content could not be saved.',
    })
  } finally {
    saving[tab.key] = false
  }
}
</script>

<template>
  <div class="max-w-5xl mx-auto p-4 sm:p-6">
    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
      <div class="border-b border-gray-200 px-6 pt-4">
        <nav class="-mb-px flex flex-wrap gap-4">
          <button
            v-for="tab in TABS"
            :key="tab.key"
            type="button"
            @click="activeTab = tab.key"
            class="py-2 px-1 border-b-2 text-sm font-medium"
            :class="activeTab === tab.key ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <div v-if="currentTab" :key="currentTab.key" class="p-6 space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
          <input
            type="text"
            v-model="forms[currentTab.key].title"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea
            v-model="forms[currentTab.key].description"
            rows="3"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
          <img v-if="imagePreviews[currentTab.key]" :src="imagePreviews[currentTab.key]" class="w-48 h-32 object-cover rounded-md border border-gray-200 mb-2" />
          <input type="file" accept="image/*" @change="onImageChange(currentTab.key, $event)" class="block w-full text-sm text-gray-600" />
        </div>

        <div v-if="currentTab.hasItems" class="space-y-4">
          <h3 class="text-sm font-semibold text-gray-700">
            {{ currentTab.key === 'services' ? 'Service cards' : 'Questions' }}
          </h3>

          <div
            v-for="(item, index) in forms[currentTab.key].items"
            :key="index"
            class="border border-gray-200 rounded-md p-4 space-y-3 relative"
          >
            <button
              type="button"
              @click="removeItem(currentTab, index)"
              class="absolute top-2 right-2 text-xs text-red-600 hover:text-red-800"
            >
              Remove
            </button>

            <template v-if="currentTab.key === 'services'">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                <input type="text" v-model="item.title" class="border-gray-300 rounded-md shadow-sm block w-full text-sm" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <textarea v-model="item.description" rows="2" class="border-gray-300 rounded-md shadow-sm block w-full text-sm"></textarea>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Image</label>
                <img v-if="item.image" :src="item.image" class="w-32 h-20 object-cover rounded-md border border-gray-200 mb-2" />
                <input type="file" accept="image/*" @change="onItemImageChange(currentTab.key, index, $event)" class="block w-full text-sm text-gray-600" />
              </div>
            </template>

            <template v-else-if="currentTab.key === 'faq'">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Question</label>
                <input type="text" v-model="item.question" class="border-gray-300 rounded-md shadow-sm block w-full text-sm" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Answer</label>
                <textarea v-model="item.answer" rows="2" class="border-gray-300 rounded-md shadow-sm block w-full text-sm"></textarea>
              </div>
            </template>
          </div>

          <button
            type="button"
            @click="addItem(currentTab)"
            class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
          >
            + Add {{ currentTab.key === 'services' ? 'card' : 'question' }}
          </button>
        </div>

        <div class="pt-2">
          <button
            type="button"
            @click="save(currentTab)"
            :disabled="saving[currentTab.key]"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ saving[currentTab.key] ? 'Saving...' : 'Save changes' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
