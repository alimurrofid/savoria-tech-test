<script setup lang="ts">
import Card from 'primevue/card';
import AppTable from '@/components/common/AppTable.vue';
import Column from 'primevue/column';
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const stats = ref([
  { title: 'Total Users', value: '0', icon: 'pi pi-users', color: 'text-blue-500' },
  { title: 'Total Products', value: '0', icon: 'pi pi-box', color: 'text-purple-500' },
  { title: 'Active Projects', value: '0', icon: 'pi pi-briefcase', color: 'text-green-500' },
  { title: 'Revenue', value: '$0', icon: 'pi pi-dollar', color: 'text-orange-500' },
]);

const recentActivity = ref([
  { id: 1, action: 'User login', user: 'Admin User', date: '2023-10-27 10:00 AM' },
  { id: 2, action: 'Project created', user: 'John Doe', date: '2023-10-27 09:30 AM' },
  { id: 3, action: 'Task completed', user: 'Jane Smith', date: '2023-10-27 08:45 AM' },
  { id: 4, action: 'Settings updated', user: 'Admin User', date: '2023-10-26 04:20 PM' },
]);

onMounted(async () => {
  try {
    const response = await api.get('/dashboard');
    const data = response.data.data;
    
    stats.value[0]!.value = data.total_users.toString();
    stats.value[1]!.value = data.total_products.toString();
    stats.value[2]!.value = data.active_projects.toString();
    stats.value[3]!.value = `$${data.revenue.toLocaleString()}`;
  } catch (error) {
    console.error('Failed to load dashboard stats', error);
  }
});
</script>

<template>
  <div class="flex flex-col gap-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">Dashboard</h1>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <Card v-for="(stat, index) in stats" :key="index" class="shadow-sm">
        <template #content>
          <div class="flex items-center justify-between">
            <div>
              <p class="text-surface-500 dark:text-surface-400 font-medium mb-1">{{ stat.title }}</p>
              <h2 class="text-3xl font-bold text-surface-900 dark:text-surface-0">{{ stat.value }}</h2>
            </div>
            <div :class="['w-12 h-12 flex items-center justify-center rounded-full bg-surface-100 dark:bg-surface-800', stat.color]">
              <i :class="[stat.icon, 'text-xl']"></i>
            </div>
          </div>
        </template>
      </Card>
    </div>

    <!-- Recent Activity Table -->
    <Card class="shadow-sm">
      <template #title>Recent Activity</template>
      <template #content>
        <AppTable :value="recentActivity">
          <Column field="action" header="Action"></Column>
          <Column field="user" header="User"></Column>
          <Column field="date" header="Date"></Column>
        </AppTable>
      </template>
    </Card>
  </div>
</template>
