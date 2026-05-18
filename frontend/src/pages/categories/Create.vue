<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import CategoryForm from '@/components/categories/CategoryForm.vue';
import api from '@/services/api';

const router = useRouter();
const toast = useToast();
const loading = ref(false);

const handleFormSubmit = async (payload: Record<string, unknown>) => {
  loading.value = true;
  try {
    await api.post('/categories', payload);
    toast.add({
      severity: 'success',
      summary: 'Created',
      detail: 'Category created successfully.',
      life: 3000,
    });
    router.push({ name: 'categories.index' });
  } catch (err: any) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: err?.response?.data?.message ?? 'An error occurred.',
      life: 4000,
    });
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
      <Button
        icon="pi pi-arrow-left"
        text
        rounded
        @click="router.push({ name: 'categories.index' })"
      />
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">New Category</h1>
        <p class="text-sm text-slate-400 mt-1">Create a new application category</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <CategoryForm
        :loading="loading"
        @submit="handleFormSubmit"
        @cancel="router.push({ name: 'categories.index' })"
      />
    </div>
  </div>
</template>
