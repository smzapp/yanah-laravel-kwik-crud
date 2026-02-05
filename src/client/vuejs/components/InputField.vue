<template>
  <template v-if="attributes.type === 'textarea'">
    <Textarea
      v-bind="attributes?.inputProps"
      :name="fieldName"
      v-model="inputField"
      :class="`border-gray-300 shadow-sm focus:ring-primary-default focus:border-primary-default p-2 w-full ${attributes.class}`"
      :rows="attributes?.rows"
    >{{ attributes?.value}}</Textarea>
  </template>

  <template v-else-if="attributes.type === 'radio'">
    <div v-for="(option, index) in attributes.options" :key="index" class="flex gap-3 mt-1">
      <RadioButton 
        :value="option.value"
        v-bind="attributes?.inputProps"
        v-model="inputField"
      />
      <label>{{ option.label }}</label>
    </div>
  </template>

  <template v-else-if="attributes.type === 'select'">
    <Select 
      :options="attributes.options" 
      :id="fieldName"
      :name="fieldName"
      :placeholder="`- Select -`" 
      :class="`${attributes.class}`"
      optionLabel="label"
      optionValue="optionValue"
      v-model="inputField"
      v-bind="attributes?.inputProps"
    />
  </template>

  <template v-else-if="attributes.type === 'select_group'">
    <Select 
      v-model="inputField"
      :options="attributes.options" 
      optionLabel="label" 
      optionGroupLabel="label" 
      optionGroupChildren="items" 
      :placeholder="attributes.placeholder" 
      v-bind="attributes?.inputProps"
      optionValue="optionValue"
    >
        <template #optiongroup="slotProps">
            <div>
                <div>{{ slotProps.option.label }}</div>
            </div>
        </template>
      </Select>
  </template>
  
  <template v-else-if="attributes.type === 'upload'">
    <FileUpload 
      v-bind="attributes?.inputProps"
      :multiple="true"
      mode="basic" 
      @select="onFileSelect"
      customUpload
      auto
      severity="secondary"
      class="p-button-outlined"
    />
    <div
      v-if="normalizedImages.length"
      class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mt-4"
    >
      <img
        v-for="(image, index) in normalizedImages"
        :key="index"
        :src="image"
        class="w-full h-28 object-cover rounded-lg border border-gray-200"
      />
    </div>
  </template>


  <template v-else-if="attributes.type === 'switch'">
    <ToggleSwitch
      v-bind="attributes?.inputProps"
      :modelValue="inputField ? true : false"
      v-model="inputField"
    />
  </template>

  <template v-else-if="attributes.type === 'calendar'">
      <DatePicker 
        v-bind="attributes?.inputProps"
        v-model="inputField"
        :showIcon="attributes?.showIcon ?? true"
      />
  </template>
  
  <template v-else-if="attributes.type === 'input_group'">
      <InputGroup>
          <InputGroupAddon v-if="attributes.group_icon">
              <i :class="attributes.group_icon"></i>
          </InputGroupAddon>
          <InputText 
            :name="fieldName" 
            :placeholder="attributes.placeholder" 
            v-bind="attributes?.inputProps" 
            v-model="inputField"
          />
      </InputGroup>
    </template>

  <template v-else>
    <InputText 
      v-bind="attributes?.inputProps"
      :name="fieldName"
      :class="`border-gray-300 shadow-sm focus:ring-primary-default focus:border-primary-default p-2 w-full ${attributes.class}`"
      v-model="inputField"
    />
  </template>
</template>

<script setup>
import Select from 'primevue/select';
import RadioButton from 'primevue/radiobutton';
import FileUpload from 'primevue/fileupload';
import DatePicker from 'primevue/datepicker';
import Textarea from 'primevue/textarea';
import { ref, watch, computed } from 'vue';
import { InputGroup, InputGroupAddon, InputText, ToggleSwitch } from 'primevue';

const props = defineProps({
  attributes: {
    type: Object,
    required: true,
  },
  fieldName: {
    type: String,
    required: true,
  },
});

const inputField = ref(props.attributes.value || '');
const emit = defineEmits(['updateFieldValue']);

const src = ref([]); // uploaded previews

// ✅ Normalize existing + uploaded images
const normalizedImages = computed(() => {

  // prioritize uploaded previews
  if (src.value?.length) {
    return src.value;
  }

  let value = props.attributes?.value;

  if (!value) return [];

  if (Array.isArray(value)) {
    return value;
  }

  if (typeof value === 'string') {
    try {
      const parsed = JSON.parse(value);
      if (Array.isArray(parsed)) {
        return parsed;
      }
      return [value];
    } catch (e) {
      return [value];
    }
  }

  return [];
});

function onFileSelect(event) {
  src.value = [];

  const files = event.files;

  files.forEach((file) => {
    const reader = new FileReader();

    reader.onload = (e) => {
      const base64 = e.target.result;

      src.value.push(base64);

      emit('updateFieldValue', props.fieldName, src.value);
    };

    reader.readAsDataURL(file);
  });
}

watch(inputField, (newValue) => {
  emit('updateFieldValue', props.fieldName, newValue);
}, { immediate: true });
</script>
