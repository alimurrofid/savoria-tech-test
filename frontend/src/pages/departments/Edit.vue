<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import DepartmentForm from '@/components/departments/DepartmentForm.vue';
import api from '@/services/api';
import type { Department, ApiResponse } from '@/types/api';

defineOptions({
  name: 'DepartmentsEdit',
});

const route = useRoute();
const router = useRouter();
const toast = useToast();

const loading = ref(false);
const saving = ref(false);
const department = ref<Department | null>(null);

onMounted(async () => {
  loading.value = true;
  try {
    // Backend omits the show route; fetch list and resolve by id.
    const { data } = await api.get<ApiResponse<Department[]>>(`/departments`);
    const found = data.data.find((d) => d.id === Number(route.params.id));
    if (found) {
      department.value = found;
    } else {
      throw new Error('Not found');
    }
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load department.',
      life: 3000,
    });
    router.push({ name: 'departments.index' });
  } finally {
    loading.value = false;
  }
});

const handleFormSubmit = async (payload: { name: string }) => {
  saving.value = true;
  try {
    await api.put(`/departments/${route.params.id}`, payload);
    toast.add({
      severity: 'success',
      summary: 'Updated',
      detail: 'Department updated successfully.',
      life: 3000,
    });
    router.push({ name: 'departments.index' });
  } catch (err: unknown) {
    const message =
      typeof err === 'object' && err !== null && 'response' in err
        ? (err as { response?: { data?: { message?: string } } }).response?.data?.message
        : undefined;
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: message ?? 'An error occurred.',
      life: 4000,
    });
  } finally {
    saving.value = false;
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
        @click="router.push({ name: 'departments.index' })"
      />
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Department</h1>
        <p class="text-sm text-slate-400 mt-1">Update department details</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div v-if="loading" class="flex justify-center p-8">
        <i class="pi pi-spin pi-spinner text-2xl text-slate-400"></i>
      </div>
      <DepartmentForm
        v-else
        :initial-data="department"
        :loading="saving"
        @submit="handleFormSubmit"
        @cancel="router.push({ name: 'departments.index' })"
      />
    </div>
  </div>
</template>
