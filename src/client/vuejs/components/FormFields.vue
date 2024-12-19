<template>
  <template v-if="field.type === 'custom_file'">
    <component 
      :is="currentComponent"
      v-on="$attrs"
      @updateFieldValue="updateFormValue"
      v-bind="{attributes: field, fieldName: fieldName}"
    />
  </template>

  <template v-else>
    <template v-if="field.type === 'checkbox'">
      <div :class="`flex items-center gap-2`" v-bind="field?.others?.wrapper">
        <Checkbox 
          :inputId="`${fieldName}`" 
          :name="fieldName"
          v-bind="field?.others?.inputProps"
          :value="field?.value"
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
          :value="field?.value"
          @valueChange="updateSwitch(fieldName, $event)"
        />
      </div>
    </template>
    
    <template v-else-if="field.type === 'autocomplete'">
      <CustomAutocomplete
        :field="field"
        :name="fieldName"
        :value="field?.value"
        @updateFieldValue="updateFormValue"
      />
    </template>
    
    <template v-else>
      <div class="flex flex-col" v-bind="field?.others?.wrapper">
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
  </template>
  
  <p v-if="field.helper_text" class="text-slate-400 text-sm mt-1">{{ field.helper_text }}</p>
</template>
  
<script setup>
import Checkbox from 'primevue/checkbox';
import { markRaw, onMounted, ref } from "vue";
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
  emit('updateFieldValue', props.fieldName, event.target.checked);
}

const updateSwitch = (name, value) => {
  emit('updateFieldValue', name, value);
}

const components = import.meta.glob('@/Components/**/*.vue');
const currentComponent = ref(null);

onMounted(() => {
  const resolvedSource = props.field?.source?.replace(/^@/, '/resources/js');
  const componentPath = components[resolvedSource];

  if (componentPath) {
    componentPath().then((mod) => {
      currentComponent.value = markRaw(mod.default); 
    });
  }
});
</script>
  