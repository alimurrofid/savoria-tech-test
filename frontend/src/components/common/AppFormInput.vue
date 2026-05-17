<script setup lang="ts">
import { ref, computed } from 'vue';
import InputText from 'primevue/inputtext';

const props = defineProps<{
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

const showPassword = ref(false);

const inputType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password';
  }
  return props.type || 'text';
});

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};
</script>

<template>
  <div class="flex flex-col gap-1 w-full">
    <label :for="id" class="font-medium text-sm text-surface-700 dark:text-surface-0">{{
      label
    }}</label>

    <div class="relative w-full">
      <InputText
        :id="id"
        :type="inputType"
        :modelValue="modelValue == null ? '' : String(modelValue)"
        @update:modelValue="
          $emit('update:modelValue', type === 'number' && $event ? Number($event) : ($event ?? ''))
        "
        :class="[{ 'p-invalid': error }, type === 'password' ? 'pr-10' : '', 'w-full']"
        :placeholder="placeholder"
      />

      <button
        v-if="type === 'password'"
        type="button"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none flex items-center"
        @click="togglePassword"
        tabindex="-1"
      >
        <i
          :class="['pi text-[1.1rem]', showPassword ? 'pi-eye-slash text-blue-500' : 'pi-eye']"
        ></i>
      </button>
    </div>

    <small v-if="error" class="text-red-500">{{ error }}</small>
  </div>
</template>
