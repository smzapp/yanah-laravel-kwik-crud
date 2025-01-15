<template>
    <Link :href="`${activeRoute}/${props.record.id}?${uuidParam}`">
      <Button
        v-if="localControls.actions.preview"
        size="large"
        icon="pi pi-eye"
        class="p-button-rounded p-button-info p-button-text px-5"
        title="Preview Record"
      />
    </Link>
    <Link :href="`${activeRoute}/${props.record.id}/edit?${uuidParam}`">
      <Button
        v-if="localControls.actions.edit"
        size="large"
        icon="pi pi-pencil"
        class="p-button-rounded p-button-warning p-button-text"
        title="Edit"
      />
    </Link>
    <Button
      v-if="localControls.actions.delete"
      size="large"
      icon="pi pi-trash"
      class="p-button-rounded p-button-danger p-button-text"
      title="Delete"
      @click="$emit('deleteRecord', props.record.id)"
    />
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import { Button } from 'primevue';
  import { computed } from 'vue';
  
  const props = defineProps({
    localControls: Object,
    record: Object,
    activeRoute: String
  })
  
  defineEmits(['deleteRecord']);
  
  const uuidParam = computed(() => {
    return props.record.uuid ? `&uuid=${props.record.uuid}` : '';
  })
  
  </script>