<script setup lang="ts">
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import api from '@/services/api';
import type { UserAccessReport, ApiResponse } from '@/types/api';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

const toast = useToast();

// ─── State ────────────────────────────────────────────────────────────────────
const records = ref<UserAccessReport[]>([]);
const loading = ref(false);
const dt = ref();

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// ─── Fetch ────────────────────────────────────────────────────────────────────
const fetchRecords = async () => {
  loading.value = true;
  try {
    const { data } = await api.get<ApiResponse<UserAccessReport[]>>('/reports/user-access');
    records.value = data.data as any[];
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load report.',
      life: 3000,
    });
  } finally {
    loading.value = false;
  }
};

onMounted(fetchRecords);
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">User Access Report</h1>
        <p class="text-sm text-slate-400 mt-1">View the applications each user has access to</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <DataTable
        ref="dt"
        :value="records"
        dataKey="id"
        :loading="loading"
        :paginator="true"
        :rows="10"
        v-model:filters="filters"
        :globalFilterFields="['name', 'email']"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
        stripedRows
        class="text-sm"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <span class="text-sm text-slate-400 font-medium">{{ records.length }} total</span>
            <IconField iconPosition="left">
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText
                v-model="filters['global'].value"
                placeholder="Search users..."
                class="w-64"
              />
            </IconField>
          </div>
        </template>

        <Column field="name" header="User" style="min-width: 200px">
          <template #body="{ data }">
            <div class="flex flex-col">
              <span class="font-semibold text-slate-800">{{ data.name }}</span>
              <span class="text-xs text-slate-500">{{ data.email }}</span>
            </div>
          </template>
        </Column>

        <Column header="Applications Accessed" style="min-width: 400px">
          <template #body="{ data }">
            <div class="flex flex-wrap gap-2">
              <span
                v-if="!data.applications || data.applications.length === 0"
                class="text-slate-400 italic"
                >No access</span
              >
              <Tag
                v-for="app in data.applications"
                :key="app.application_id"
                :value="app.name"
                severity="info"
                class="text-xs"
              />
            </div>
          </template>
        </Column>

        <template #empty>
          <div class="flex flex-col items-center justify-center py-12 text-center">
            <i class="pi pi-chart-bar text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-500 font-medium">No records found</p>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>

<style scoped>
:deep(.p-datatable .p-datatable-thead > tr > th) {
  background: #f8fafc;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3b8;
}
:deep(.p-datatable .p-datatable-tbody > tr > td) {
  border-bottom: 1px solid #f8fafc;
  padding-top: 0.9rem;
  padding-bottom: 0.9rem;
}
:deep(.p-paginator) {
  border-top: 1px solid #f1f5f9;
  padding: 0.75rem 1rem;
  background: white;
}
</style>
