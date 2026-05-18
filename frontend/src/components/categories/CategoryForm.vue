<script setup lang="ts">
import { reactive, watch } from 'vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import type { Category } from '@/types/api';

const props = defineProps<{
  initialData?: Partial<Category> | null;
  loading?: boolean;
  disabled?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', payload: Omit<Category, 'id' | 'created_at' | 'updated_at'>): void;
  (e: 'cancel'): void;
}>();

const form = reactive({
  name: '',
  description: '',
});

const errors = reactive<Record<string, string>>({});

const clearErrors = () => {
  errors.name = '';
};

const validate = (): boolean => {
  clearErrors();
  let valid = true;

  if (!form.name.trim()) {
    errors.name = 'Category name is required.';
    valid = false;
  }

  return valid;
};

watch(
  () => props.initialData,
  (data) => {
    form.name = data?.name ?? '';
    form.description = data?.description ?? '';
    clearErrors();
  },
  { immediate: true },
);

const handleSubmit = () => {
  if (!validate()) return;
  emit('submit', {
    name: form.name.trim(),
    description: form.description.trim() || null,
  });
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        Name <span class="text-red-500">*</span>
      </label>
      <InputText
        v-model="form.name"
        placeholder="e.g. Finance"
        :class="['w-full', { 'p-invalid': errors.name }]"
        :disabled="disabled"
      />
      <small v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">Description</label>
      <Textarea
        v-model="form.description"
        placeholder="Brief description of the category..."
        rows="3"
        class="w-full resize-none"
        :disabled="disabled"
      />
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
          :label="initialData?.id ? 'Save Changes' : 'Create Category'"
          icon="pi pi-check"
          :loading="loading"
        />
      </template>
    </div>
  </form>
</template>
