<template>
  <Toast />
  <!-- <ConfirmDialog /> -->
  <Head :title="pageProps.pageTitle" />

  <div class="container mx-auto mt-3 pb-5">
    <div class="flex justify-end">
      <BreadCrumbsLocal 
        :breadCrumbs="pageProps.breadCrumbs"
        :currentLabel="'List'"
      />
    </div>
    
    <h3 class="text-2xl mb-2 text-slate-800 capitalize">
      {{ pageProps.pageText.plural }}
    </h3>
    
    <!-- Search Toolbar -->
    <div class="mb-3" v-if="localControls.showSearchBar">
      <div class="bg-white  mb-4 border rounded-lg py-3 px-3 flex justify-between align-items-center items-center">
        <div v-if="localControls.showSearch && pageProps.listview == 'TableListView'">
          <IconField>
            <InputIcon>
              <i class="pi pi-search" />
            </InputIcon>
            <InputText placeholder="Search" size="small" @change="handleSearch"/>
          </IconField>
        </div>
        <div v-if="pageProps.controls.showListSummary">
          <template v-if="localCrud.data">
            <div class="text-sm text-gray-600">
              Showing <span class="font-medium">{{ localCrud.from }}</span> to
              <span class="font-medium">{{ localCrud.to }}</span> of
              <span class="font-medium">{{ localCrud.total }}</span>&nbsp; 
              <span class="font-semibold">{{pageProps.pageText.plural}}.</span>&nbsp;
              <a :href="localCrud.path" class="text-primary-default">Reset</a>
            </div>
          </template>
          <template v-else>
            <div class="text-sm text-gray-600">
              Showing {{ Object.keys(localCrud).length }} total of&nbsp;
              <span class="font-bold">{{pageProps.pageText.plural}}</span>
            </div>
          </template>
        </div>
        <div> 
          <Link 
            :href="`${pageProps.activeRoute}/create`" 
            v-if="localControls.showAddButton"
          >
            <Button icon="pi pi-plus" :label="'Add ' + pageProps.pageText.singular" class="bg-primary-default capitalize" size="small" />
          </Link>
          <Button icon="pi pi-print" class="ml-3" title="Print" severity="secondary" text v-if="localControls.showPrintPdf"/>
        </div>
      </div>
    </div>
    
    <!-- prepend -->
    <component 
      :is="prependPageLocal" 
      @updateCrudList="updateCrudList" 
    />
    
    <div 
      v-if="Object.entries(localCrud).length == 0" 
      class="bg-white py-5 flex flex-col items-center gap-3"
    >
      <span class="pi pi-file" style="font-size: 2.0rem;"></span>
      <span>No records found.</span>
    </div>

    <template v-else>
      <template v-if="pageProps.listview == 'TableListView'">
        <TableListView 
          :localCrud="localCrud ?? null"
          :localControls="localControls"
          :isLoading="isLoading"
          :rowsPerPage="rowsPerPage"
          :headers="pageProps.headers"
          :activeRoute="pageProps.activeRoute"
          @deleteRecord="deleteRecord"
          @onPageChange="onPageChange"
        />
      </template>
      <template v-else>
        <ListItemView
          :localCrud="localCrud"
          :localControls="localControls"
          :isLoading="isLoading"
          :rowsPerPage="rowsPerPage"
          :fields="pageProps.fields"
          :activeRoute="pageProps.activeRoute"
          @deleteRecord="deleteRecord"
          @onPageChange="onPageChange"
        />
      </template>
    </template><!-- else of records -->

    <!-- append -->
    <component 
      :is="appendPageLocal" 
      @updateCrudList="updateCrudList" 
    />
  </div>
</template>


<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, defineAsyncComponent, markRaw, onMounted, reactive, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import ConfirmDialog from 'primevue/confirmdialog';
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { IconField, InputIcon, InputText } from 'primevue';
import TableListView from '../components/TableListView.vue';
import ListItemView from '../components/ListItemView.vue';
import BreadCrumbsLocal from '../components/BreadCrumbsLocal.vue';

// inits
const toast = useToast();
const confirm = useConfirm();
const { props: pageProps } = usePage();

const isLoading = ref(false);
const localControls = reactive({...pageProps.controls});
const rowsPerPage = ref(10);
const localCrud = ref(pageProps.crud);

const updateCrudList = (crud) => {
  localCrud.value = crud;
}

// Prepend and Append begin
const components = import.meta.glob('@/Components/**/*.vue');
const prependPageLocal = ref(null);
const appendPageLocal = ref(null);

onMounted(() => {
  const prependSource = pageProps?.pageWrapper?.prepend?.replace(/^@/, '/resources/js');
  const appendSource = pageProps?.pageWrapper?.append?.replace(/^@/, '/resources/js');

  if (prependSource && components[prependSource]) {
    components[prependSource]().then((mod) => {
      prependPageLocal.value = markRaw(mod.default);
    });
  }

  if (appendSource && components[appendSource]) {
    components[appendSource]().then((mod) => {
      appendPageLocal.value = markRaw(mod.default);
    });
  }
});


const fetchPage = async (page = 1, q = '') => {
  const url = `${pageProps.activeRoute}?page=${page}&q=${q}`;
  try {
    isLoading.value = true;
    const response = await axios.get(url);
    localCrud.value = response.data;
  } catch (error) {
    toast.add({
      severity: "warn",
      summary: "Opps",
      detail: "Something went wrong.",
      life: 3000,
    });
  } finally {
    isLoading.value = false;
  }
};

const onPageChange = (event) => {
  const newPage = event.page + 1;
  fetchPage(newPage);
};

const handleSearch = async(e) => {
  fetchPage(1, e.target.value);
}

const deleteRecord = async (id) => {
  confirm.require({
    message: "Do you want to delete this record?",
    header: "Delete Confirmation",
    icon: "pi pi-info-circle",
    accept: async () => {
      try {
        isLoading.value = true;
        await axios.delete(`${pageProps.activeRoute}/${id}`);
        toast.add({
          severity: "success",
          summary: "Success",
          detail: "Record deleted successfully!",
          life: 3000,
        });
        
        router.visit(`${pageProps.activeRoute}?page=${localCrud.current_page}`, {
          method: 'get',
          preserveState: true, 
          replace: true, 
          onSuccess: (response) => {
            localCrud.value = response.props.crud
          },
        });
      } catch (error) {
        toast.add({
          severity: "error",
          summary: "Error",
          detail: error.response?.data?.message || 'Unable to delete!',
          life: 3000,
        });
      } finally {
        isLoading.value = false;
      }
    },
  });
};

fetchPage(localCrud.current_page);

</script>

