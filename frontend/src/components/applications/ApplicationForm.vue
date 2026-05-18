<script setup lang="ts">
import { reactive, watch, ref, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Select from 'primevue/select';
import type { Application, Category, ApiResponse } from '@/types/api';
import api from '@/services/api';

const props = defineProps<{
  initialData?: Partial<Application> | null;
  loading?: boolean;
  disabled?: boolean;
}>();

type ApplicationPayload = Omit<Application, 'id' | 'created_at' | 'updated_at' | 'category'>;

const emit = defineEmits<{
  (e: 'submit', payload: ApplicationPayload): void;
  (e: 'cancel'): void;
}>();

const form = reactive({
  name: '',
  url: '',
  icon: '',
  category_id: null as number | null,
  description: '',
});

const errors = reactive<Record<string, string>>({});
const categories = ref<Category[]>([]);
const loadingCategories = ref(false);

const primeIcons = [
  { name: 'Server', icon: 'pi pi-server' },
  { name: 'Users', icon: 'pi pi-users' },
  { name: 'Wallet', icon: 'pi pi-wallet' },
  { name: 'Headphones', icon: 'pi pi-headphones' },
  { name: 'Calendar', icon: 'pi pi-calendar' },
  { name: 'File', icon: 'pi pi-file' },
  { name: 'Box', icon: 'pi pi-box' },
  { name: 'Briefcase', icon: 'pi pi-briefcase' },
  { name: 'Chart Bar', icon: 'pi pi-chart-bar' },
  { name: 'Desktop', icon: 'pi pi-desktop' },
  { name: 'Cog', icon: 'pi pi-cog' },
  { name: 'Home', icon: 'pi pi-home' },
  { name: 'Globe', icon: 'pi pi-globe' },
  { name: 'Building', icon: 'pi pi-building' },
];

const fetchCategories = async () => {
  loadingCategories.value = true;
  try {
    const { data } = await api.get<ApiResponse<Category[]>>('/categories', {
      params: { search: '', limit: 'all' },
    });
    categories.value = data.data;
  } catch (e) {
    console.error('Failed to load categories', e);
  } finally {
    loadingCategories.value = false;
  }
};

onMounted(fetchCategories);

const clearErrors = () => {
  errors.name = '';
  errors.url = '';
  errors.category_id = '';
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
  if (!form.category_id) {
    errors.category_id = 'Category is required.';
    valid = false;
  }

  return valid;
};

watch(
  () => props.initialData,
  (data) => {
    form.name = data?.name ?? '';
    form.url = data?.url ?? '';
    form.icon = data?.icon ?? '';
    form.category_id = data?.category_id ?? null;
    form.description = data?.description ?? '';
    clearErrors();
  },
  { immediate: true },
);

const handleSubmit = () => {
  if (!validate()) return;
  const payload: ApplicationPayload = {
    name: form.name.trim(),
    url: form.url.trim(),
    icon: form.icon.trim() || null,
    category_id: form.category_id,
    description: form.description.trim() || null,
  };
  emit('submit', payload);
};
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        Application Name <span class="text-red-500">*</span>
      </label>
      <InputText
        v-model="form.name"
        placeholder="e.g. ERP System"
        :class="['w-full', { 'p-invalid': errors.name }]"
        :disabled="disabled"
      />
      <small v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        URL <span class="text-red-500">*</span>
      </label>
      <InputText
        v-model="form.url"
        placeholder="https://erp.internal"
        :class="['w-full', { 'p-invalid': errors.url }]"
        :disabled="disabled"
      />
      <small v-if="errors.url" class="text-red-500 text-xs">{{ errors.url }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">Icon</label>
      <Select
        v-model="form.icon"
        :options="primeIcons"
        optionLabel="name"
        optionValue="icon"
        placeholder="Select an Icon"
        class="w-full"
        :disabled="disabled"
      >
        <template #value="slotProps">
          <div v-if="slotProps.value" class="flex items-center">
            <i :class="slotProps.value" class="mr-2 text-primary-600"></i>
            <div>{{ primeIcons.find((i) => i.icon === slotProps.value)?.name }}</div>
          </div>
          <span v-else>{{ slotProps.placeholder }}</span>
        </template>
        <template #option="slotProps">
          <div class="flex items-center">
            <i :class="slotProps.option.icon" class="mr-2 text-slate-600"></i>
            <div>{{ slotProps.option.name }}</div>
          </div>
        </template>
      </Select>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">
        Category <span class="text-red-500">*</span>
      </label>
      <Select
        v-model="form.category_id"
        :options="categories"
        optionLabel="name"
        optionValue="id"
        placeholder="Select a Category"
        :loading="loadingCategories"
        :class="['w-full', { 'p-invalid': errors.category_id }]"
        :disabled="disabled"
      />
      <small v-if="errors.category_id" class="text-red-500 text-xs">{{ errors.category_id }}</small>
    </div>

    <div class="flex flex-col gap-1.5">
      <label class="text-sm font-semibold text-slate-700">Description</label>
      <Textarea
        v-model="form.description"
        placeholder="Brief description of the application..."
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
          :label="initialData?.id ? 'Save Changes' : 'Create Application'"
          icon="pi pi-check"
          :loading="loading"
        />
      </template>
    </div>
  </form>
</template>
