<script setup lang="ts">
import { reactive, watch } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import type { Department } from '@/types/api';

const props = defineProps<{
  initialData?: Partial<Department> | null;
  loading?: boolean;
  disabled?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', payload: { name: string }): void;
  (e: 'cancel'): void;
}>();

const form = reactive({ name: '' });
const errors = reactive<Record<string, string>>({});

watch(
  () => props.initialData,
  (d) => {
    form.name = d?.name ?? '';
    errors.name = '';
  },
  { immediate: true },
);

const handleSubmit = () => {
  errors.name = '';
  if (!form.name.trim()) {
    errors.name = 'Department name is required.';
    return;
  }
  emit('submit', { name: form.name.trim() });
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        Department Name <span class="text-red-500">*</span>
      </label>
      <InputText
        v-model="form.name"
        placeholder="e.g. Human Resources"
        :class="['w-full', { 'p-invalid': errors.name }]"
        :disabled="disabled"
      />
      <small v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</small>
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
          :label="initialData?.id ? 'Save Changes' : 'Create Department'"
          icon="pi pi-check"
          :loading="loading"
        />
      </template>
    </div>
  </form>
</template>
