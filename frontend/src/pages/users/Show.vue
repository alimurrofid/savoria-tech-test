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
const user = ref<User | null>(null);
const departments = ref<Pick<Department, 'id' | 'name'>[]>([]);
const roles = ref<Pick<Role, 'id' | 'name'>[]>([]);

onMounted(async () => {
  loading.value = true;
  try {
    const [userRes, deptRes, roleRes] = await Promise.all([
      api.get<ApiResponse<User>>(`/users/${route.params.id}`),
      api.get<ApiResponse<Department[]>>('/departments'),
      api.get<ApiResponse<Role[]>>('/roles'),
    ]);
    user.value = userRes.data.data;
    departments.value = deptRes.data.data;
    roles.value = roleRes.data.data;
  } catch {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load user.', life: 3000 });
    router.push({ name: 'users.index' });
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
      <Button icon="pi pi-arrow-left" text rounded @click="router.push({ name: 'users.index' })" />
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">View User</h1>
        <p class="text-sm text-slate-400 mt-1">User details (Read-only)</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div v-if="loading" class="flex justify-center p-8">
        <i class="pi pi-spin pi-spinner text-2xl text-slate-400"></i>
      </div>
      <UserForm
        v-else
        :initial-data="user"
        :departments="departments"
        :roles="roles"
        disabled
        @cancel="router.push({ name: 'users.index' })"
      />
    </div>
  </div>
</template>
