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
  departments: Pick<Department, 'id' | 'name'>[];
  roles: Pick<Role, 'id' | 'name'>[];
  loading?: boolean;
  disabled?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', payload: Record<string, unknown>): void;
  (e: 'cancel'): void;
}>();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  department_id: null as number | null,
  role_id: null as number | null,
  is_admin: false,
});
const errors = reactive<Record<string, string>>({});
const isEditMode = reactive({ value: false });

watch(
  () => props.initialData,
  (d) => {
    isEditMode.value = !!d?.id;
    form.name = d?.name ?? '';
    form.email = d?.email ?? '';
    form.password = '';
    form.password_confirmation = '';
    form.department_id = d?.department_id ?? null;
    form.role_id = d?.role_id ?? null;
    form.is_admin = d?.is_admin ?? false;
    Object.keys(errors).forEach((k) => (errors[k] = ''));
  },
  { immediate: true },
);

const handleSubmit = () => {
  Object.keys(errors).forEach((k) => (errors[k] = ''));
  let valid = true;
  if (!form.name.trim()) {
    errors.name = 'Name is required.';
    valid = false;
  }
  if (!form.email.trim()) {
    errors.email = 'Email is required.';
    valid = false;
  }
  if (!isEditMode.value && !form.password) {
    errors.password = 'Password is required for new users.';
    valid = false;
  }
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">
          Full Name <span class="text-red-500">*</span>
        </label>
        <InputText
          v-model="form.name"
          placeholder="e.g. John Doe"
          :class="['w-full', { 'p-invalid': errors.name }]"
          :disabled="disabled"
        />
        <small v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</small>
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">
          Email Address <span class="text-red-500">*</span>
        </label>
        <InputText
          v-model="form.email"
          type="email"
          placeholder="john@example.com"
          :class="['w-full', { 'p-invalid': errors.email }]"
          :disabled="disabled"
        />
        <small v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</small>
      </div>
    </div>

    <div v-if="!disabled" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">
          Password <span v-if="!initialData?.id" class="text-red-500">*</span>
        </label>
        <Password
          v-model="form.password"
          :feedback="false"
          toggleMask
          :placeholder="initialData?.id ? 'Leave blank to keep current' : 'Enter password'"
          :inputClass="['w-full', { 'p-invalid': errors.password }]"
          class="w-full"
        />
        <small v-if="errors.password" class="text-red-500 text-xs">{{ errors.password }}</small>
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">
          Confirm Password <span v-if="!initialData?.id" class="text-red-500">*</span>
        </label>
        <Password
          v-model="form.password_confirmation"
          :feedback="false"
          toggleMask
          :placeholder="initialData?.id ? 'Leave blank to keep current' : 'Confirm password'"
          :inputClass="['w-full', { 'p-invalid': errors.password_confirmation }]"
          class="w-full"
        />
        <small v-if="errors.password_confirmation" class="text-red-500 text-xs">{{
          errors.password_confirmation
        }}</small>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">Department</label>
        <Select
          v-model="form.department_id"
          :options="departments"
          optionLabel="name"
          optionValue="id"
          placeholder="Select Department"
          class="w-full"
          :disabled="form.is_admin || disabled"
        />
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-sm font-semibold text-slate-700">Role</label>
        <Select
          v-model="form.role_id"
          :options="roles"
          optionLabel="name"
          optionValue="id"
          placeholder="Select Role"
          class="w-full"
          :disabled="form.is_admin || disabled"
        />
      </div>
    </div>

    <div class="flex items-center gap-3 py-2">
      <ToggleSwitch v-model="form.is_admin" :disabled="disabled" />
      <div>
        <label class="text-sm font-semibold text-slate-800 block">System Administrator</label>
        <p class="text-xs text-slate-500">Grants full access to all applications and settings.</p>
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <Button
        v-if="disabled"
        type="button"
        label="Back"
        severity="secondary"
        text
        @click="emit('cancel')"
      />
      <template v-else>
        <Button type="button" label="Cancel" severity="secondary" text @click="emit('cancel')" />
        <Button
          type="submit"
          :label="initialData?.id ? 'Save Changes' : 'Create User'"
          icon="pi pi-check"
          :loading="loading"
        />
      </template>
    </div>
  </form>
</template>
