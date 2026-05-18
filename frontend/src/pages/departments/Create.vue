<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import DepartmentForm from '@/components/departments/DepartmentForm.vue';
import api from '@/services/api';

defineOptions({
  name: 'DepartmentsCreate',
});

const router = useRouter();
const toast = useToast();
const loading = ref(false);

const handleFormSubmit = async (payload: { name: string }) => {
  loading.value = true;
  try {
    await api.post('/departments', payload);
    toast.add({
      severity: 'success',
      summary: 'Created',
      detail: 'Department created successfully.',
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
        @click="router.push({ name: 'departments.index' })"
      />
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">New Department</h1>
        <p class="text-sm text-slate-400 mt-1">Create a new organizational department</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <DepartmentForm
        :loading="loading"
        @submit="handleFormSubmit"
        @cancel="router.push({ name: 'departments.index' })"
      />
    </div>
  </div>
</template>
