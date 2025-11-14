<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios' // si lo tienes global puedes quitar esta línea
import Swal from 'sweetalert2' 

// Props
const props = defineProps({
  quotes: { type: Array, default: () => [] },
})

const viewModal = ref(false)
const item = ref({});
const newStatus = ref('')

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'In Review', value: 'in_review' },
  { label: 'Quoted', value: 'quoted' },
  { label: 'Closed', value: 'closed' },
  { label: 'Cancelled', value: 'cancelled' },
]

const getDate = (date)=> {
    if (!date) return '-'
    var today  = new Date(date);
    return today.toLocaleDateString("en-US");
};

const viewItem = (quote) => {
    item.value = quote;
    newStatus.value = quote.status;
    viewModal.value = true;
}
const closeItem = () => {
    item.value = {};
    newStatus.value = '';
    viewModal.value = false;
}

const handleKeyup = (e) => {
  if (e.key === 'Escape' && viewModal.value) {
    closeItem()
  }
}

const saveStatus = async () => {
  try {
    const url = `/api/moving-quotes/${item.value.id}`

    const { data } = await axios.put(url, {
      status: newStatus.value,
    })

    item.value.status = newStatus.value

    // swal success
    // const swal = window.Swal
    if (Swal) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'The status was successfully updated.',
        timer: 2000,
        showConfirmButton: false,
      })
    }

    closeItem()
  } catch (error) {
    console.error(error)
    if (Swal) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.response?.data?.message || 'The status could not be updated.',
      })
    }
  }
}

onMounted(() => {
  window.addEventListener('keyup', handleKeyup)
})
onBeforeUnmount(() => {
  window.removeEventListener('keyup', handleKeyup)
})

</script>

<template>
    <div class="">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="booking__table">
                                <div class="booking__table--head-item">
                                    ID
                                </div>
                                <div class="booking__table--head-item">
                                    Name
                                </div>
                                <div class="booking__table--head-item">
                                    Email
                                </div>
                                <div class="booking__table--head-item">
                                    Date
                                </div>
                                <div class="booking__table--head-item">
                                    Origin
                                </div>
                                <div class="booking__table--head-item">
                                    Destination
                                </div>
                                <div class="booking__table--head-item">
                                    Status
                                </div>
                                <div class="booking__table--head-item">
                                    Created At
                                </div>
                            <template v-for="quote in quotes">
                                <div class="booking__table--body-item">
                                    <button class="booking__table--body-button" @click="viewItem(quote)">
                                        {{quote.id}}
                                    </button>
                                </div>
                                <div class="booking__table--body-item">
                                    <button class="booking__table--body-button" @click="viewItem(quote)">
                                        {{quote.name}}
                                    </button>
                                </div>
                                <div class="booking__table--body-item">
                                    {{quote.email}}
                                </div>
                                <div class="booking__table--body-item">
                                    {{getDate(quote.preferred_date)}}
                                </div>
                                <div class="booking__table--body-item">
                                    {{quote.origin_address}}
                                </div>
                                <div class="booking__table--body-item">
                                    {{quote.destination_address}}
                                </div>
                                <div class="booking__table--body-item">
                                    <span class="booking__table--body-chip" :class="`booking__table--body-chip--${quote.status}`">
                                        {{quote.status}}
                                    </span>
                                </div>
                                <div class="booking__table--body-item">
                                    {{getDate(quote.created_at)}}
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div v-if="viewModal" class="booking__modal-overlay" @click.self="closeItem">
        <div class="booking__modal">
            <div class="booking__modal--header">
                <h2 class="booking__modal--title">
                    Booking of {{ item.name || '—' }}
                </h2>
                <button class="booking__modal--close" @click="closeItem">×</button>
                </div>

                <div class="booking__modal--body">
                <div class="booking__modal--row">
                    <span class="booking__modal--label">ID:</span>
                    <span class="booking__modal--value">{{ item.id }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Email:</span>
                    <span class="booking__modal--value">{{ item.email || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Phone:</span>
                    <span class="booking__modal--value">{{ item.phone || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Preferred date:</span>
                    <span class="booking__modal--value">{{ getDate(item.preferred_date) }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Schedule:</span>
                    <span class="booking__modal--value">{{ item.schedule || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Move type:</span>
                    <span class="booking__modal--value">{{ item.move_type || '-' }}</span>
                </div>

                <div class="booking__modal--section-title">Status</div>
                <div class="booking__modal--row">
                    <select v-model="newStatus" class="booking__modal--select">
                    <option
                        v-for="opt in statusOptions"
                        :key="opt.value"
                        :value="opt.value"
                    >
                        {{ opt.label }}
                    </option>
                    </select>
                </div>

                <div class="booking__modal--section-title">Origin</div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Address:</span>
                    <span class="booking__modal--value">{{ item.origin_address || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Floor:</span>
                    <span class="booking__modal--value">{{ item.origin_floor || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Elevator:</span>
                    <span class="booking__modal--value">
                    {{ item.origin_elevator ? 'Yes' : 'No' }}
                    </span>
                </div>

                <div class="booking__modal--section-title">Destination</div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Address:</span>
                    <span class="booking__modal--value">{{ item.destination_address || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Floor:</span>
                    <span class="booking__modal--value">{{ item.destination_floor || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Elevator:</span>
                    <span class="booking__modal--value">
                    {{ item.destination_elevator ? 'Yes' : 'No' }}
                    </span>
                </div>

                <div class="booking__modal--section-title">Other</div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Packing service:</span>
                    <span class="booking__modal--value">
                    {{ item.packing_service ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Comments:</span>
                    <span class="booking__modal--value">{{ item.comments || '-' }}</span>
                </div>
                <div class="booking__modal--row">
                    <span class="booking__modal--label">Created:</span>
                    <span class="booking__modal--value">{{ getDate(item.created_at) }}</span>
                </div>
                </div>

                <div class="booking__modal--footer">
                    <button class="button__primary" @click="saveStatus" :disabled="item.status == newStatus">Save</button>
                    <button class="booking__modal--button" @click="closeItem">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
