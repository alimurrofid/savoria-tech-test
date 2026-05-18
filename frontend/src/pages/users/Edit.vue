<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import UserForm from '@/components/users/UserForm.vue';
import api from '@/services/api';
import type { User, Department, Role, ApiResponse } from '@/types/api';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const loading = ref(false);
const saving = ref(false);
const user = ref<User | null>(null);
const departments = ref<Department[]>([]);
const roles = ref<Role[]>([]);

onMounted(async () => {
  loading.value = true;
  try {
    const [usersRes, deptsRes, rolesRes] = await Promise.all([
      api.get<ApiResponse<User[]>>('/users'),
      api.get<ApiResponse<Department[]>>('/departments'),
      api.get<ApiResponse<Role[]>>('/roles'),
    ]);
    departments.value = deptsRes.data.data;
    roles.value = rolesRes.data.data;
    
    const found = usersRes.data.data.find(u => u.id === Number(route.params.id));
    if (found) {
        user.value = found;
    } else {
        throw new Error('Not found');
    }
  } catch {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load user data.', life: 3000 });
    router.push({ name: 'users.index' });
  } finally {
    loading.value = false;
  }
});

const handleFormSubmit = async (payload: Record<string, unknown>) => {
  saving.value = true;
  try {
    await api.put(`/users/${route.params.id}`, payload);
    toast.add({ severity: 'success', summary: 'Updated', detail: 'User updated successfully.', life: 3000 });
    router.push({ name: 'users.index' });
  } catch (err: any) {
    toast.add({ severity: 'error', summary: 'Error', detail: err?.response?.data?.message ?? 'An error occurred.', life: 4000 });
  } finally {
    saving.value = false;
  }
};
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
      <Button icon="pi pi-arrow-left" text rounded @click="router.push({ name: 'users.index' })" />
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Edit User</h1>
        <p class="text-sm text-slate-400 mt-1">Update user details and access</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div v-if="loading" class="flex justify-center p-8">
        <i class="pi pi-spin pi-spinner text-2xl text-slate-400"></i>
      </div>
      <UserForm v-else :initial-data="user" :departments="departments" :roles="roles" :loading="saving" @submit="handleFormSubmit" @cancel="router.push({ name: 'users.index' })" />
    </div>
  </div>
</template>
