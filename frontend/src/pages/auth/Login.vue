<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Message from 'primevue/message';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

defineOptions({
  name: 'AuthLogin',
});

const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');

const submit = async () => {
  loading.value = true;
  errorMessage.value = '';

  try {
    await authStore.login({
      email: email.value,
      password: password.value,
    });
  } catch (error: unknown) {
    const message =
      typeof error === 'object' && error !== null && 'response' in error
        ? (
            error as {
              response?: {
                data?: {
                  message?: string;
                };
              };
            }
          ).response?.data?.message
        : undefined;

    errorMessage.value = message || 'Login failed. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen grid lg:grid-cols-2 bg-surface-50">
    <div
      class="hidden lg:flex relative overflow-hidden flex-col justify-between p-12 bg-linear-to-br from-primary-900 via-primary-800 to-primary-700 text-white"
    >
      <div class="absolute inset-0 opacity-[0.05] pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white" />
        <div class="absolute -bottom-32 -left-20 w-md h-112 rounded-full bg-white" />
      </div>

      <div class="relative z-10 flex items-center gap-3">
        <div class="w-12 h-12 rounded-2xl bg-red-500 flex items-center justify-center">
          <span class="text-2xl font-bold text-white">S</span>
        </div>

        <div>
          <h2 class="font-semibold text-lg">Savoria</h2>
          <p class="text-primary-100/70 text-sm">Management Platform</p>
        </div>
      </div>

      <div class="relative z-10 max-w-xl">
        <h1 class="text-5xl font-bold leading-tight mb-5">
          Kelola sistem dengan lebih modern dan efisien.
        </h1>

        <p class="text-lg text-primary-100/80 leading-relaxed">
          Platform terintegrasi untuk operasional dan manajemen data secara terpusat.
        </p>
      </div>

      <div class="relative z-10 text-sm text-primary-100/50">© 2026 Savoria</div>
    </div>

    <div class="flex items-center justify-center p-6 lg:p-10 bg-surface-50">
      <div class="w-full max-w-md">
        <div class="lg:hidden text-center mb-8">
          <div
            class="mx-auto w-14 h-14 rounded-2xl bg-primary text-white flex items-center justify-center shadow-lg"
          >
            <span class="text-3xl font-bold font-serif">S</span>
          </div>

          <h2 class="mt-4 text-2xl font-bold text-surface-900">Savoria</h2>
          <p class="text-surface-500 text-sm">Management Platform</p>
        </div>

        <div class="bg-white border border-surface-200 rounded-3xl shadow-2xl p-8">
          <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-surface-900">Welcome Back</h1>

            <p class="mt-2 text-surface-500">Sign in to continue</p>
          </div>

          <Message v-if="errorMessage" severity="error" class="mb-5">
            {{ errorMessage }}
          </Message>

          <form @submit.prevent="submit" class="space-y-5">
            <div>
              <label class="block mb-2 text-sm font-medium text-surface-700"> Email Address </label>

              <IconField>
                <InputIcon>
                  <i class="pi pi-envelope text-surface-400" />
                </InputIcon>

                <InputText
                  v-model="email"
                  type="email"
                  placeholder="admin@example.com"
                  class="w-full border-surface-300"
                />
              </IconField>
            </div>

            <div>
              <label class="block mb-2 text-sm font-medium text-surface-700"> Password </label>

              <Password
                v-model="password"
                :feedback="false"
                toggleMask
                fluid
                placeholder="••••••••"
                class="border-surface-300"
              />
            </div>

            <div class="flex justify-end">
              <a href="#" class="text-sm text-primary hover:underline font-medium">
                Forgot password?
              </a>
            </div>

            <Button type="submit" label="Sign In" :loading="loading" class="w-full" />
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
