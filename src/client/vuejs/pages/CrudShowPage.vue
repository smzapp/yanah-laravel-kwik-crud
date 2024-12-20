<template>
  <Head :title="pageProps.pageText.title" />

  <div class="container mx-auto mt-6 pb-3 space-y-6">
    <div class="flex justify-between items-center">
      <div class="flex space-x-2 items-baseline">
        <Link :href="pageProps.activeRoute" class="text-sm text-primary-default"> &laquo; Back</Link>
        <h3 class="text-3xl font-bold capitalize text-[#555] tracking-tight">{{ pageProps.pageText.singular }}</h3>
      </div>
      <BreadCrumbsLocal
        :breadCrumbs="pageProps.breadCrumbs"
        :currentLabel="'Preview'"
      />
    </div>

    <!-- prepend -->
    <component :is="prependPageLocal" />


    <div v-if="pageProps.responseData.data" >
      <div 
        v-for="(item, index) in pageProps.responseData.data" 
        :key="index" 
        class="flex items-center even:bg-[#f8fafc] even:border-color-[#e2e8f0] border-b odd:bg-white"
      >
        <template v-if="!pageProps.responseData.except.includes(index)">
          <div class="w-1/4 px-4 py-3 font-semibold text-gray-600 capitalize">{{ index?.replace(/_/g, ' ') }}</div>
          <div class="w-3/4 px-4 py-3 text-gray-800">{{ item }}</div>
        </template>
      </div>

      <!-- append -->
      <component :is="appendPageLocal" />

      <div v-if="pageProps.controls.actions.edit" class="mt-4 capitalize text-sm text-primary-default">
          <Link :href="pageProps.activeRoute + `/${pageProps.activeId}/edit?${uuidParams}`">
            Edit 
          </Link>
        </div>
    </div>
    <p 
      v-else 
      class="text-center text-lg text-gray-500 bg-gray-100 py-6 rounded-lg"
    >
      No Record Found
    </p>

  </div>
</template>
    
<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import BreadCrumbsLocal from '../components/BreadCrumbsLocal.vue';
import { markRaw, onMounted, ref } from 'vue';
  
const { props: pageProps } = usePage();
 
const uuidParams = pageProps.uuid ? `&uuid=${pageProps.uuid}` : '';

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
</script>
    