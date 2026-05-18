<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import CategoryForm from '@/components/categories/CategoryForm.vue';
import api from '@/services/api';
import type { Category, ApiResponse } from '@/types/api';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const loading = ref(false);
const category = ref<Category | null>(null);

onMounted(async () => {
  loading.value = true;
  try {
    const { data } = await api.get<ApiResponse<Category>>(`/categories/${route.params.id}`);
    category.value = data.data;
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load category.',
      life: 3000,
    });
    router.push({ name: 'categories.index' });
  } finally {
    loading.value = false;
  }
});
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
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">View Category</h1>
        <p class="text-sm text-slate-400 mt-1">Category details (Read-only)</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div v-if="loading" class="flex justify-center p-8">
        <i class="pi pi-spin pi-spinner text-2xl text-slate-400"></i>
      </div>
      <CategoryForm
        v-else
        :initial-data="category"
        disabled
        @cancel="router.push({ name: 'categories.index' })"
      />
    </div>
  </div>
</template>
