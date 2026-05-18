<script setup lang="ts">
import { reactive, watch } from 'vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';
import Button from 'primevue/button';
import type { User, Department, Role } from '@/types/api';

const props = defineProps<{
  initialData?: Partial<User> | null;
  departments: Department[];
  roles: Role[];
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', payload: Record<string, unknown>): void;
  (e: 'cancel'): void;
}>();

const form = reactive({
  name: '',
  email: '',
  password: '',
  department_id: null as number | null,
  role_id: null as number | null,
  is_admin: false,
});
const errors = reactive<Record<string, string>>({});
const isEditMode = reactive({ value: false });

watch(() => props.initialData, (d) => {
  isEditMode.value = !!d?.id;
  form.name = d?.name ?? '';
  form.email = d?.email ?? '';
  form.password = '';
  form.department_id = d?.department_id ?? null;
  form.role_id = d?.role_id ?? null;
  form.is_admin = d?.is_admin ?? false;
  Object.keys(errors).forEach(k => (errors[k] = ''));
}, { immediate: true });

const handleSubmit = () => {
  Object.keys(errors).forEach(k => (errors[k] = ''));
  let valid = true;
  if (!form.name.trim()) { errors.name = 'Name is required.'; valid = false; }
  if (!form.email.trim()) { errors.email = 'Email is required.'; valid = false; }
  if (!isEditMode.value && !form.password) { errors.password = 'Password is required for new users.'; valid = false; }
  if (!valid) return;

  const payload: Record<string, unknown> = {
    name: form.name.trim(),
    email: form.email.trim(),
    department_id: form.department_id,
    role_id: form.role_id,
    is_admin: form.is_admin,
  };
  if (form.password) payload.password = form.password;
  emit('submit', payload);
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4" novalidate>
    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">Name <span class="text-red-500">*</span></label>
      <InputText v-model="form.name" placeholder="Full name" :class="['w-full', { 'p-invalid': errors.name }]" />
      <small v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
      <InputText v-model="form.email" type="email" placeholder="user@example.com" :class="['w-full', { 'p-invalid': errors.email }]" />
      <small v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        Password <span v-if="!isEditMode.value" class="text-red-500">*</span>
        <span v-else class="text-slate-400 font-normal">(leave blank to keep current)</span>
      </label>
      <Password
        v-model="form.password"
        :placeholder="isEditMode.value ? 'New password (optional)' : 'Password'"
        :feedback="true"
        toggleMask
        inputClass="w-full"
        class="w-full"
        :class="{ 'p-invalid': errors.password }"
      />
      <small v-if="errors.password" class="text-red-500 text-xs">{{ errors.password }}</small>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">Department</label>
        <Select v-model="form.department_id" :options="departments" optionLabel="name" optionValue="id" placeholder="Select..." showClear class="w-full" />
      </div>
      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">Role</label>
        <Select v-model="form.role_id" :options="roles" optionLabel="name" optionValue="id" placeholder="Select..." showClear class="w-full" />
      </div>
    </div>

    <div class="flex items-center gap-3 pt-1">
      <ToggleSwitch v-model="form.is_admin" inputId="is_admin_toggle" />
      <label for="is_admin_toggle" class="text-sm font-semibold text-slate-700 cursor-pointer">Admin privileges</label>
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <Button type="button" label="Cancel" severity="secondary" text @click="emit('cancel')" />
      <Button type="submit" :label="isEditMode.value ? 'Save Changes' : 'Create User'" icon="pi pi-check" :loading="loading" />
    </div>
  </form>
</template>
