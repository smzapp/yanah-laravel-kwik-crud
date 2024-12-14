<template>
    <div
      v-for="(field, name) in fields"
      :key="name"
      class="flex flex-col mb-4"
    >
      <template v-if="field.type === 'custom_vue'">
        <component :is="getVueComponent(field.vueSource)" />
      </template>

      <template v-else>
        <label class="text-lg font-medium text-gray-700 mb-1">
          {{ field.label }}
          <span class="text-danger" v-if="field.required">*</span>
        </label>
        <InputField
          :attributes="field"
          :fieldName="name"
          @updateFieldValue="updateFormValue"
        />
      </template>
      
      <p v-if="field.helper_text" class="text-slate-400 text-sm mt-1">{{ field.helper_text }}</p>
    </div>
  </template>
  
  <script setup>
  import { defineAsyncComponent } from "vue";
import InputField from "./InputField.vue";
  
  const props = defineProps({
    fields: {
      type: Object,
      required: true,
    }
  });
  
  const emit = defineEmits(["updateFieldValue"]);
  
  const updateFormValue = (name, value) => {
    emit('updateFieldValue', name, value);
  }

// Dynamically resolve a Vue component
const getVueComponent = (source) => {
  return defineAsyncComponent(() => import(`@/${source}`));
};

  </script>
  