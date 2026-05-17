<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '@/services/api';
import AppButton from '@/components/common/AppButton.vue';
import { useToast } from 'primevue/usetoast';

const router = useRouter();
const route = useRoute();
const toast = useToast();

const loading = ref(false);
const product = ref({ name: '', sku: '', price: 0, stock: 0 });

const fetchProduct = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/products/${route.params.id}`);
    product.value = response.data.data;
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load product details',
      life: 3000,
    });
    router.push({ name: 'products.index' });
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProduct();
});

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(value);
};
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
      <AppButton
        icon="pi pi-arrow-left"
        severity="secondary"
        variant="text"
        rounded
        @click="router.push({ name: 'products.index' })"
      />
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Product Details</h1>
        <p class="text-slate-500 text-sm mt-1">Viewing information for product</p>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center p-10">
      <i class="pi pi-spin pi-spinner text-4xl text-slate-400"></i>
    </div>

    <div
      v-else
      class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100 flex flex-col gap-6"
    >
      <div class="flex flex-col">
        <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">PRODUCT NAME</span>
        <span class="font-medium text-lg text-slate-700">{{ product.name }}</span>
      </div>

      <div class="flex flex-col">
        <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">SKU / BARCODE</span>
        <span
          class="font-mono text-sm bg-slate-50 px-3 py-1.5 rounded border border-slate-100 self-start text-slate-600"
        >
          {{ product.sku }}
        </span>
      </div>

      <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-6">
        <div class="flex flex-col">
          <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">PRICE</span>
          <span class="text-slate-800 font-semibold text-lg">{{
            formatCurrency(product.price)
          }}</span>
        </div>
        <div class="flex flex-col">
          <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">CURRENT STOCK</span>
          <span class="text-slate-700 font-medium text-lg"> {{ product.stock }} Units </span>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-slate-100">
        <AppButton
          label="Edit Product"
          icon="pi pi-pencil"
          @click="router.push({ name: 'products.edit', params: { id: route.params.id } })"
        />
      </div>
    </div>
  </div>
</template>
