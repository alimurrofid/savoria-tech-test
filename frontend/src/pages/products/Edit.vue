<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '@/services/api';
import AppButton from '@/components/common/AppButton.vue';
import AppFormInput from '@/components/common/AppFormInput.vue';
import { useToast } from 'primevue/usetoast';

const router = useRouter();
const route = useRoute();
const toast = useToast();

const loading = ref(false);
const formLoading = ref(false);
const form = ref({ id: null, name: '', sku: '', price: 0, stock: 0 });

const fetchProduct = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/products/${route.params.id}`);
    form.value = response.data.data;
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

const updateProduct = async () => {
  formLoading.value = true;
  try {
    await api.put(`/products/${route.params.id}`, form.value);
    toast.add({
      severity: 'success',
      summary: 'Updated',
      detail: 'Product updated successfully',
      life: 3000,
    });
    router.push({ name: 'products.index' });
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to update product',
      life: 3000,
    });
  } finally {
    formLoading.value = false;
  }
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
        <h1 class="text-2xl font-bold text-slate-800">Edit Product</h1>
        <p class="text-slate-500 text-sm mt-1">Update your product details here.</p>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center p-10">
      <i class="pi pi-spin pi-spinner text-4xl text-slate-400"></i>
    </div>

    <div
      v-else
      class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100"
    >
      <div class="space-y-5">
        <AppFormInput
          id="name"
          label="Product Name"
          placeholder="e.g. Mechanical Keyboard"
          v-model="form.name"
          required
        />
        <AppFormInput
          id="sku"
          label="SKU / Barcode"
          placeholder="e.g. KB-MECH-01"
          v-model="form.sku"
          required
        />
        <div class="grid grid-cols-2 gap-4">
          <AppFormInput
            id="price"
            label="Price (IDR)"
            type="number"
            v-model="form.price"
            required
          />
          <AppFormInput
            id="stock"
            label="Current Stock"
            type="number"
            v-model="form.stock"
            required
          />
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-slate-100">
        <button
          @click="router.push({ name: 'products.index' })"
          class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors"
        >
          Cancel
        </button>
        <AppButton label="Update Product" :loading="formLoading" @click="updateProduct" />
      </div>
    </div>
  </div>
</template>
