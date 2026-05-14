<script setup lang="ts">
import InputText from 'primevue/inputtext';

defineProps<{
  id: string;
  label: string;
  modelValue: string | number;
  type?: string;
  error?: string;
  placeholder?: string;
}>();

defineEmits<{
  (e: 'update:modelValue', value: string | number): void;
}>();
</script>

<template>
  <div class="flex flex-col gap-1 w-full">
    <label :for="id" class="font-medium text-sm text-surface-700 dark:text-surface-0">{{ label }}</label>
    <InputText
      :id="id"
      :type="type || 'text'"
      :modelValue="modelValue == null ? '' : String(modelValue)"
      @update:modelValue="$emit('update:modelValue', type === 'number' && $event ? Number($event) : ($event ?? ''))"
      :class="{ 'p-invalid': error }"
      :placeholder="placeholder"
      class="w-full"
    />
    <small v-if="error" class="text-red-500">{{ error }}</small>
  </div>
</template>
