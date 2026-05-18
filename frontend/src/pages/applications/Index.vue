<script setup lang="ts">
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import ConfirmDialog from 'primevue/confirmdialog';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import type { Application, PaginatedApiResponse } from '@/types/api';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

defineOptions({
  name: 'ApplicationsIndex',
});

const toast = useToast();
const confirm = useConfirm();
const router = useRouter();

const records = ref<Application[]>([]);
const loading = ref(false);
const dt = ref();

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const fetchRecords = async () => {
  loading.value = true;
  try {
    const { data } = await api.get<PaginatedApiResponse<Application>>('/applications', {
      params: { page: 1, search: '' },
    });
    records.value = data.data;
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load applications.',
      life: 3000,
    });
  } finally {
    loading.value = false;
  }
};

onMounted(fetchRecords);

const openCreate = () => router.push({ name: 'applications.create' });
const openEdit = (row: Application) =>
  router.push({ name: 'applications.edit', params: { id: row.id } });
const openShow = (row: Application) =>
  router.push({ name: 'applications.show', params: { id: row.id } });

const handleDelete = (row: Application) => {
  confirm.require({
    message: `Delete "${row.name}"? This will also remove all its access assignments.`,
    header: 'Delete Application',
    icon: 'pi pi-exclamation-triangle',
    rejectLabel: 'Cancel',
    acceptLabel: 'Delete',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await api.delete(`/applications/${row.id}`);
        toast.add({
          severity: 'success',
          summary: 'Deleted',
          detail: `"${row.name}" deleted.`,
          life: 3000,
        });
        await fetchRecords();
      } catch {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete.', life: 3000 });
      }
    },
  });
};
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Applications</h1>
        <p class="text-sm text-slate-400 mt-1">Manage the master list of applications</p>
      </div>
      <Button label="Add Application" icon="pi pi-plus" @click="openCreate" />
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
        :globalFilterFields="['name', 'category']"
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
              <InputText v-model="filters['global'].value" placeholder="Search..." class="w-64" />
            </IconField>
          </div>
        </template>

        <Column field="name" header="Application" style="min-width: 200px">
          <template #body="{ data }">
            <div class="flex items-center gap-3">
              <div
                class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0"
              >
                <i :class="data.icon ?? 'pi pi-th-large'"></i>
              </div>
              <span class="font-semibold text-slate-800">{{ data.name }}</span>
            </div>
          </template>
        </Column>

        <Column field="category" header="Category" style="width: 150px">
          <template #body="{ data }">
            <Tag :value="data.category?.name ?? '—'" severity="secondary" />
          </template>
        </Column>

        <Column field="url" header="URL" style="min-width: 180px">
          <template #body="{ data }">
            <a
              :href="data.url"
              target="_blank"
              rel="noopener noreferrer"
              class="text-primary-600 hover:underline truncate max-w-xs block"
            >
              {{ data.url }}
            </a>
          </template>
        </Column>

        <Column header="Description" style="min-width: 200px">
          <template #body="{ data }">
            <span class="text-slate-500 line-clamp-1">{{ data.description ?? '—' }}</span>
          </template>
        </Column>

        <Column header="Actions" style="width: 140px">
          <template #body="{ data }">
            <div class="flex items-center justify-end gap-2">
              <Button
                icon="pi pi-eye"
                size="small"
                severity="info"
                text
                rounded
                v-tooltip.top="'Show'"
                @click="openShow(data)"
              />
              <Button
                icon="pi pi-pencil"
                size="small"
                severity="secondary"
                text
                rounded
                v-tooltip.top="'Edit'"
                @click="openEdit(data)"
              />
              <Button
                icon="pi pi-trash"
                size="small"
                severity="danger"
                text
                rounded
                v-tooltip.top="'Delete'"
                @click="handleDelete(data)"
              />
            </div>
          </template>
        </Column>

        <template #empty>
          <div class="flex flex-col items-center justify-center py-12 text-center">
            <i class="pi pi-inbox text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-500 font-medium">No applications found</p>
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
