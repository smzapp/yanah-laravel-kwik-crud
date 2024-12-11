<template>
  <div class="bg-white">
    <div 
      v-for="item in (localCrud.data ? localCrud.data : localCrud)" 
      :key="item.id"
      class="flex justify-content-between border-b py-3 even:bg-[#f8fafc] odd:border-[#e2e8f0]"
      >
      <div class="pl-4 w-full">
        <div class="font-bold text-lg capitalize text-[#333]">
          {{ item.primary ?? 'N/A'}}
        </div>
        <div class="text-slate-500">
          {{ item.secondary ?? 'N/A'}}
        </div>
      </div>
      <div class="w-[160px] flex justify-content-between">
        <ActionButtions
          :record="item"
          :localControls="localControls"
          :activeRoute="activeRoute"
          @deleteRecord="$emit('deleteRecord', $event)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import ActionButtions from '@kwik/components/ActionButtons.vue';

const props = defineProps({
  localCrud: Object,
  localControls: Object,
  isLoading: Boolean,
  rowsPerPage: Number,
  fields: Array,
  activeRoute: String,
});

defineEmits(['deleteRecord']);

</script>