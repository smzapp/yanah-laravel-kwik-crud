<template>
  <Head :title="pageProps.pageText.title" />

  <div class="container mx-auto mt-3 pb-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-3xl font-semibold text-[#555] capitalize">
        <i class="pi pi-pen-to-square" style="font-size: 25px;"></i>&nbsp;
        {{ pageProps.pageText.singular }}
      </h2>
      <BreadCrumbsLocal 
        :breadCrumbs="pageProps.breadCrumbs"
        :currentLabel="pageProps.activeId ? 'Edit' : 'Create'"
      />
    </div>
 
    <component :is="prependPageLocal" />

    <Form v-slot="$form" @submit="submitForm" enctype="multipart/form-data">
      <Tabs :value="0" v-if="hasTabs" :class="'bg-primary-default'">
        <TabList>
          <template v-for="(group, index) in groupedForm" :key="index">
            <Tab v-if="group.details.tab" :value="index">
              {{ group.details.label }}
            </Tab>
          </template>
        </TabList>
        <TabPanels>
          <TabPanel
            v-for="(group, index) in groupedForm"
            :key="index"
            :header="group.details.label"
            :value="index"
          >
            <div class="flex min-h-80 space-x-10 justify-between">
              <div class="w-8/12">
                <!-- <FormFields 
                  :fields="group.fields" 
                  @updateFieldValue="updateFormData"
                /> -->
                <FieldWrapper
                  :fields="group.fields" 
                  @updateFieldValue="updateFormData"
                />
              </div>
              <div class="w-4/12">
                <div class="flex items-start flex-col mt-5 h-full">
                  <h2 class="text-2xl mb-5">{{ group.details.title }}</h2>
                  <p>{{ group.details.description }}</p>
                </div>
              </div>
            </div>
          </TabPanel>
        </TabPanels>
      </Tabs>

      <!-- Non-tab Groups -->
      <div v-else>
        <div class="bg-white p-4 rounded-lg" v-for="(group, index) in groupedForm" :key="index">
          <h3 class="text-lg font-bold">{{ group.details.label }}</h3>
          <!-- <FormFields 
            :fields="group.fields" 
            @updateFieldValue="updateFormData"
          /> -->
          <FieldWrapper
              :fields="group.fields" 
              @updateFieldValue="updateFormData"
            />
        </div>
      </div>

      <Button 
        type="submit" 
        :label="pageProps.button?.text ?? 'Submit'" 
        icon="pi pi-send"
        class="mt-5"
        :loading="loading"
      />
    </Form>

    <component :is="appendPageLocal" />
  </div>
</template>

<script setup>
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed,  defineAsyncComponent,  reactive,  ref, watch } from "vue";
import { Tab, TabList, TabPanel, TabPanels, Tabs, useToast, Button, Message } from "primevue";
import FormFields from "../components/FormFields.vue";
import { Form } from '@primevue/forms';
import axios from 'axios';
import BreadCrumbsLocal from '../components/BreadCrumbsLocal.vue';
import { router } from '@inertiajs/vue3';
import FieldWrapper from "../components/FieldWrapper.vue";


const toast = useToast();
const { props: pageProps } = usePage();

const formData = ref({}); 
const loading = ref(false);

const hasTabs = computed(() =>
  pageProps.formgroup.some((group) => group.details.tab)
);

// Append or prepend file
const createAsyncComponent = (path) => 
  path ? computed(() => defineAsyncComponent({ loader: () => import(`${path}`) })) : null;

const prependPageLocal = createAsyncComponent(pageProps?.pageWrapper?.prepend);
const appendPageLocal = createAsyncComponent(pageProps?.pageWrapper?.append);

const groupedForm = computed(() => {
  return pageProps.formgroup.map((group) => ({
    ...group,
    fields: group.fields || [],
    details: group.details || {},
  }));
});

const updateFormData = (name, value) => {
  console.log(name, value);
  
  formData.value[name] = value;
};
 
// Autoload data when value is available in the prepareEditForm
const initializeFormData = () => {
  formData.value = {}; 
  pageProps.formgroup.forEach(group => {
    Object.entries(group.fields).forEach(([key, field]) => {
      formData.value[key] = field.value || '';
    });
  });
};

watch(() => pageProps.formgroup, initializeFormData, { immediate: true });

// Form submission
const submitForm = async () => {
  const plainFormData = JSON.parse(JSON.stringify(formData.value));
  
  try {
    loading.value = true;
    let message = "New record added successfully!";

    if(pageProps.activeId !== undefined) {
      await axios.put(`${pageProps.activeRoute}/${pageProps.activeId}`, plainFormData);
      message = 'Record updated successfully!';
    } else {
      await axios.post(pageProps.activeRoute, plainFormData);
    }

    toast.add({
      severity: "success",
      summary: "Success",
      detail: message,
      life: 5000,
    });

    if(pageProps.redirectTo) {
      setTimeout(() => {
        router.visit(pageProps.redirectTo);
      }, 1500);
    }
  } catch(e) {
    console.log(e);
    
    toast.add({
      severity: "error",
      summary: "Error",
      detail: e.response?.data?.message || 'Something went wrong!',
      life: 5000,
    });
  } finally {
    loading.value = false;
  }
};
</script>
