<script setup lang="ts">
import { ref } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const activities = ref([
  { id: 1, action: 'User login', user: 'Admin', date: '10:00 AM', status: 'Success' },
  { id: 2, action: 'Project created', user: 'John Doe', date: '09:30 AM', status: 'Active' },
  { id: 3, action: 'Task completed', user: 'Jane Smith', date: '08:45 AM', status: 'Complete' },
  { id: 4, action: 'Settings updated', user: 'System', date: '07:15 AM', status: 'Success' },
]);

const getSeverity = (status: string) => {
  switch (status) {
    case 'Success':
      return 'bg-green-100 text-green-700';
    case 'Active':
      return 'bg-cyan-100 text-cyan-700';
    case 'Complete':
      return 'bg-purple-100 text-purple-700';
    default:
      return 'bg-slate-100 text-slate-700';
  }
};
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100 flex items-center justify-between"
      >
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-full bg-teal-50 flex items-center justify-center text-teal-600"
            >
              <i class="pi pi-users text-lg"></i>
            </div>
            <span class="text-sm font-semibold text-slate-500 tracking-wide">Total Users</span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-slate-800">1,234</span>
            <span class="text-sm text-slate-400 font-medium">users</span>
          </div>
          <p class="text-xs text-slate-500 font-medium">
            <span class="text-teal-500">+5.2%</span> this week
          </p>
        </div>
        <div class="w-24 h-12">
          <svg
            viewBox="0 0 100 30"
            class="stroke-teal-400 fill-none"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M0,25 L20,15 L40,20 L60,5 L80,10 L100,0" />
          </svg>
        </div>
      </div>

      <div
        class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100 flex items-center justify-between"
      >
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600"
            >
              <i class="pi pi-folder text-lg"></i>
            </div>
            <span class="text-sm font-semibold text-slate-500 tracking-wide">Active Projects</span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-slate-800">42</span>
            <span class="text-sm text-slate-400 font-medium">projects</span>
          </div>
          <p class="text-xs text-slate-500 font-medium">
            <span class="text-teal-500">+1%</span> since yesterday
          </p>
        </div>
        <div class="w-24 h-12">
          <svg
            viewBox="0 0 100 30"
            class="stroke-indigo-400 fill-none"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M0,20 L30,20 L60,10 L80,15 L100,5" />
          </svg>
        </div>
      </div>

      <div
        class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100 flex items-center justify-between"
      >
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600"
            >
              <i class="pi pi-dollar text-lg"></i>
            </div>
            <span class="text-sm font-semibold text-slate-500 tracking-wide">Revenue</span>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-slate-800">$12,345</span>
          </div>
          <p class="text-xs text-slate-500 font-medium">
            <span class="text-red-500">-2.1%</span> this week
          </p>
        </div>
        <div class="w-24 h-12">
          <svg
            viewBox="0 0 100 30"
            class="stroke-slate-400 fill-none"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M0,5 L20,10 L40,5 L60,25 L80,15 L100,20" />
          </svg>
        </div>
      </div>
    </div>

    <div
      class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100"
    >
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-base font-bold text-slate-800">Performance trends</h2>
        <span
          class="text-xs font-semibold text-slate-500 border border-slate-200 rounded-lg px-3 py-1.5 cursor-pointer hover:bg-slate-50 transition-colors"
        >
          30 days <i class="pi pi-angle-down ml-1 text-[10px]"></i>
        </span>
      </div>
      <div class="w-full h-40 relative flex items-end">
        <svg viewBox="0 0 1000 100" class="w-full h-full drop-shadow-md" preserveAspectRatio="none">
          <defs>
            <linearGradient id="grad" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" style="stop-color: rgba(14, 165, 233, 0.2); stop-opacity: 1" />
              <stop offset="100%" style="stop-color: rgba(14, 165, 233, 0); stop-opacity: 1" />
            </linearGradient>
          </defs>
          <path
            d="M0,100 C150,60 250,80 400,50 C550,20 650,50 800,10 C900,0 1000,30 1000,30 L1000,100 L0,100 Z"
            fill="url(#grad)"
            stroke="#0ea5e9"
            stroke-width="2"
          />
        </svg>
      </div>
    </div>

    <div
      class="bg-white rounded-2xl p-2 shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100"
    >
      <div class="px-4 pt-4 pb-2">
        <h2 class="text-base font-bold text-slate-800">Recent Activity</h2>
      </div>

      <DataTable :value="activities" class="p-datatable-sm w-full text-sm">
        <Column
          field="action"
          header="ACTION"
          headerClass="text-xs font-semibold text-slate-400 tracking-wider bg-white border-b border-slate-100 pb-3 uppercase"
          bodyClass="py-4 text-slate-800 font-medium border-b border-slate-50 px-4"
        ></Column>
        <Column
          field="user"
          header="USER"
          headerClass="text-xs font-semibold text-slate-400 tracking-wider bg-white border-b border-slate-100 pb-3 uppercase"
          bodyClass="py-4 text-slate-600 border-b border-slate-50"
        ></Column>
        <Column
          field="date"
          header="DATE"
          headerClass="text-xs font-semibold text-slate-400 tracking-wider bg-white border-b border-slate-100 pb-3 uppercase"
          bodyClass="py-4 text-slate-500 border-b border-slate-50"
        ></Column>
        <Column
          field="status"
          header="STATUS"
          headerClass="text-xs font-semibold text-slate-400 tracking-wider bg-white border-b border-slate-100 pb-3 uppercase"
          bodyClass="py-4 border-b border-slate-50 px-4"
        >
          <template #body="{ data }">
            <span
              :class="[
                'px-2.5 py-1 rounded-md text-xs font-bold tracking-wide',
                getSeverity(data.status),
              ]"
            >
              {{ data.status }}
            </span>
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<style scoped>
:deep(.p-datatable .p-datatable-thead > tr > th) {
  border-bottom-width: 1px;
  border-color: #f1f5f9;
  background: white;
}
:deep(.p-datatable .p-datatable-tbody > tr > td) {
  border-bottom-width: 1px;
  border-color: #f8fafc;
}
</style>
