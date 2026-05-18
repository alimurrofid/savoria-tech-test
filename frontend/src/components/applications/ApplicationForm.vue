<script setup lang="ts">
import { reactive, watch } from 'vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import type { Application } from '@/types/api';

// ─── Props & Emits ────────────────────────────────────────────────────────────
const props = defineProps<{
  initialData?: Partial<Application> | null;
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', payload: Omit<Application, 'id' | 'created_at' | 'updated_at'>): void;
  (e: 'cancel'): void;
}>();

// ─── Form State ───────────────────────────────────────────────────────────────
const form = reactive({
  name: '',
  url: '',
  icon: '',
  category: '',
  description: '',
});

const errors = reactive<Record<string, string>>({});

// ─── Validation (PINDAH KE SINI) ──────────────────────────────────────────────
const clearErrors = () => {
  errors.name = '';
  errors.url = '';
  errors.category = '';
};

const validate = (): boolean => {
  clearErrors();
  let valid = true;

  if (!form.name.trim()) {
    errors.name = 'Application name is required.';
    valid = false;
  }
  if (!form.url.trim()) {
    errors.url = 'URL is required.';
    valid = false;
  } else {
    try {
      new URL(form.url);
    } catch {
      errors.url = 'Please enter a valid URL (e.g. https://app.internal).';
      valid = false;
    }
  }
  if (!form.category.trim()) {
    errors.category = 'Category is required.';
    valid = false;
  }

  return valid;
};

// Populate form when initialData changes (edit mode)
watch(
  () => props.initialData,
  (data) => {
    form.name = data?.name ?? '';
    form.url = data?.url ?? '';
    form.icon = data?.icon ?? '';
    form.category = data?.category ?? '';
    form.description = data?.description ?? '';
    clearErrors(); // Sekarang aman dipanggil karena fungsinya sudah dibuat di atas
  },
  { immediate: true },
);

// ─── Submit ───────────────────────────────────────────────────────────────────
const handleSubmit = () => {
  if (!validate()) return;
  emit('submit', {
    name: form.name.trim(),
    url: form.url.trim(),
    icon: form.icon.trim() || null,
    category: form.category.trim(),
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
        placeholder="e.g. ERP System"
        :class="['w-full', { 'p-invalid': errors.name }]"
      />
      <small v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        URL <span class="text-red-500">*</span>
      </label>
      <InputText
        v-model="form.url"
        placeholder="https://app.internal"
        :class="['w-full', { 'p-invalid': errors.url }]"
      />
      <small v-if="errors.url" class="text-red-500 text-xs">{{ errors.url }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        Category <span class="text-red-500">*</span>
      </label>
      <InputText
        v-model="form.category"
        placeholder="e.g. Enterprise, HR, Finance"
        :class="['w-full', { 'p-invalid': errors.category }]"
      />
      <small v-if="errors.category" class="text-red-500 text-xs">{{ errors.category }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">Icon</label>
      <div class="flex gap-3 items-center">
        <InputText
          v-model="form.icon"
          placeholder="e.g. pi pi-server"
          class="w-full"
        />
        <div
          v-if="form.icon"
          class="w-10 h-10 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600"
        >
          <i :class="form.icon"></i>
        </div>
      </div>
      <small class="text-slate-400 text-xs">Use a PrimeIcons class (e.g. <code>pi pi-server</code>).</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">Description</label>
      <Textarea
        v-model="form.description"
        placeholder="Brief description of the application..."
        rows="3"
        class="w-full resize-none"
      />
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <Button
        type="button"
        label="Cancel"
        severity="secondary"
        text
        @click="emit('cancel')"
      />
      <Button
        type="submit"
        :label="initialData?.id ? 'Save Changes' : 'Create Application'"
        icon="pi pi-check"
        :loading="loading"
      />
    </div>
  </form>
</template>