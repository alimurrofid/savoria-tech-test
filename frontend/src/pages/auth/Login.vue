<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import Card from 'primevue/card';
import AppFormInput from '@/components/common/AppFormInput.vue';
import AppButton from '@/components/common/AppButton.vue';

const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');

const submit = async () => {
  loading.value = true;
  errorMessage.value = '';
  try {
    await authStore.login({ email: email.value, password: password.value });
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Login failed. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-surface-50 dark:bg-surface-950 p-4">
    <Card class="w-full max-w-md shadow-lg">
      <template #title>
        <div class="text-center text-2xl font-bold mb-2">Welcome Back</div>
      </template>
      <template #subtitle>
        <div class="text-center text-surface-500 mb-6">Please enter your details to sign in.</div>
      </template>
      <template #content>
        <form @submit.prevent="submit" class="flex flex-col gap-4">
          <AppFormInput
            id="email"
            label="Email Address"
            type="email"
            v-model="email"
            placeholder="admin@example.com"
          />

          <AppFormInput
            id="password"
            label="Password"
            type="password"
            v-model="password"
            placeholder="••••••••"
          />

          <div v-if="errorMessage" class="p-3 bg-red-100 text-red-700 rounded-md text-sm">
            {{ errorMessage }}
          </div>

          <AppButton type="submit" label="Sign In" class="w-full mt-2" :loading="loading" />
        </form>
      </template>
    </Card>
  </div>
</template>
