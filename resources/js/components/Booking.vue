<script setup>
import { ref, computed } from 'vue';
import { Field, Form, ErrorMessage } from 'vee-validate';
import * as Yup from 'yup';
import DatePicker from 'vue-datepicker-next'
import 'vue-datepicker-next/index.css'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import AddressAutocomplete from './AddressAutocomplete.vue'

// Pasa tu API KEY (idealmente desde env)
const GOOGLE_API_KEY = import.meta.env.VITE_GOOGLE_MAPS_API_KEY


const props = defineProps({
  wp_action: {
    type: String,
    default: 'success'
  },
  disabledDates:{
    type: Array,
    default: () => []
  }
})

// vars
const step = ref(1);

const wbar = ref({
  1: '33%', 
  2: '66%', 
  3: '100%', 
})

const scheduleOptions = ref([
  {
    label: 'Morning (8am - 12pm)',
    value: 'morning',
  },
  {
    label: 'Afternoon (1pm - 5pm)',
    value: 'afternoon',
  },
])

const todayStart = computed(() => {
  const d = new Date();
  d.setHours(0,0,0,0);
  return d;
})

const schema = Yup.object().shape({
  name: Yup.string().required().label('Name'),
  email: Yup.string().email().required().label('Email'),
  org_address: Yup.object({
    formatted: Yup.string().required(),
    lat: Yup.string().required(),
    lng: Yup.string().required()
  }).required().label('Origin Address'),
  end_address: Yup.object({
    formatted: Yup.string().required(),
    lat: Yup.string().required(),
    lng: Yup.string().required()
  }).required().label('End Address'),
  date: Yup.date().required().min(todayStart.value, 'Date cannot be in the past').label('Preferred Date'),
  schedule: Yup.string().required('Please choose a service') 
});

const normalizeDay = (d) => {
  const x = new Date(d);
  x.setHours(0, 0, 0, 0);
  return x;
};

const isDateDisabled = (d) => {
  const x = normalizeDay(d);
  // no past day
  if( x < todayStart.value) return true

  // weekend
  if(x.getDay() === 0 || x.getDay() === 6) return true
  
  
}

function onSubmit(values) {
  // Ejemplo de formateo
  const d = values.date;
  const pad = (n) => String(n).padStart(2, '0');
  const payload = {
    ...values,
    date_iso: d.toISOString(),
    date_ddmmyyyy: `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`
  };
  alert(JSON.stringify(payload, null, 2));
}

</script>

<template>
  <div class="booking">
    <div class="booking__container container">
      <div class="booking__card">
        <div class="booking__header">
          <h3 class="booking__header--title"  data-aos="fade-up" data-aos-duration="1500">
            Step {{ step }} of 3
          </h3>
          <div class="booking__header--loader">
            <div class="booking__header--loader-bar" :style="`width: ${wbar[step]}`"></div>
          </div>
        </div>
        <div class="booking__content"  data-aos="fade-up" data-aos-duration="1500">
          <Form @submit="onSubmit" :validation-schema="schema" class="booking__form">
            <div class="booking__form--field">
              <label for="name" class="booking__form--label">Your Name</label>
              <Field id="name" name="name" type="text" placeholder="Enter your full name" />
              <ErrorMessage name="name" class="form-error"/>
            </div>

            <div class="booking__form--field">
              <label for="email" class="booking__form--label">Your Email</label>
              <Field id="email" name="email" type="email" placeholder="Enter your email"/>
              <ErrorMessage name="email" class="form-error"/>
            </div>

            <!-- Origin Address -->
            <div class="booking__form--field">
              <label for="org_address" class="booking__form--label">Origin Address</label>
              <Field name="org_address" v-slot="{ value, errorMessage, setValue, setTouched }">
                <AddressAutocomplete
                  :api-key="GOOGLE_API_KEY"
                  :model-value="value"
                  :countries="['us','ve','nl']"
                  :biasCenter="{ lat: 10.4806, lng: -66.9036 }"
                  placeholder="Start typing the origin address"
                  @update:modelValue="setValue"
                  @blur="() => setTouched(true)"
                />
                <p class="form-error" v-if="errorMessage"><span>{{ errorMessage }}</span></p>
              </Field>
            </div>

            <!-- Destination Address -->
            <div class="booking__form--field">
              <label for="end_address" class="booking__form--label">End Address</label>
              <Field name="end_address" v-slot="{ value, errorMessage, setValue, setTouched }">
                <AddressAutocomplete
                  :api-key="GOOGLE_API_KEY"
                  :model-value="value"
                  :countries="['us','ve','nl']"
                  placeholder="Destination address"
                  color="red"
                  @update:modelValue="setValue"
                  @blur="() => setTouched(true)"
                />
                <p class="form-error" v-if="errorMessage"><span>{{ errorMessage }}</span></p>
              </Field>
            </div>

            <div class="booking__form--field">
              <label for="date" class="booking__form--label">Preferred Date</label>
              <Field name="date" v-slot="{value, errorMessage, setValue, setTouched}">
                <DatePicker
                  :value="value"
                  value-type="date"
                  type="date"
                  format="DD/MM/YYYY"
                  :editable="false"
                  :clearable="true"
                  :disabled-date="isDateDisabled"
                  placeholder="dd/mm/aaaa"
                  :input-attr="{ id: 'date' }"        
                  @change="(v) => setValue(v)"
                  @blur="() => setTouched(true)" 
                />
                <p v-if="errorMessage?.length > 0" class="form-error"><span>{{ errorMessage }}</span></p>
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

            <button class="booking__form--button button__primary" type="submit">Continue</button>
          </Form>
        </div>
      </div>
    </div>
  </div>
</template>