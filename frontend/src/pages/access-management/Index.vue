<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue';
import Select from 'primevue/select';
import Skeleton from 'primevue/skeleton';
import Message from 'primevue/message';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import AccessPickList from '@/components/access-management/AccessPickList.vue';
import api from '@/services/api';
import type {
  Department,
  Role,
  User,
  Application,
  AccessRulePayload,
  ApiResponse,
  PaginatedApiResponse,
} from '@/types/api';

defineOptions({
  name: 'AccessManagementIndex',
});

const toast = useToast();

type EntityType = 'department' | 'role' | 'user';

const allApplications = ref<Application[]>([]);
const masterDepartments = ref<Department[]>([]);
const masterRoles = ref<Role[]>([]);
const masterUsers = ref<User[]>([]);
const masterLoading = ref(true);

const selectedDepartment = ref<number | null>(null);
const selectedRole = ref<number | null>(null);
const selectedUser = ref<number | null>(null);

const assigned = ref<Application[]>([]);
const loadingAccess = ref(false);
const saving = ref(false);

const filteredUsers = computed(() => {
  return masterUsers.value.filter((user) => {
    const matchDept = !selectedDepartment.value || user.department_id === selectedDepartment.value;
    const matchRole = !selectedRole.value || user.role_id === selectedRole.value;
    return matchDept && matchRole;
  });
});

const activeEntityType = computed<EntityType | null>(() => {
  if (selectedUser.value) return 'user';
  if (selectedRole.value && !selectedDepartment.value) return 'role';
  if (selectedDepartment.value && !selectedRole.value) return 'department';

  return null;
});

const activeEntityId = computed<number | null>(() => {
  if (selectedUser.value) return selectedUser.value;
  if (selectedRole.value && !selectedDepartment.value) return selectedRole.value;
  if (selectedDepartment.value && !selectedRole.value) return selectedDepartment.value;
  return null;
});

onMounted(async () => {
  try {
    const [deptRes, roleRes, userRes, appRes] = await Promise.all([
      api.get<ApiResponse<Department[]>>('/departments'),
      api.get<ApiResponse<Role[]>>('/roles'),
      api.get<ApiResponse<User[]>>('/users'),
      api.get<PaginatedApiResponse<Application>>('/applications', { params: { page: 1 } }),
    ]);
    masterDepartments.value = deptRes.data.data;
    masterRoles.value = roleRes.data.data;
    masterUsers.value = userRes.data.data;
    allApplications.value = appRes.data.data;
  } catch {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load data.', life: 3000 });
  } finally {
    masterLoading.value = false;
  }
});

watch(selectedUser, (userId) => {
  if (userId) {
    const user = masterUsers.value.find((u) => u.id === userId);
    if (user) {
      selectedDepartment.value = user.department_id;
      selectedRole.value = user.role_id;
    }
  }
});

watch(activeEntityId, async (id) => {
  if (!id || !activeEntityType.value) {
    assigned.value = [];
    return;
  }

  loadingAccess.value = true;
  try {
    const { data } = await api.get<ApiResponse<AccessRulePayload>>(
      `/access-rules/${activeEntityType.value}/${id}`,
    );
    assigned.value = data.data.assigned_applications;
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load access rules.',
      life: 3000,
    });
  } finally {
    loadingAccess.value = false;
  }
});

const saveAccess = async (applicationIds: number[]) => {
  if (!activeEntityId.value || !activeEntityType.value) return;

  saving.value = true;
  try {
    await api.put(`/access-rules/${activeEntityType.value}/${activeEntityId.value}`, {
      application_ids: applicationIds,
    });
    toast.add({
      severity: 'success',
      summary: 'Saved',
      detail: 'Access rights updated successfully.',
      life: 3000,
    });

    const { data } = await api.get<ApiResponse<AccessRulePayload>>(
      `/access-rules/${activeEntityType.value}/${activeEntityId.value}`,
    );
    assigned.value = data.data.assigned_applications;
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to save access rights.',
      life: 3000,
    });
  } finally {
    saving.value = false;
  }
};

const resetFilters = () => {
  selectedUser.value = null;
  selectedRole.value = null;
  selectedDepartment.value = null;
  assigned.value = [];
};
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Access Management</h1>
        <p class="text-sm text-slate-400 mt-1">
          Assign and revoke application access with smart filtering
        </p>
      </div>
      <Button
        label="Clear Selection"
        icon="pi pi-refresh"
        severity="secondary"
        text
        class="w-full sm:w-auto"
        @click="resetFilters"
      />
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 flex items-center gap-2">
            <i class="pi pi-building text-slate-400"></i> Department
          </label>
          <Select
            v-model="selectedDepartment"
            :options="masterDepartments"
            optionLabel="name"
            optionValue="id"
            placeholder="Filter by Department..."
            :disabled="!!selectedUser"
            class="w-full"
            filter
            showClear
          />
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 flex items-center gap-2">
            <i class="pi pi-id-card text-slate-400"></i> Role
          </label>
          <Select
            v-model="selectedRole"
            :options="masterRoles"
            optionLabel="name"
            optionValue="id"
            placeholder="Filter by Role..."
            :disabled="!!selectedUser"
            class="w-full"
            filter
            showClear
          />
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-slate-700 flex items-center gap-2">
            <i class="pi pi-user text-slate-400"></i> Specific User Assignment
          </label>
          <Select
            v-model="selectedUser"
            :options="filteredUsers"
            :optionLabel="(opt) => `${opt.name} (${opt.email})`"
            optionValue="id"
            placeholder="Select a specific user..."
            class="w-full"
            filter
            showClear
          />
        </div>
      </div>

      <div class="border-t border-slate-100 pt-6">
        <div v-if="loadingAccess" class="space-y-3">
          <Skeleton height="2.5rem" />
          <Skeleton height="12rem" />
        </div>

        <div v-else-if="selectedDepartment && selectedRole && !selectedUser">
          <Message severity="warn" :closable="false">
            You have selected both a Department and a Role. Please select a
            <strong>Specific User</strong> within this cross-section to adjust individual access, or
            clear one field to manage at the group level.
          </Message>
        </div>

        <Message v-else-if="!activeEntityId" severity="secondary" :closable="false">
          Select a single Department, a single Role, or a Specific User above to manage application
          access.
        </Message>

        <div v-else class="space-y-2">
          <AccessPickList
            :all="allApplications"
            :assigned="assigned"
            :saving="saving"
            @save="saveAccess"
          />
        </div>
      </div>
    </div>
  </div>
</template>
