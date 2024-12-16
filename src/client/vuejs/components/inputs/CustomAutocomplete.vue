<template>
  <div v-bind="field?.others?.wrapper">
    <label v-bind="field?.others?.labelOthers" class="text-lg font-medium text-gray-700 mb-1">
      {{ field.label }}
      <span class="text-danger" v-if="field.required">*</span>
    </label>
    <div class="card">
      <AutoComplete 
        v-bind="field?.others?.inputOthers"
        v-model="selectedComplete" 
        forceSelection 
        optionLabel="label" 
        :suggestions="filteredSuggestions" 
        @complete="search"
        @valueChange="changeAutoComplete"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { AutoComplete } from 'primevue';
import { ref, onMounted } from "vue";

const props = defineProps({
  field: Object,
  name: String,
})

onMounted(() => {
  suggestedItems.value = props.field?.default_query_results;
});

const emit = defineEmits(['updateFieldValue']);

const suggestedItems = ref();
const selectedComplete = ref();
const filteredSuggestions = ref();

const changeAutoComplete = (event) => {
  if(event?.value != undefined) {
    emit('updateFieldValue', props.name, event.value);
  }
}

const search = (event) => {
    if (!event.query.trim().length) {
      filteredSuggestions.value = [...suggestedItems.value];
    } else {
      filteredSuggestions.value = suggestedItems.value.filter((country) => {
        return country.label.toLowerCase().startsWith(event.query.toLowerCase());
      });
    }
}
</script>

