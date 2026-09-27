<script setup>
import { ref, computed } from 'vue'
import { Field, Form, ErrorMessage } from 'vee-validate'
import * as Yup from 'yup'
import DatePicker from 'vue-datepicker-next'
import 'vue-datepicker-next/index.css'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import axios from 'axios'
import Swal from 'sweetalert2'

// Props
const props = defineProps({
  wp_action: { type: String, default: 'success' },
  disabledDates: { type: Array, default: () => [] }
})

const CONTACT_PHONE = { display: '(770) 589-9512', tel: '+17705899512' }

// Step system: 1 = contact & date, 2 = size of the move, 3 = confirmation
const step = ref(1)
const quoteId = ref(null)
const step1FormRef = ref()
const step2FormRef = ref()
const submitting = ref(false)

const wbar = {
  1: '50%',
  2: '100%',
  3: '100%'
}

// Options
const scheduleOptions = [
  { label: 'Morning (8am - 12pm)', value: 'morning' },
  { label: 'Afternoon (1pm - 5pm)', value: 'afternoon' },
  { label: "I'm flexible", value: 'flexible' }
]

const moveTypeOptions = [
  { label: 'House', value: 'house' },
  { label: 'Apartment', value: 'apartment' },
  { label: 'Office', value: 'office' },
  { label: 'Labor only, no truck', value: 'labor_only' }
]

const bedroomOptions = [
  { label: 'Studio', value: 'studio' },
  { label: '1', value: '1' },
  { label: '2', value: '2' },
  { label: '3', value: '3' },
  { label: '4+', value: '4+' }
]

const floorOptions = [
  { label: 'Ground floor', value: 'ground' },
  { label: '1', value: '1' },
  { label: '2', value: '2' },
  { label: '3+', value: '3+' }
]

const specialItemOptions = [
  { label: 'Large fridge', value: 'large_fridge' },
  { label: 'Piano', value: 'piano' },
  { label: 'Pool table', value: 'pool_table' },
  { label: 'Jacuzzi / hot tub', value: 'jacuzzi' },
  { label: 'Safe', value: 'safe' }
]

// Date limits
const todayStart = computed(() => {
  const d = new Date()
  d.setHours(0, 0, 0, 0)
  return d
})

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

// Step 1 validation
const step1Schema = Yup.object().shape({
  name: Yup.string().nullable(),
  phone: Yup.string().required('Phone is required').label('Phone'),
  sms_consent: Yup.boolean(),
  email: Yup.string().email('Enter a valid email').nullable(),
  date: Yup.date()
    .nullable()
    .min(todayStart.value, 'Date cannot be in the past')
    .when('date_flexible', {
      is: false,
      then: (s) => s.required('Choose a moving date or mark it as flexible')
    })
    .label('Moving Date'),
  date_flexible: Yup.boolean(),
  schedule: Yup.string().required('Please choose a time window'),
  origin_zip: Yup.string().matches(/^\d{5}$/, 'Enter a 5-digit ZIP code').required('Origin ZIP is required'),
  destination_zip: Yup.string().matches(/^\d{5}$/, 'Enter a 5-digit ZIP code').required('Destination ZIP is required')
})

// Step 2 validation
const step2Schema = Yup.object().shape({
  move_type: Yup.string().required('Select the type of move'),
  bedrooms: Yup.string().required('Select how many bedrooms'),
  origin_floor: Yup.string().required('Select the origin floor'),
  origin_elevator: Yup.boolean().required(),
  destination_floor: Yup.string().required('Select the destination floor'),
  destination_elevator: Yup.boolean().required(),
  packing_service: Yup.boolean().required('Let us know if you need packing'),
  special_items: Yup.array().of(Yup.string()),
  comments: Yup.string().max(500).nullable()
})

const photoFiles = ref([])
const photoError = ref('')
const MAX_PHOTOS = 8
const MAX_PHOTO_SIZE = 8 * 1024 * 1024 // 8MB

function onPhotosChange(e) {
  const files = Array.from(e.target.files || [])
  photoError.value = ''

  if (files.length > MAX_PHOTOS) {
    photoError.value = `You can upload up to ${MAX_PHOTOS} photos.`
    return
  }
  if (files.some((f) => f.size > MAX_PHOTO_SIZE)) {
    photoError.value = 'Each photo must be smaller than 8MB.'
    return
  }

  photoFiles.value = files
}

async function onStep1Submit(values) {
  submitting.value = true
  try {
    const { data } = await axios.post('/api/moving-quotes', values)
    quoteId.value = data.quote?.id ?? null
    step.value = 2
  } catch (e) {
    console.error(e)
    if (Swal) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: e.response?.data?.message || 'There was a problem saving your request.',
      })
    }
  } finally {
    submitting.value = false
  }
}

