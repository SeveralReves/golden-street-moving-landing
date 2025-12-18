<script setup>
import { ref, computed } from 'vue'
import { Field, Form, ErrorMessage } from 'vee-validate'
import * as Yup from 'yup'
import DatePicker from 'vue-datepicker-next'
import 'vue-datepicker-next/index.css'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import AddressAutocomplete from './AddressAutocomplete.vue'
import axios from 'axios'
import Swal from 'sweetalert2' 


const GOOGLE_API_KEY = import.meta.env.VITE_GOOGLE_MAPS_API_KEY

// Props
const props = defineProps({
  wp_action: { type: String, default: 'success' },
  disabledDates: { type: Array, default: () => [] }
})

// Step system
const step = ref(1)
const formRef = ref()

const wbar = {
  1: '33%',
  2: '66%',
  3: '100%'
}

// Options
const scheduleOptions = [
  { label: 'Morning (8am - 12pm)', value: 'morning' },
  { label: 'Afternoon (1pm - 5pm)', value: 'afternoon' }
]

// Date limits
const todayStart = computed(() => {
  const d = new Date()
  d.setHours(0, 0, 0, 0)
  return d
})

// Validation schema
const schema = Yup.object().shape({
  name: Yup.string().required().label('Name'),
  email: Yup.string().email().required().label('Email'),
  org_address: Yup.object({
    formatted: Yup.string().required(),
    lat: Yup.string().required(),
    lng: Yup.string().required()
  }).required(),
  end_address: Yup.object({
    formatted: Yup.string().required(),
    lat: Yup.string().required(),
    lng: Yup.string().required()
  }).required(),
  date: Yup.date()
    .required()
    .min(todayStart.value, 'Date cannot be in the past')
    .label('Preferred Date'),
  schedule: Yup.string().required('Please choose a schedule'),

  // Step 2
  move_type: Yup.string().required('Select move type'),
  origin_floor: Yup.string().required('Select origin floor'),
  origin_elevator: Yup.boolean().required().label('Elevator at origin'),
  destination_floor: Yup.string().required('Select destination floor'),
  destination_elevator: Yup.boolean().required().label('Elevator at destination'),
  packing_service: Yup.boolean().required().label('Packing service'),
  comments: Yup.string().max(500).nullable()
})

// Utils
const normalizeDay = (d) => {
  const x = new Date(d)
  x.setHours(0, 0, 0, 0)
  return x
}

const isDateDisabled = (d) => {
  const x = normalizeDay(d)
  if (x < todayStart.value) return true // past days
  if (x.getDay() === 0 || x.getDay() === 6) return true // weekends
}

const fieldsByStep = {
  1: ['name', 'email', 'org_address', 'end_address', 'date', 'schedule'],
  2: ['move_type', 'origin_floor', 'origin_elevator', 'destination_floor', 'destination_elevator', 'packing_service', 'comments'],
  3: [] // el 3 es solo resumen
}

async function goNext() {
  if (!formRef.value) return

  const currentFields = fieldsByStep[step.value] || []

  // validamos cada campo del paso actual
  const results = await Promise.all(
    currentFields.map((name) => formRef.value?.validateField(name))
  )

  // si todos los campos del paso actual son válidos, avanzamos
  const allValid = results.every((r) => r?.valid)

  if (allValid) {
    step.value++
  }
}



function goBack() {
  if (step.value > 1) step.value--
}

async function onSubmit(values) {
  try {
    const res = await axios.post('/api/moving-quotes', values)
    if (Swal) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Your quote request was sent successfully!',
        timer: 2000,
        showConfirmButton: false,
      })
    }
  } catch (e) {
    console.error(e)
    if (Swal) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: e.response?.data?.message || 'There was a problem sending your request.',
      })
    }
  }
}

</script>

