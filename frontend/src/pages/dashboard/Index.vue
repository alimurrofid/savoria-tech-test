<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Card from 'primevue/card';
import Skeleton from 'primevue/skeleton';
import api from '@/services/api';
import type { ApiResponse, UserAppAccess } from '@/types/api';

// ─── State ────────────────────────────────────────────────────────────────────
const apps = ref<UserAppAccess[]>([]);
const loading = ref(true);

// ─── Fetch ────────────────────────────────────────────────────────────────────
onMounted(async () => {
  try {
    const { data } = await api.get<ApiResponse<UserAppAccess[]>>('/dashboard');
    apps.value = data.data;
  } finally {
    loading.value = false;
  }
});

// ─── Helpers ──────────────────────────────────────────────────────────────────
const categoryColor = (category: string | null): string => {
  const map: Record<string, string> = {
    Enterprise: 'bg-violet-100 text-violet-700',
    HR: 'bg-teal-100 text-teal-700',
    Finance: 'bg-emerald-100 text-emerald-700',
    Support: 'bg-orange-100 text-orange-700',
    Productivity: 'bg-blue-100 text-blue-700',
    Logistics: 'bg-amber-100 text-amber-700',
    Sales: 'bg-pink-100 text-pink-700',
    Analytics: 'bg-indigo-100 text-indigo-700',
    IT: 'bg-cyan-100 text-cyan-700',
  };
  return map[category ?? ''] ?? 'bg-slate-100 text-slate-600';
};

const iconBg = (category: string | null): string => {
  const map: Record<string, string> = {
    Enterprise: 'bg-violet-50 text-violet-600',
    HR: 'bg-teal-50 text-teal-600',
    Finance: 'bg-emerald-50 text-emerald-600',
    Support: 'bg-orange-50 text-orange-600',
    Productivity: 'bg-blue-50 text-blue-600',
    Logistics: 'bg-amber-50 text-amber-600',
    Sales: 'bg-pink-50 text-pink-600',
    Analytics: 'bg-indigo-50 text-indigo-600',
    IT: 'bg-cyan-50 text-cyan-600',
  };
  return map[category ?? ''] ?? 'bg-slate-50 text-slate-500';
};
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">My Applications</h1>
        <p class="text-sm text-slate-400 mt-1">All applications you have access to</p>
      </div>
      <span
        v-if="!loading"
        class="text-xs font-semibold bg-primary-50 text-primary-600 rounded-full px-3 py-1.5"
      >
        {{ apps.length }} app{{ apps.length !== 1 ? 's' : '' }}
      </span>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <div
        v-for="n in 8"
        :key="n"
        class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-4"
      >
        <Skeleton width="3rem" height="3rem" borderRadius="12px" />
        <Skeleton width="70%" height="1rem" />
        <Skeleton width="40%" height="0.75rem" />
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="apps.length === 0"
      class="flex flex-col items-center justify-center py-24 text-center bg-white rounded-2xl border border-slate-100"
    >
      <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
        <i class="pi pi-inbox text-2xl text-slate-400"></i>
      </div>
      <h3 class="text-base font-bold text-slate-700">No applications assigned</h3>
      <p class="text-sm text-slate-400 mt-1">
        Contact your administrator to get access to applications.
      </p>
    </div>

    <!-- Application Grid -->
    <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <a
        v-for="app in apps"
        :key="app.application_id"
        :href="app.url"
        target="_blank"
        rel="noopener noreferrer"
        class="group block focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-400 rounded-2xl"
      >
        <Card
          class="h-full border border-slate-100 shadow-sm rounded-2xl transition-all duration-200 group-hover:shadow-md group-hover:-translate-y-0.5 group-hover:border-primary-100 overflow-hidden"
        >
          <template #content>
            <div class="p-2 space-y-4">
              <!-- Icon -->
              <div
                :class="[
                  'w-12 h-12 rounded-xl flex items-center justify-center text-xl',
                  iconBg(app.category),
                ]"
              >
                <i :class="app.icon ?? 'pi pi-th-large'"></i>
              </div>

              <!-- Name -->
              <div>
                <h3
                  class="text-sm font-bold text-slate-800 leading-tight group-hover:text-primary-600 transition-colors"
                >
                  {{ app.name }}
                </h3>
              </div>

              <!-- Category badge + arrow -->
              <div class="flex items-center justify-between">
                <span
                  :class="[
                    'text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full',
                    categoryColor(app.category),
                  ]"
                >
                  {{ app.category ?? 'App' }}
                </span>
                <i
                  class="pi pi-arrow-up-right text-slate-300 text-xs group-hover:text-primary-400 transition-colors"
                ></i>
              </div>
            </div>
          </template>
        </Card>
      </a>
    </div>
  </div>
</template>

<style scoped>
:deep(.p-card-body) {
  padding: 0;
}
</style>
