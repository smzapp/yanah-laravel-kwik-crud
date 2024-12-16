<template>
    <div
      v-for="(field, name) in fields"
      :key="name"
      class="flex flex-col mb-4"
    >
      <template v-if="field.type === 'custom_file'">
        <component 
          :is="getVueComponent(field.source)"
          @updateFieldValue="updateFormValue"
          :attributes="field"
          :fieldName="name"
        />
      </template>

      <template v-if="field.type === 'checkbox'">
        <div :class="`flex items-center gap-2`" v-bind="field?.others?.wrapper">
          <Checkbox 
            :inputId="`${name}`" 
            :name="name"
            :value="field.value"
            v-bind="field?.others?.inputProps"
            @change="updateCheckBox"
          />
          <label :for="name"  v-bind="field?.others?.labelProps"> {{  field.label }} </label>
        </div>
      </template>
      
      <template v-else-if="field.type === 'switch'">
        <div :class="`flex items-center gap-2`" v-bind="field?.others?.wrapper">
          <label v-bind="field?.others?.labelProps" class="text-lg font-medium text-gray-700 mb-1" >
            {{ field.label }}
            <span class="text-danger" v-if="field.required">*</span>
          </label>
          <ToggleSwitch
            v-bind="field?.others?.inputProps"
            @valueChange="updateSwitch(name, $event)"
          />
        </div>
      </template>
      
      <template v-else-if="field.type === 'autocomplete'">
        <CustomAutocomplete
          :field="field"
          :name="name"
          @updateFieldValue="updateFormValue"
        />
      </template>

      <template v-else>
        <div :class="`flex gap-2 flex-col`" v-bind="field?.others?.wrapper">
          <label  v-bind="field?.others?.labelProps" class="text-lg font-medium text-gray-700 mb-1">
            {{ field.label }}
            <span class="text-danger" v-if="field.required">*</span>
          </label>
          <InputField
            :attributes="field"
            :fieldName="name"
             v-bind="field?.others?.inputProps"
            @updateFieldValue="updateFormValue"
          />
        </div>
      </template>
      
      <p v-if="field.helper_text" class="text-slate-400 text-sm mt-1">{{ field.helper_text }}</p>
    </div>
  </template>
  
<script setup>
import Checkbox from 'primevue/checkbox';
import { defineAsyncComponent } from "vue";
import InputField from "./InputField.vue";
import { ToggleSwitch } from 'primevue';
import CustomAutocomplete from './inputs/CustomAutocomplete.vue';
  
const props = defineProps({
  fields: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["updateFieldValue"]);

const updateFormValue = (name, value) => {
  emit('updateFieldValue', name, value);
}

const updateCheckBox = (event) => {
  const target = event.target;
  emit('updateFieldValue', target.name, target.value);
}

const updateSwitch = (name, value) => {
  emit('updateFieldValue', name, value);
}

const getVueComponent = (source) => {
const resolvedSource = source.replace(/^@/, '/resources/js');
return defineAsyncComponent(() => import(`${resolvedSource}`));
};

</script>
  