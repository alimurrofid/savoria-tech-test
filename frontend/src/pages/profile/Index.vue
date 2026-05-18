<script setup lang="ts">
import { ref, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import { useAuthStore } from '@/stores/auth';
import api from '@/services/api';
import type { ApiResponse, User } from '@/types/api';

defineOptions({
  name: 'ProfileIndex',
});

const toast = useToast();
const authStore = useAuthStore();

const loading = ref(false);
const saving = ref(false);

const form = ref({ name: '', email: '', password: '', password_confirmation: '' });
const errors = ref<Record<string, string>>({});

onMounted(async () => {
  loading.value = true;
  try {
    const { data } = await api.get<ApiResponse<User>>('/profile');
    form.value.name = data.data.name;
    form.value.email = data.data.email;
  } finally {
    loading.value = false;
  }
});

const handleSave = async () => {
  errors.value = {};
  if (form.value.password && form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match.';
    return;
  }
  saving.value = true;
  try {
    const payload: Record<string, string> = {
      name: form.value.name,
      email: form.value.email,
    };
    if (form.value.password) {
      payload.password = form.value.password;
      payload.password_confirmation = form.value.password_confirmation;
    }

    const { data } = await api.put<ApiResponse<User>>('/profile', payload);

    authStore.user = data.data;
    localStorage.setItem('user', JSON.stringify(data.data));

    form.value.password = '';
    form.value.password_confirmation = '';
    toast.add({
      severity: 'success',
      summary: 'Saved',
      detail: 'Profile updated successfully.',
      life: 3000,
    });
  } catch (err: unknown) {
    const response =
      typeof err === 'object' && err !== null && 'response' in err
        ? (
            err as {
              response?: {
                data?: { errors?: Record<string, string[] | string>; message?: string };
              };
            }
          ).response
        : undefined;
    const backendErrors = response?.data?.errors ?? {};
    Object.keys(backendErrors).forEach((key) => {
      const value = backendErrors[key];
      if (!value) {
        return;
      }
      errors.value[key] = Array.isArray(value) ? (value[0] ?? '') : value;
    });
    if (!Object.keys(errors.value).length) {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: response?.data?.message ?? 'Failed to save.',
        life: 3000,
      });
    }
  } finally {
    saving.value = false;
  }
};
</script>

<template>
  <div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-tight">My Profile</h1>
      <p class="text-sm text-slate-400 mt-1">Update your personal information and password.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 space-y-6">
      <!-- Avatar row -->
      <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
        <div
          class="w-16 h-16 rounded-2xl bg-primary-100 flex items-center justify-center text-primary-600 text-2xl font-bold"
        >
          {{ form.name?.charAt(0)?.toUpperCase() }}
        </div>
        <div>
          <p class="font-bold text-slate-800 text-lg">{{ form.name }}</p>
          <p class="text-sm text-slate-400">
            {{ authStore.user?.is_admin ? 'Administrator' : 'Regular User' }}
          </p>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSave" class="space-y-5" novalidate>
        <!-- Name -->
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700">Full Name</label>
          <InputText
            v-model="form.name"
            placeholder="Your name"
            :class="['w-full', { 'p-invalid': errors.name }]"
          />
          <small v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</small>
        </div>

        <!-- Email -->
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700">Email Address</label>
          <InputText
            v-model="form.email"
            type="email"
            placeholder="your@email.com"
            :class="['w-full', { 'p-invalid': errors.email }]"
          />
          <small v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</small>
        </div>

        <div class="border-t border-slate-100 pt-5">
          <p class="text-sm font-semibold text-slate-700 mb-4">
            Change Password
            <span class="font-normal text-slate-400">(leave blank to keep current)</span>
          </p>

          <!-- New Password -->
          <div class="flex flex-col gap-1.5 mb-4">
            <label class="text-sm font-semibold text-slate-700">New Password</label>
            <Password
              v-model="form.password"
              placeholder="New password"
              :feedback="true"
              toggleMask
              inputClass="w-full"
              class="w-full"
              :class="{ 'p-invalid': errors.password }"
            />
            <small v-if="errors.password" class="text-red-500 text-xs">{{ errors.password }}</small>
          </div>

          <!-- Confirm Password -->
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-semibold text-slate-700">Confirm New Password</label>
            <Password
              v-model="form.password_confirmation"
              placeholder="Confirm new password"
              :feedback="false"
              toggleMask
              inputClass="w-full"
              class="w-full"
              :class="{ 'p-invalid': errors.password_confirmation }"
            />
            <small v-if="errors.password_confirmation" class="text-red-500 text-xs">{{ errors.password_confirmation }}</small>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <Button type="submit" label="Save Changes" icon="pi pi-check" :loading="saving" />
        </div>
      </form>
    </div>
  </div>
</template>
