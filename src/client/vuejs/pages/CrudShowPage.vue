<template>
  <Head :title="pageProps.pageText.title" />

  <div class="container mx-auto mt-6 pb-3 space-y-6">
    
    <div class="flex justify-between items-center">
      <div class="flex space-x-2 items-baseline">
        <Link :href="pageProps.activeRoute"> 
            <Button 
                :label="`&laquo; Back to ${pageProps.pageText.plural}`" 
                severity="secondary"
                style="color:#555;"
                variant="link" 
                size="small" 
            />
        </Link>
        <h3 class="text-3xl font-bold capitalize text-[#555]">{{ pageProps.pageText.singular }}</h3>
        <span class="text-gray-500 text-sm uppercase tracking-wider">Preview {{pageProps.pageText.singular}}</span>
        <div v-if="pageProps.controls.actions.edit" class="mt-2 capitalize text-center text-sm text-primary-default">
          <Link :href="pageProps.activeRoute + `/${pageProps.activeId}/edit`">
            (Edit) 
          </Link>
        </div>
      </div>
      <BreadCrumbsLocal
        :breadCrumbs="pageProps.breadCrumbs"
        :currentLabel="'Preview'"
      />
    </div>

    <div 
      v-if="pageProps.responseData" 
      class="rounded-lg border shadow-sm  overflow-hidden"
    >
      <div 
        v-for="(item, index) in pageProps.responseData" 
        :key="index" 
        class="flex items-center even:bg-[#f8fafc] even:border-color-[#e2e8f0] border-b odd:bg-white"
      >
        <div class="w-1/4 px-4 py-3 font-medium text-gray-600 capitalize">{{ index }}</div>
        <div class="w-3/4 px-4 py-3 text-gray-800">{{ item }}</div>
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
import { Button } from 'primevue';
import { Link } from '@inertiajs/vue3';
import BreadCrumbsLocal from '../components/BreadCrumbsLocal.vue';
  
const { props: pageProps } = usePage();
  
</script>
    