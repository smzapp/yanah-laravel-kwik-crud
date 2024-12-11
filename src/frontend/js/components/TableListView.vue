<template>
    <div class="rounded-lg bg-white">
        <DataTable
          :value="localCrud.data ? localCrud.data : localCrud"
          :loading="isLoading"
          :paginator="true"
          :rows="rowsPerPage"
          :total-records="localCrud.total"
          :lazy="true"
          :first="(localCrud.current_page - 1) * rowsPerPage"
          :page="localCrud.current_page - 1"
          @page="$emit('onPageChange', $event)"
          stripedRows 
        >
          <Column
            v-for="field in fields"
            :key="field"
            :field="field"
            :header="field"
            :headerClass="'capitalize'"
            :bodyStyle="'padding:4px 15px;'"
          ></Column>
          <Column header="Actions" :bodyStyle="'padding:4px; width: 160px;'">
            <template #body="slotProps">
              <ActionButtions
                :record="slotProps.data"
                :localControls="localControls"
                :activeRoute="activeRoute"
                @deleteRecord="$emit('deleteRecord', $event)"
              />
            </template>
          </Column>
        </DataTable>
      </div>
</template>

<script setup>
import { Column, DataTable } from 'primevue';
import ActionButtions from './ActionButtons.vue';

const props = defineProps({
  localCrud: Object,
  localControls: Object,
  isLoading: Boolean,
  rowsPerPage: Number,
  fields: Array,
  activeRoute: String,
});

defineEmits(['deleteRecord', 'onPageChange']);

</script>