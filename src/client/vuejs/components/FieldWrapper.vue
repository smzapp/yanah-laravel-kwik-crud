<template>
  <div
    v-for="(field, fieldName) in fields"
    :key="fieldName"
    class="flex flex-col mb-4"
  >
    <template v-if="fieldName == 'wrapperIndex'">
      <div v-bind="field.vBind">
        <template 
          v-for="(fieldSub, fieldWrapName) in field.wrappedItems" 
          :key="fieldWrapName"
          >
          <FormFields
            :field="fieldSub"
            :fieldName="fieldWrapName"
            @updateFieldValue="(name, value) => $emit('updateFieldValue', name, value)"
          />
        </template>
      </div>
    </template>

    <template v-else>
      <FormFields
        :field="field"
        :fieldName="fieldName"
        @updateFieldValue="(name, value) => $emit('updateFieldValue', name, value)"
      />
    </template>
  </div>
</template>

<script setup>
import FormFields from './FormFields.vue';

const props = defineProps({
  fields: {
    type: Object,
    required: true,
  },
});

defineEmits(['updateFieldValue']);
</script>