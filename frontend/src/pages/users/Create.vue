<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import UserForm from '@/components/users/UserForm.vue';
import api from '@/services/api';
import type { Department, Role, ApiResponse } from '@/types/api';

const router = useRouter();
const toast = useToast();
const loading = ref(false);
const submitting = ref(false);

const departments = ref<Department[]>([]);
const roles = ref<Role[]>([]);

onMounted(async () => {
  loading.value = true;
  try {
    const [deptsRes, rolesRes] = await Promise.all([
      api.get<ApiResponse<Department[]>>('/departments'),
      api.get<ApiResponse<Role[]>>('/roles'),
    ]);
    departments.value = deptsRes.data.data;
    roles.value = rolesRes.data.data;
  } catch {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load form data.', life: 3000 });
  } finally {
    loading.value = false;
  }
});

const handleFormSubmit = async (payload: Record<string, unknown>) => {
  submitting.value = true;
  try {
    await api.post('/users', payload);
    toast.add({ severity: 'success', summary: 'Created', detail: 'User created successfully.', life: 3000 });
    router.push({ name: 'users.index' });
  } catch (err: any) {
    toast.add({ severity: 'error', summary: 'Error', detail: err?.response?.data?.message ?? 'An error occurred.', life: 4000 });
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
      <Button icon="pi pi-arrow-left" text rounded @click="router.push({ name: 'users.index' })" />
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">New User</h1>
        <p class="text-sm text-slate-400 mt-1">Create a new system user</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div v-if="loading" class="flex justify-center p-8">
        <i class="pi pi-spin pi-spinner text-2xl text-slate-400"></i>
      </div>
      <UserForm v-else :departments="departments" :roles="roles" :loading="submitting" @submit="handleFormSubmit" @cancel="router.push({ name: 'users.index' })" />
    </div>
  </div>
</template>
