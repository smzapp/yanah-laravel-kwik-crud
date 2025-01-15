<template>
  <div
    v-for="(field, fieldName) in fields"
    :key="fieldName"
    class="flex flex-col mb-4"
  >
    <template v-if="field.wrappedItems">
      <div v-if="field?.headings" :class="field?.headings?.length ? 'flex gap-10' : ''">
        <div 
          :style="field.vBind?.style ? field.vBind.style: ''"
          :class="responsiveGridClasses(field.vBind.columns) + ` ${field.vBind.class}`"
        >
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

        <div v-if="field?.headings?.length" :style="field.headings?.style">
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

const responsiveGridClasses = (columns) => {
  const xsCols = columns?.xs ?? 1;
  const smCols = columns?.sm ?? xsCols;
  const mdCols = columns?.md ?? smCols;
  const lgCols = columns?.lg ?? mdCols;

  return [
    `grid`,
    `w-full`,
    `xs:grid-cols-${xsCols}`,
    `sm:grid-cols-${smCols}`,
    `md:grid-cols-${mdCols}`,
    `lg:grid-cols-${lgCols}`,
  ].join(' ');
};


defineEmits(['updateFieldValue']);
</script>