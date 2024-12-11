<template>
    <Breadcrumb :model="breadCrumbsLocal" class="text-sm" :style="'background:transparent; color: #777;'">
      <template #item="{ item }">
        <Link v-if="item.href" class="cursor-pointer" :href="item.href ?? '#'">
          <span :class="item.icon"></span>{{  item.label }} 
        </Link>
        <span v-else>{{  item.label }}</span>
      </template>
    </Breadcrumb>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import { Breadcrumb } from 'primevue';
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    breadCrumbs: Array,
    currentLabel: String || null,
  });
  
  const breadCrumbsLocal = ref([...props.breadCrumbs]);
  watch(
    () => props.breadCrumbs,
    (newVal) => {
      if(props.currentLabel) {
        breadCrumbsLocal.value = [...newVal, {label: props.currentLabel}];
      }
    },
    { immediate: true}
  );
  
  const home = ref({
      icon: 'pi pi-home'
  });
  </script>