<template>
    <div
      v-for="(field, name) in fields"
      :key="name"
      class="flex flex-col mb-4"
    >
      <label class="text-lg font-medium text-gray-700 mb-1">
        {{ field.label }}
        <span class="text-danger" v-if="field.required">*</span>
      </label>
      <InputField
        :attributes="field"
        :fieldName="name"
        @updateFieldValue="updateFormValue"
      />
    </div>
  </template>
  
  <script setup>
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
  </script>
  