<template>
  <div v-bind="field?.others?.wrapper">
    <label v-bind="field?.others?.labelProps" class="text-lg font-medium text-gray-700 mb-1">
      {{ field.label }}
      <span class="text-danger" v-if="field?.required">*</span>
    </label>
    <div class="card">
      <AutoComplete 
        v-bind="field?.others?.inputProps"
        v-model="selectedComplete" 
        forceSelection 
        optionLabel="label" 
        :suggestions="filteredSuggestions" 
        :emptyMessage="dropdownMessage"
        @complete="search"
        @valueChange="changeAutoComplete"
        :defaultValue="field?.defaultValue"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from 'axios';
import { AutoComplete } from 'primevue';

// Define Props
const props = defineProps({
  field: {
    type: Object,
    required: true
  },
  name: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['updateFieldValue']);

const suggestedItems = ref([]);
const selectedComplete = ref(null);
const filteredSuggestions = ref([]);
const dropdownMessage = ref('No results found.');

onMounted(() => {
  suggestedItems.value = props.field?.default_query_results || [];
  filteredSuggestions.value = [...suggestedItems.value];
});

const changeAutoComplete = (event) => {
  if (event?.value !== undefined) {
    emit('updateFieldValue', props.name, event.value);
  }
};

const search = async (event) => {
  const query = event.query.trim();

  if (!query.length) {
    filteredSuggestions.value = [...suggestedItems.value];
    return;
  }

  let result = suggestedItems.value.filter((item) => 
    item.label.toLowerCase().startsWith(query.toLowerCase())
  );

  // If no results, query the API
  if (!result.length && props.field?.api_endpoint) {
    dropdownMessage.value = 'Searching...';

    try {
      const response = await axios.get(`${props.field.api_endpoint}?query=${query}`);
      result = response.data.data || [];
      suggestedItems.value = result; 
    } catch (error) {
      console.error('API Error:', error);
      dropdownMessage.value = 'Error retrieving suggestions.';
    }
  }

  filteredSuggestions.value = result;
};
</script>
