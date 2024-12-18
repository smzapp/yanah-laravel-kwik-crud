<template>
  <div
    v-for="(field, fieldName) in fields"
    :key="fieldName"
    class="flex flex-col mb-4"
  >
    <template v-if="fieldName == 'wrapperIndex'">
      <template v-for="(wrapHolder, key) in field" :key="key">
        <div v-bind="wrapHolder.vBind">
          <template 
            v-for="(fieldSub, fieldWrapName) in wrapHolder.wrappedItems" 
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