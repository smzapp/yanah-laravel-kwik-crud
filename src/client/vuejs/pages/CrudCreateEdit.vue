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
            <FieldWrapper
              :fields="group.fields" 
              @updateFieldValue="updateFormData"
            />
          </TabPanel>
        </TabPanels>
      </Tabs>

      <!-- Non-tab Groups -->
      <div v-else>
        <div class="bg-white p-4 rounded-lg" v-for="(group, index) in groupedForm" :key="index">
          <h3 class="text-lg font-bold">{{ group.details.label }}</h3>
          <FieldWrapper
              :fields="group.fields" 
              @updateFieldValue="updateFormData"
            />
        </div>
      </div>

      <div class="flex justify-end gap-3 items-center mt-8 w-80">
        <div class="w-72">
          <Link :href="pageProps.activeRoute">
            <Button :label="'Cancel'" class="bg-secondary" variant="outlined" fluid />
          </Link>
        </div>
        <Button 
          type="submit" 
          :label="pageProps.button?.text ?? 'Submit'" 
          icon="pi pi-send"
          :class="'px-10'"
          :loading="loading"
          fluid
        />
      </div>
    </Form>

    <component :is="appendPageLocal" />
  </div>
</template>

<script setup>
import { Head, useForm, usePage, Link } from "@inertiajs/vue3";
import { computed, markRaw, onMounted, ref, watch } from "vue";
import { Tab, TabList, TabPanel, TabPanels, Tabs, useToast, Button } from "primevue";
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

const groupedForm = computed(() => {
  return pageProps.formgroup.map((group) => ({
    ...group,
    fields: group.fields || [],
    details: group.details || {},
  }));
});

const updateFormData = (name, value) => {
  formData.value[name] = value;
};
 
// Autoload data when value is available in the prepareEditForm
const initializeFormData = () => {
  formData.value = {}; 
  pageProps.formgroup.forEach(group => {
    const sortedFields = Object.entries(group.fields)
      .sort(([keyA, fieldA], [keyB, fieldB]) => fieldA.tabIndex - fieldB.tabIndex);

      console.log(sortedFields);
      

    Object.entries(sortedFields).forEach(([key, field]) => {
      const wrapped = typeof field[1] !== undefined ? field[1] : null;

      if(wrapped.wrappedItems) {
        Object.entries(wrapped.wrappedItems).forEach(([wrapKey, props]) => {
          if(props.is_boolean) {
            formData.value[wrapKey] = props.value ?? false;
          } else {
            formData.value[wrapKey] = props.value || '';
          }
        });
      } else {
        if(field.is_boolean) {
          formData.value[key] = field.value ?? false;
        } else {
          formData.value[key] = field.value || '';
        }
      }
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
