<template>
  <div v-bind="field?.wrapperProps" class="mb-4">
    <template v-if="field.type === 'custom_file'">
      <component 
        :is="currentComponent"
        v-on="$attrs"
        @updateFieldValue="updateFormValue"
        v-bind="{attributes: field, fieldName: fieldName}"
      />
    </template>
    
    <template v-else-if="field.type === 'custom_html'">
      <div v-html="field?.value" />
    </template>

    <template v-else>
      <template v-if="field.type === 'checkbox'">
        <div :class="`flex items-center gap-2`">
          <Checkbox
            v-model="inputCheckbox"
            :binary="field?.is_boolean"
            @valueChange="(newValue) => $emit('updateFieldValue', fieldName, newValue)"
          />
          <label :for="fieldName"  v-bind="field?.labelProps"> 
            <span>{{  field.label }} </span>
            <span 
              v-if="field?.tooltip_label"
              v-tooltip.top="field?.tooltip_label"
              class="pi-question-circle pi ml-2" 
            ></span>
          </label>
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
        <div class="flex flex-col">
          <label  v-bind="field?.labelProps" class="text-lg font-medium text-gray-700 mb-1">
            {{ field.label }}
            <span class="text-danger" v-if="field.required">*</span>
            <span 
              v-if="field?.tooltip_label"
              v-tooltip.top="field?.tooltip_label"
              class="pi-question-circle pi ml-2" 
            ></span>
          </label>
          <InputField
            :attributes="field"
            :fieldName="fieldName"
            v-bind="field?.inputProps"
            @updateFieldValue="updateFormValue"
          />
        </div>
      </template>
    </template>
    
    <p v-if="field.helper_text" class="text-slate-400 text-sm mt-1">{{ field.helper_text }}</p>
  </div>
</template>
  
<script setup>
import Checkbox from 'primevue/checkbox';
import { markRaw, onMounted, ref } from "vue";
import InputField from "./InputField.vue";
import CustomAutocomplete from './inputs/CustomAutocomplete.vue';

const props = defineProps({
  fieldName: String,
  field: Object,
});
const emit = defineEmits(["updateFieldValue"]);

const inputCheckbox = ref(props.field?.is_boolean ? !!props.field?.value : props.field?.value);
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

/**
 * Methods
 */
const updateFormValue = (name, value) => {
  emit('updateFieldValue', name, value);
}

</script>
  
