<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import Avatar from 'primevue/avatar';
import Menu from 'primevue/menu';

const authStore = useAuthStore();
const router = useRouter();
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const userMenuRef = ref<InstanceType<typeof Menu> | null>(null);
const toggleUserMenu = (event: Event) => userMenuRef.value?.toggle(event);

const userMenuItems = ref([
  {
    label: 'Profile',
    icon: 'pi pi-user',
    command: () => router.push({ name: 'profile' }),
  },
  { separator: true },
  {
    label: 'Sign Out',
    icon: 'pi pi-sign-out',
    command: () => authStore.logout(),
  },
]);
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex font-sans">
    <aside
      :class="[
        'bg-white border-r border-slate-100 flex flex-col fixed h-full z-20 transition-all duration-300 ease-in-out',
        isSidebarCollapsed ? 'w-20' : 'w-64',
      ]"
    >
      <!-- Logo -->
      <div
        class="h-16 flex items-center transition-all duration-300"
        :class="isSidebarCollapsed ? 'justify-center w-full' : 'px-5'"
      >
        <div class="flex items-center gap-3 overflow-hidden">
          <div
            class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white font-black text-xl shrink-0 shadow-lg shadow-red-200"
          >
            S
          </div>
          <span
            v-if="!isSidebarCollapsed"
            class="font-bold text-xl text-slate-800 tracking-tight whitespace-nowrap animate-fade-in"
          >
            Savoria
          </span>
        </div>
      </div>

      <!-- Nav links -->
      <div class="flex-1 px-3 py-4 space-y-6 overflow-y-auto overflow-x-hidden">
        <!-- Main Menu -->
        <div>
          <span
            v-if="!isSidebarCollapsed"
            class="text-[10px] font-bold text-slate-400 tracking-[0.2em] mb-4 block px-4 uppercase"
            >Main Menu</span
          >
          <div v-else class="h-px bg-slate-100 w-10 mx-auto mb-6"></div>

          <nav class="space-y-1">
            <!-- Dashboard -->
            <router-link
              to="/dashboard"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Dashboard', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path === '/dashboard'
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-home text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
            </router-link>
          </nav>
        </div>

        <!-- Admin Section -->
        <div v-if="authStore.user?.is_admin">
          <span
            v-if="!isSidebarCollapsed"
            class="text-[10px] font-bold text-slate-400 tracking-[0.2em] mb-4 block px-4 uppercase"
            >Administration</span
          >
          <div v-else class="h-px bg-slate-100 w-10 mx-auto mb-6"></div>

          <nav class="space-y-1">
            <!-- Applications -->
            <router-link
              to="/applications"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Applications', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path.startsWith('/applications')
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-th-large text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">Applications</span>
            </router-link>

            <!-- Categories -->
            <router-link
              to="/categories"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Categories', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path.startsWith('/categories')
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-tags text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">Categories</span>
            </router-link>

            <!-- Access Management -->
            <router-link
              to="/access-management"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Access Management', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path === '/access-management'
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-shield text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">Access Management</span>
            </router-link>
          </nav>
        </div>

        <!-- Master Data Section (Admin only) -->
        <div v-if="authStore.user?.is_admin">
          <span
            v-if="!isSidebarCollapsed"
            class="text-[10px] font-bold text-slate-400 tracking-[0.2em] mb-4 block px-4 uppercase"
            >Master Data</span
          >
          <div v-else class="h-px bg-slate-100 w-10 mx-auto mb-6"></div>

          <nav class="space-y-1">
            <!-- Departments -->
            <router-link
              to="/departments"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Departments', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path === '/departments'
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-building text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">Departments</span>
            </router-link>

            <!-- Roles -->
            <router-link
              to="/roles"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Roles', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path === '/roles'
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-id-card text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">Roles</span>
            </router-link>

            <!-- Users -->
            <router-link
              to="/users"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Users', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path.startsWith('/users')
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-users text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">Users</span>
            </router-link>
          </nav>
        </div>

        <!-- Reports Section (Admin only) -->
        <div v-if="authStore.user?.is_admin">
          <span
            v-if="!isSidebarCollapsed"
            class="text-[10px] font-bold text-slate-400 tracking-[0.2em] mb-4 block px-4 uppercase"
            >Reports</span
          >
          <div v-else class="h-px bg-slate-100 w-10 mx-auto mb-6"></div>

          <nav class="space-y-1">
            <!-- User Access Report -->
            <router-link
              to="/reports/user-access"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'User Access Report', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              :class="[
                $route.path === '/reports/user-access'
                  ? 'bg-primary-50 text-primary-600 font-semibold'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-chart-bar text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap">User Access</span>
            </router-link>
          </nav>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div
      :class="[
        'flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out',
        isSidebarCollapsed ? 'ml-20' : 'ml-64',
      ]"
    >
      <!-- Topbar -->
      <header
        class="h-16 bg-white/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] border-b border-slate-50"
      >
        <!-- Left: sidebar toggle -->
        <button
          @click="toggleSidebar"
          class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 hover:bg-slate-50 transition-all active:scale-95"
        >
          <i
            :class="[
              'pi text-lg transition-transform duration-300',
              isSidebarCollapsed ? 'pi-align-left rotate-180' : 'pi-align-left',
            ]"
          ></i>
        </button>

        <!-- Right: user avatar + dropdown menu -->
        <div class="flex items-center gap-3">
          <div class="text-right hidden md:block">
            <p class="text-sm font-bold text-slate-700 leading-none">{{ authStore.user?.name }}</p>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5 uppercase tracking-wider">
              {{ authStore.user?.is_admin ? 'Admin' : 'User' }}
            </p>
          </div>
          <Avatar
            :label="authStore.user?.name?.charAt(0).toUpperCase()"
            shape="circle"
            class="cursor-pointer border-2 border-white shadow-sm hover:ring-2 hover:ring-slate-300 transition-all select-none"
            @click="toggleUserMenu"
            aria-haspopup="true"
            aria-controls="user_menu"
          />
          <Menu id="user_menu" ref="userMenuRef" :model="userMenuItems" popup />
        </div>
      </header>

      <main class="flex-1 p-8">
        <RouterView />
        <Toast />
        <ConfirmDialog />
      </main>
    </div>
  </div>
</template>

<style>
.p-tooltip {
  max-width: 12rem;
}
.p-tooltip-text {
  background: #1e293b !important;
  color: #ffffff !important;
  font-size: 0.75rem !important;
  font-weight: 600 !important;
  padding: 0.5rem 0.75rem !important;
  border-radius: 8px !important;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}
.p-tooltip-arrow {
  border-right-color: #1e293b !important;
}
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