<template>
  <div class="booking">
    <div class="booking__container container">
      <div class="booking__card">
        <!-- Header -->
        <div class="booking__header">
          <h3 class="booking__header--title" data-aos="fade-up" data-aos-duration="1500">
            Step {{ step }} of 3
          </h3>
          <div class="booking__header--loader">
            <div class="booking__header--loader-bar" :style="`width: ${wbar[step]}`"></div>
          </div>
        </div>

        <div class="booking__content" data-aos="fade-up" data-aos-duration="1500">
          <Form ref="formRef" @submit="onSubmit" :validation-schema="schema" class="" v-slot="{ values }">
            
            <!-- ANIMATED WRAPPER -->
            <transition name="fade" mode="out-in">
              <!-- STEP 1 -->
              <div v-show="step === 1" key="step-1" class="booking__form">
                <div class="booking__form--field">
                  <label for="name" class="booking__form--label">Your Name</label>
                  <Field id="name" name="name" type="text" placeholder="Enter your full name" />
                  <ErrorMessage name="name" class="form-error" />
                </div>

                <div class="booking__form--field">
                  <label for="email" class="booking__form--label">Your Email</label>
                  <Field id="email" name="email" type="email" placeholder="Enter your email" />
                  <ErrorMessage name="email" class="form-error" />
                </div>


                <div class="booking__form--field">
                  <label for="date" class="booking__form--label">Preferred Date</label>
                  <Field name="date" v-slot="{ value, errorMessage, setValue, setTouched }">
                    <DatePicker
                      :value="value"
                      value-type="date"
                      type="date"
                      format="DD/MM/YYYY"
                      :editable="false"
                      :clearable="true"
                      :disabled-date="isDateDisabled"
                      placeholder="dd/mm/yyyy"
                      :input-attr="{ id: 'date' }"
                      @change="(v) => setValue(v)"
                      @blur="() => setTouched(true)"
                    />
                    <p v-if="errorMessage" class="form-error"><span>{{ errorMessage }}</span></p>
                  </Field>
                </div>

                <div class="booking__form--field">
                  <label for="schedule" class="booking__form--label">Schedule</label>
                  <Field name="schedule" v-slot="{ value, errorMessage, setValue, setTouched }">
                    <Multiselect
                      :options="scheduleOptions"
                      :model-value="value"
                      @update:model-value="setValue"
                      @blur="() => setTouched(true)"
                      placeholder="Choose a schedule"
                      mode="single"
                      value-prop="value"
                      label-prop="label"
                      track-by="value"
                      :can-clear="true"
                      :searchable="false"
                      input-id="schedule"
                    />
                    <p class="form-error"><span>{{ errorMessage }}</span></p>
                  </Field>
                </div>
              </div>
            </transition>

            <transition name="fade" mode="out-in">
              <!-- STEP 2 -->
              <div v-show="step === 2" key="step-2" class="booking__form">
                <div class="booking__form--field">
                  <label for="move_type" class="booking__form--label">Type of Move</label>
                  <Field as="select" id="move_type" name="move_type">
                    <option value="">Select one</option>
                    <option value="residential">Residential</option>
                    <option value="office">Office</option>
                    <option value="storage">Storage</option>
                  </Field>
                  <ErrorMessage name="move_type" class="form-error" />
                </div>

                <div class="booking__form--field">
                  <label class="booking__form--label">Do you need packing service?</label>
                  <Field as="select" name="packing_service">
                    <option value="">Select one</option>
                    <option :value="true">Yes</option>
                    <option :value="false">No</option>
                  </Field>
                  <ErrorMessage name="packing_service" class="form-error" />
                </div>

                <div class="booking__form--group">
                  <div class="booking__form--field">
                    <label for="origin_floor" class="booking__form--label">Origin Floor</label>
                    <Field as="select" id="origin_floor" name="origin_floor">
                      <option value="">Select</option>
                      <option v-for="n in 10" :key="n" :value="`${n}`">{{ n }}</option>
                    </Field>
                    <ErrorMessage name="origin_floor" class="form-error" />
                  </div>

                  <div class="booking__form--field">
                    <label class="booking__form--label">Elevator at Origin</label>
                    <Field as="select" name="origin_elevator">
                      <option :value="true">Yes</option>
                      <option :value="false">No</option>
                    </Field>
                    <ErrorMessage name="origin_elevator" class="form-error" />
                  </div>
                </div>

                <div class="booking__form--group">
                  <div class="booking__form--field">
                    <label for="destination_floor" class="booking__form--label">Destination Floor</label>
                    <Field as="select" id="destination_floor" name="destination_floor">
                      <option value="">Select</option>
                      <option v-for="n in 10" :key="n" :value="`${n}`">{{ n }}</option>
                    </Field>
                    <ErrorMessage name="destination_floor" class="form-error" />
                  </div>

                  <div class="booking__form--field">
                    <label class="booking__form--label">Elevator at Destination</label>
                    <Field as="select" name="destination_elevator">
                      <option :value="true">Yes</option>
                      <option :value="false">No</option>
                    </Field>
                    <ErrorMessage name="destination_elevator" class="form-error" />
                  </div>
                </div>

                <div class="booking__form--field">
                  <label for="comments" class="booking__form--label">Additional Comments</label>
                  <Field as="textarea" id="comments" name="comments" rows="3" placeholder="e.g. Narrow street, fragile items, etc." />
                </div>
              </div>
            </transition>

            <transition name="fade" mode="out-in">
              <!-- STEP 3 -->
              <div v-show="step === 3" class="booking__summary" key="step-3">
                <h4>Review your information</h4>
                <ul>
                  <li><strong>Name:</strong> {{ values.name }}</li>
                  <li><strong>Email:</strong> {{ values.email }}</li>
                  <li><strong>From:</strong> {{ values.org_address?.formatted }}</li>
                  <li><strong>To:</strong> {{ values.end_address?.formatted }}</li>
                  <li><strong>Date:</strong> {{ values.date ? new Date(values.date).toLocaleDateString() : '' }}</li>
                  <li><strong>Schedule:</strong> {{ values.schedule }}</li>
                  <li><strong>Move Type:</strong> {{ values.move_type }}</li>
                  <li><strong>Origin:</strong> Floor {{ values.origin_floor }} (Elevator: {{ values.origin_elevator ? 'Yes' : 'No' }})</li>
                  <li><strong>Destination:</strong> Floor {{ values.destination_floor }} (Elevator: {{ values.destination_elevator ? 'Yes' : 'No' }})</li>
                  <li><strong>Packing Service:</strong> {{ values.packing_service ? 'Yes' : 'No' }}</li>
                  <li><strong>Comments:</strong> {{ values.comments }}</li>
                </ul>
              </div>
            </transition>

            <!-- Actions -->
            <div class="booking__form--actions">
              <button v-if="step > 1" type="button" class="button__secondary" @click="goBack">Back</button>
              <button v-if="step < 3" type="button" class="button__primary" @click="goNext">Continue</button>
              <button v-if="step === 3" type="submit" class="button__primary">Confirm & Send</button>
            </div>

          </Form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Fade animation between steps */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