async function onStep2Submit(values) {
  submitting.value = true
  try {
    const formData = new FormData()
    Object.entries(values).forEach(([key, value]) => {
      if (key === 'special_items') {
        (value || []).forEach((item) => formData.append('special_items[]', item))
      } else if (typeof value === 'boolean') {
        // Laravel's `boolean` rule only accepts true/false/1/0/"1"/"0", not the
        // "true"/"false" strings FormData.append() would otherwise produce.
        formData.append(key, value ? '1' : '0')
      } else if (value !== null && value !== undefined) {
        formData.append(key, value)
      }
    })
    photoFiles.value.forEach((file) => formData.append('photos[]', file))

    await axios.post(`/api/moving-quotes/${quoteId.value}/complete`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    step.value = 3
  } catch (e) {
    console.error(e)
    if (Swal) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: e.response?.data?.message || 'There was a problem sending your request.',
      })
    }
  } finally {
    submitting.value = false
  }
}

function goBackToStep1() {
  step.value = 1
}
</script>

<template>
  <div class="booking">
    <div class="booking__container container">
      <div class="booking__card">
        <!-- Header -->
        <div class="booking__header" v-if="step < 3">
          <h3 class="booking__header--title" data-aos="fade-up" data-aos-duration="1500">
            <template v-if="step === 1">Step 1 of 2 — takes less than a minute</template>
            <template v-else>Step 2 of 2 — this is the part that gets you an accurate price</template>
          </h3>
          <div class="booking__header--loader">
            <div class="booking__header--loader-bar" :style="`width: ${wbar[step]}`"></div>
          </div>
        </div>

        <div class="booking__content" data-aos="fade-up" data-aos-duration="1500">
          <!-- STEP 1: Contact & date -->
          <Form
            v-if="step === 1"
            ref="step1FormRef"
            @submit="onStep1Submit"
            :validation-schema="step1Schema"
            :initial-values="{ sms_consent: false, date_flexible: false }"
            v-slot="{ values }"
          >
            <div class="booking__form">
              <div class="booking__form--field">
                <label for="name" class="booking__form--label">Your Name</label>
                <Field id="name" name="name" type="text" placeholder="Enter your full name" />
                <ErrorMessage name="name" class="form-error" />
              </div>

              <div class="booking__form--field">
                <label for="phone" class="booking__form--label">Phone Number</label>
                <Field id="phone" name="phone" type="tel" placeholder="Enter your phone number" />
                <ErrorMessage name="phone" class="form-error" />
                <label class="booking__form--checkbox">
                  <Field name="sms_consent" type="checkbox" :value="true" :unchecked-value="false" />
                  <span>I can receive text messages at this number</span>
                </label>
              </div>

              <div class="booking__form--field">
                <label for="email" class="booking__form--label">Your Email (optional)</label>
                <Field id="email" name="email" type="email" placeholder="Enter your email" />
                <ErrorMessage name="email" class="form-error" />
              </div>

              <div class="booking__form--field">
                <label for="date" class="booking__form--label">Moving Date</label>
                <Field name="date" v-slot="{ value, errorMessage, setValue, setTouched }">
                  <DatePicker
                    :value="value"
                    value-type="date"
                    type="date"
                    format="MM/DD/YYYY"
                    :editable="false"
                    :clearable="true"
                    :disabled="values.date_flexible"
                    :disabled-date="isDateDisabled"
                    placeholder="mm/dd/yyyy"
                    :input-attr="{ id: 'date' }"
                    @change="(v) => setValue(v)"
                    @blur="() => setTouched(true)"
                  />
                  <p v-if="errorMessage" class="form-error"><span>{{ errorMessage }}</span></p>
                </Field>
                <label class="booking__form--checkbox">
                  <Field name="date_flexible" type="checkbox" :value="true" :unchecked-value="false" />
                  <span>My date is flexible</span>
                </label>
              </div>

              <div class="booking__form--field">
                <label for="schedule" class="booking__form--label">Preferred Time</label>
                <Field name="schedule" v-slot="{ value, errorMessage, setValue, setTouched }">
                  <Multiselect
                    :options="scheduleOptions"
                    :model-value="value"
                    @update:model-value="setValue"
                    @blur="() => setTouched(true)"
                    placeholder="Choose a time window"
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

              <div class="booking__form--group">
                <div class="booking__form--field">
                  <label for="origin_zip" class="booking__form--label">Origin ZIP Code</label>
                  <Field id="origin_zip" name="origin_zip" type="text" inputmode="numeric" maxlength="5" placeholder="e.g. 30301" />
                  <ErrorMessage name="origin_zip" class="form-error" />
                </div>

                <div class="booking__form--field">
                  <label for="destination_zip" class="booking__form--label">Destination ZIP Code</label>
                  <Field id="destination_zip" name="destination_zip" type="text" inputmode="numeric" maxlength="5" placeholder="e.g. 30303" />
                  <ErrorMessage name="destination_zip" class="form-error" />
                </div>
              </div>

              <div class="booking__form--actions">
                <span></span>
                <button type="submit" class="button__primary" :disabled="submitting">
                  {{ submitting ? 'Saving...' : 'Continue' }}
                </button>
              </div>
            </div>
          </Form>

          <!-- STEP 2: Size of the move -->
          <Form
            v-else-if="step === 2"
            ref="step2FormRef"
            @submit="onStep2Submit"
            :validation-schema="step2Schema"
            :initial-values="{ origin_elevator: false, destination_elevator: false, packing_service: false, special_items: [] }"
          >
            <div class="booking__form">
              <div class="booking__form--field">
                <label for="move_type" class="booking__form--label">Type of Move</label>
                <Field as="select" id="move_type" name="move_type">
                  <option value="">Select one</option>
                  <option v-for="opt in moveTypeOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </Field>
                <ErrorMessage name="move_type" class="form-error" />
              </div>

              <div class="booking__form--field booking__form--field-wide">
                <label class="booking__form--label">How Many Bedrooms?</label>
                <Field name="bedrooms" v-slot="{ value, setValue }">
                  <div class="booking__chip-group">
                    <button
                      v-for="opt in bedroomOptions"
                      :key="opt.value"
                      type="button"
                      class="booking__chip"
                      :class="{ 'booking__chip--active': value === opt.value }"
                      @click="setValue(opt.value)"
                    >
                      {{ opt.label }}
                    </button>
                  </div>
                </Field>
                <ErrorMessage name="bedrooms" class="form-error" />
              </div>

              <div class="booking__form--group">
                <div class="booking__form--field">
                  <label for="origin_floor" class="booking__form--label">Origin Floor</label>
                  <Field as="select" id="origin_floor" name="origin_floor">
                    <option value="">Select</option>
                    <option v-for="opt in floorOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
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
                    <option v-for="opt in floorOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
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
                <label class="booking__form--label">Do You Need Packing Help?</label>
                <Field as="select" name="packing_service">
                  <option :value="true">Yes</option>
                  <option :value="false">No</option>
                </Field>
                <ErrorMessage name="packing_service" class="form-error" />
              </div>

              <div class="booking__form--field booking__form--field-wide">
                <label class="booking__form--label">Special Items</label>
                <div class="booking__checkbox-group">
                  <label v-for="opt in specialItemOptions" :key="opt.value" class="booking__form--checkbox">
                    <Field name="special_items" type="checkbox" :value="opt.value" />
                    <span>{{ opt.label }}</span>
                  </label>
                </div>
              </div>

              <div class="booking__form--field booking__form--field-wide">
                <label for="photos" class="booking__form--label">Photos (optional)</label>
                <p class="booking__form--hint">Upload photos of what you're moving and we'll give you a more accurate price.</p>
                <input id="photos" type="file" accept="image/*" multiple @change="onPhotosChange" />
                <p v-if="photoError" class="form-error"><span>{{ photoError }}</span></p>
                <p v-else-if="photoFiles.length" class="booking__form--hint">{{ photoFiles.length }} photo(s) selected</p>
              </div>

              <div class="booking__form--field booking__form--field-wide">
                <label for="comments" class="booking__form--label">Comments (optional)</label>
                <Field as="textarea" id="comments" name="comments" rows="3" placeholder="e.g. Narrow street, fragile items, etc." />
              </div>

              <div class="booking__form--actions">
                <button type="button" class="button__secondary" @click="goBackToStep1">Back</button>
                <button type="submit" class="button__primary" :disabled="submitting">
                  {{ submitting ? 'Sending...' : 'Get My Estimate' }}
                </button>
              </div>
            </div>
          </Form>

          <!-- STEP 3: Confirmation -->
          <div v-else class="booking__confirmation">
            <h4 class="booking__confirmation--title">We've got your request!</h4>
            <p class="booking__confirmation--text">
              We'll text or email you in the next few minutes with your estimate.
            </p>
            <p class="booking__confirmation--subtext">Prefer to talk now?</p>
            <div class="booking__confirmation--actions">
              <a :href="`tel:${CONTACT_PHONE.tel}`" class="button__primary">Call {{ CONTACT_PHONE.display }}</a>
              <a :href="`sms:${CONTACT_PHONE.tel}`" class="button__secondary">Send a Text</a>
            </div>
          </div>
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
