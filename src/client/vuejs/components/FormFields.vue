<template>
  <template v-if="field.type === 'custom_file'">
    <component 
      :is="getVueComponent(field.source)"
      @updateFieldValue="updateFormValue"
      :attributes="field"
      :fieldName="fieldName"
    />
  </template>

  <template v-if="field.type === 'checkbox'">
    <div :class="`flex items-center gap-2`" v-bind="field?.others?.wrapper">
      <Checkbox 
        :inputId="`${fieldName}`" 
        :name="fieldName"
        :value="field.value"
        v-bind="field?.others?.inputProps"
        @change="updateCheckBox"
      />
      <label :for="fieldName"  v-bind="field?.others?.labelProps"> {{  field.label }} </label>
    </div>
  </template>
  
  <template v-else-if="field.type === 'switch'">
    <div :class="`flex items-center gap-2`" v-bind="field?.others?.wrapper">
      <label v-bind="field?.others?.labelProps" class="text-lg font-medium text-gray-700" >
        {{ field.label }}
        <span class="text-danger" v-if="field.required">*</span>
      </label>
      <ToggleSwitch
        v-bind="field?.others?.inputProps"
        @valueChange="updateSwitch(fieldName, $event)"
      />
    </div>
  </template>
  
  <template v-else-if="field.type === 'autocomplete'">
    <CustomAutocomplete
      :field="field"
      :name="fieldName"
      @updateFieldValue="updateFormValue"
    />
  </template>
  
  <template v-else>
    <div  v-bind="field?.others?.wrapper">
      <label  v-bind="field?.others?.labelProps" class="text-lg font-medium text-gray-700 mb-1">
        {{ field.label }}
        <span class="text-danger" v-if="field.required">*</span>
      </label>
      <InputField
        :attributes="field"
        :fieldName="fieldName"
          v-bind="field?.others?.inputProps"
        @updateFieldValue="updateFormValue"
      />
    </div>
  </template>
  
  <p v-if="field.helper_text" class="text-slate-400 text-sm mt-1">{{ field.helper_text }}</p>
</template>
  
<script setup>
import Checkbox from 'primevue/checkbox';
import { defineAsyncComponent } from "vue";
import InputField from "./InputField.vue";
import { ToggleSwitch } from 'primevue';
import CustomAutocomplete from './inputs/CustomAutocomplete.vue';
  
const props = defineProps({
  fieldName: String,
  field: Object,
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
  