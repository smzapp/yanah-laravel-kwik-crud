<template>
  <div
    v-for="(field, fieldName) in fields"
    :key="fieldName"
    class="flex flex-col mb-4"
  >
    <template v-if="field.wrappedItems">
      <div v-if="field?.headings" :class="Object.entries(field.headings).length > 0 ? 'flex gap-7' : ''">
        <div 
          :style="field.vBind?.style ? field.vBind.style: ''"
          :class="field.vBind?.class"
        >
          <div 
            v-for="(fieldSub, fieldWrapName) in field.wrappedItems" 
            :key="fieldWrapName"
            class="mb-4"
            >
              <FormFields
                :field="fieldSub"
                :fieldName="fieldWrapName"
                @updateFieldValue="(name, value) => $emit('updateFieldValue', name, value)"
              />
          </div>
        </div>

        <div 
          v-if="Object.entries(field.headings).length > 0" 
          :class="field.headings?.headingClass"
          :style="field.headings?.style"
          >
          <div class="flex items-start flex-col mt-5 h-full">
            <h2 class="text-2xl mb-5">{{ field.headings?.heading }}</h2>
            <p>{{ field.headings?.paragraph }}</p>
          </div>
        </div>
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