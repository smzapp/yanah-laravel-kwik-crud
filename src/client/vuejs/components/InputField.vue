<template>
  <template v-if="attributes.type === 'textarea'">
    <textarea
      v-bind="attributes?.inputProps"
      :id="fieldName"
      :name="fieldName"
      @change="handleInput"
      :class="`border-gray-300 shadow-sm focus:ring-primary-default focus:border-primary-default p-2 w-full ${attributes.class}`"
      :rows="attributes?.rows"
    >{{ attributes?.value}}</textarea>
  </template>

  <template v-else-if="attributes.type === 'radio'">
    <div v-for="(option, index) in attributes.options" :key="index" class="flex gap-3 mt-1">
      <RadioButton 
        :value="option.value"
        v-bind="attributes?.inputProps"
        @change="handleInput"
      />
      <label>{{ option.label }}</label>
    </div>
  </template>

  <template v-else-if="attributes.type === 'select'">
    <Select 
      @change="handleInput"
      :options="attributes.options" 
      :id="fieldName"
      :name="fieldName"
      :placeholder="`- Select -`" 
      :class="`w-full ${attributes.class}`"
      optionLabel="label"
      optionValue="optionValue"
      v-model="selectedOption"
      v-bind="attributes?.inputProps"
    />
  </template>

  <template v-else-if="attributes.type === 'select_group'">
    <Select 
      @change="handleInput"
      :options="attributes.options" 
      optionLabel="label" 
      optionGroupLabel="label" 
      optionGroupChildren="items" 
      :placeholder="attributes.placeholder" 
      v-bind="attributes?.inputProps"
      optionValue="optionValue"
      :value="attributes?.value"
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
      mode="basic" 
      @select="onFileSelect" customUpload auto severity="secondary" class="p-button-outlined" 
    />
    <img v-if="src" :src="src" alt="Image" class="shadow-md w-full sm:w-64" style="filter: grayscale(100%)" />
  </template>

  <template v-else-if="attributes.type === 'calendar'">
      <DatePicker 
        v-bind="attributes?.inputProps"
        v-model="calendarDate"
        :value="attributes?.value"
        @dateSelect="handleInput" 
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
            v-model="inputGroupText"
            @valueChange="handleInput"
          />
      </InputGroup>
    </template>

  <template v-else>
    <input
      v-bind="attributes?.inputProps"
      :type="attributes.type || 'text'"
      :id="fieldName"
      :name="fieldName"
      :value="attributes?.value"
      :class="`border-gray-300 shadow-sm focus:ring-primary-default focus:border-primary-default p-2 w-full ${attributes.class}`"
      @input="handleInput"
    />
  </template>
</template>

<script setup>
import Select from 'primevue/select';
import RadioButton from 'primevue/radiobutton';
import FileUpload from 'primevue/fileupload';
import DatePicker from 'primevue/datepicker';
import { ref } from 'vue';
import { InputGroup, InputGroupAddon, InputText } from 'primevue';

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
const inputGroupText = ref(props.attributes.value);
const selectedOption = ref(props.attributes.value);

const emit = defineEmits(['updateFieldValue']);

const src = ref(null);
const calendarDate = ref();

function onFileSelect(event) {
const file = event.files[0];
const reader = new FileReader();
reader.onload = async (e) => {
  const sourceValue = e.target.result;
  emit('updateFieldValue', props.fieldName, sourceValue);
  src.value = sourceValue;
};
reader.readAsDataURL(file);
}

function handleInput(event) {

let value = '';

if(event.value?.optionValue !== undefined) {
  value = event.value?.optionValue;
}
else if (event?.value !== undefined) {
  value = event.value;
} 
else if (event?.target?.value !== undefined) {
  value = event.target.value;
} else {
  value = event ?? '';
}

emit('updateFieldValue', props.fieldName, value);
}

</script>