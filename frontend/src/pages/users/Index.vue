<script setup lang="ts">
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import type { ApiResponse, Department, Role, User } from '@/types/api';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { useAuthStore } from '@/stores/auth';

defineOptions({
  name: 'UsersIndex',
});

const authStore = useAuthStore();

const toast = useToast();
const confirm = useConfirm();
const router = useRouter();

type UserWithRelations = User & { department?: Department | null; role?: Role | null };

const records = ref<UserWithRelations[]>([]);
const loading = ref(false);
const dt = ref();

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const fetchRecords = async () => {
  loading.value = true;
  try {
    const { data } = await api.get<ApiResponse<UserWithRelations[]>>('/users');
    records.value = data.data;
  } catch {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load users.', life: 3000 });
  } finally {
    loading.value = false;
  }
};
onMounted(fetchRecords);

const openCreate = () => router.push({ name: 'users.create' });
const openEdit = (row: User) => router.push({ name: 'users.edit', params: { id: row.id } });
const openShow = (row: User) => router.push({ name: 'users.show', params: { id: row.id } });

const handleDelete = (row: User) => {
  confirm.require({
    message: `Delete user "${row.name}" (${row.email})?`,
    header: 'Delete User',
    icon: 'pi pi-exclamation-triangle',
    rejectLabel: 'Cancel',
    acceptLabel: 'Delete',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await api.delete(`/users/${row.id}`);
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
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Users</h1>
        <p class="text-sm text-slate-400 mt-1">Manage system users and their access</p>
      </div>
      <Button label="Add User" icon="pi pi-plus" class="w-full sm:w-auto" @click="openCreate" />
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <DataTable
        ref="dt"
        :value="records"
        dataKey="id"
        :loading="loading"
        :paginator="true"
        :rows="10"
        responsiveLayout="scroll"
        v-model:filters="filters"
        :globalFilterFields="['name', 'email']"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
        stripedRows
        class="text-sm"
      >
        <template #header>
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <span class="text-sm text-slate-400 font-medium">{{ records.length }} total</span>
            <IconField iconPosition="left" class="w-full sm:w-auto">
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText
                v-model="filters['global'].value"
                placeholder="Search..."
                class="w-full sm:w-64"
              />
            </IconField>
          </div>
        </template>
        <Column field="name" header="Name" style="min-width: 180px">
          <template #body="{ data }">
            <div class="flex items-center gap-3">
              <div
                class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-xs shrink-0"
              >
                {{ data.name?.charAt(0)?.toUpperCase() }}
              </div>
              <span class="font-semibold text-slate-800">{{ data.name }}</span>
            </div>
          </template>
        </Column>

        <Column field="email" header="Email" style="min-width: 180px">
          <template #body="{ data }"
            ><span class="text-slate-600">{{ data.email }}</span></template
          >
        </Column>

        <Column header="Department" style="width: 140px">
          <template #body="{ data }">
            <span class="text-slate-600 text-sm">{{ data.department?.name ?? '—' }}</span>
          </template>
        </Column>

        <Column header="Role" style="width: 130px">
          <template #body="{ data }">
            <span class="text-slate-600 text-sm">{{ data.role?.name ?? '—' }}</span>
          </template>
        </Column>

        <Column header="Type" style="width: 100px">
          <template #body="{ data }">
            <Tag
              :value="data.is_admin ? 'Admin' : 'User'"
              :severity="data.is_admin ? 'warn' : 'secondary'"
            />
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
                v-if="authStore.user?.id !== data.id"
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
            <p class="text-slate-500 font-medium">No users found</p>
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
