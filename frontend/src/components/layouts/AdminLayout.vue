<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import AppButton from '@/components/common/AppButton.vue';

const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
};
</script>

<template>
  <div class="min-h-screen bg-surface-50 dark:bg-surface-950 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-surface-900 border-r border-surface-200 dark:border-surface-800 flex flex-col">
      <div class="p-4 border-b border-surface-200 dark:border-surface-800 h-16 flex items-center">
        <span class="font-bold text-xl text-primary-500">StarterKit</span>
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <router-link to="/dashboard" class="block p-3 rounded-md hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-700 dark:text-surface-0 transition-colors">
          <i class="pi pi-home mr-2"></i>Dashboard
        </router-link>
        <router-link to="/products" class="block p-3 rounded-md hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-700 dark:text-surface-0 transition-colors">
          <i class="pi pi-box mr-2"></i>Products
        </router-link>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Top Navbar -->
      <header class="h-16 bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800 flex items-center justify-between px-6">
        <div>
          <!-- Breadcrumb or Title could go here -->
        </div>
        <div class="flex items-center gap-4">
          <AppButton label="Logout" severity="secondary" size="small" @click="handleLogout" />
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
