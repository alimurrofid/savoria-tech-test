<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Avatar from 'primevue/avatar';

const authStore = useAuthStore();
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const handleLogout = () => {
  authStore.logout();
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex font-sans">
    <aside
      :class="[
        'bg-white border-r border-slate-100 flex flex-col fixed h-full z-20 transition-all duration-300 ease-in-out',
        isSidebarCollapsed ? 'w-20' : 'w-64',
      ]"
    >
      <div
        class="h-16 flex items-center transition-all duration-300"
        :class="isSidebarCollapsed ? 'justify-center w-full' : 'px-5'"
      >
        <div class="flex items-center gap-3 overflow-hidden">
          <div
            class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black text-xl shrink-0 shadow-lg shadow-blue-200"
          >
            S
          </div>
          <span
            v-if="!isSidebarCollapsed"
            class="font-bold text-xl text-slate-800 tracking-tight whitespace-nowrap animate-fade-in"
          >
            StarterKit
          </span>
        </div>
      </div>

      <div class="flex-1 px-3 py-4 space-y-6 overflow-y-auto overflow-x-hidden">
        <div>
          <span
            v-if="!isSidebarCollapsed"
            class="text-[10px] font-bold text-slate-400 tracking-[0.2em] mb-4 block px-4 uppercase"
            >Main Menu</span
          >
          <div v-else class="h-px bg-slate-100 w-10 mx-auto mb-6"></div>

          <nav class="space-y-1">
            <router-link
              to="/dashboard"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Overview', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              active-class="bg-primary-50 text-primary-600 font-semibold"
              :class="[
                $route.path === '/dashboard'
                  ? 'bg-primary-50 text-primary-600'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-home text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap"> Overview </span>
            </router-link>

            <router-link
              to="/products"
              v-tooltip.right="
                isSidebarCollapsed ? { value: 'Products', class: 'custom-tooltip' } : null
              "
              class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200"
              active-class="bg-primary-50 text-primary-600 font-semibold"
              :class="[
                $route.path.includes('/products')
                  ? 'bg-primary-50 text-primary-600'
                  : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700',
                isSidebarCollapsed ? 'justify-center' : '',
              ]"
            >
              <i class="pi pi-box text-lg shrink-0"></i>
              <span v-if="!isSidebarCollapsed" class="whitespace-nowrap"> Products </span>
            </router-link>
          </nav>
        </div>
      </div>

      <div class="p-4 border-t border-slate-50">
        <div
          class="flex items-center bg-slate-50 rounded-2xl cursor-pointer hover:bg-slate-100 transition-all duration-200 overflow-hidden"
          :class="isSidebarCollapsed ? 'justify-center py-3' : 'justify-between px-3 py-2.5'"
          v-tooltip.right="isSidebarCollapsed ? 'Account Settings' : null"
        >
          <div class="flex items-center gap-3 shrink-0">
            <Avatar
              image="https://api.dicebear.com/7.x/notionists/svg?seed=John"
              shape="circle"
              class="border-2 border-white shadow-sm"
            />
            <div v-if="!isSidebarCollapsed" class="animate-fade-in">
              <p class="text-sm font-bold text-slate-700 leading-none">John Doe</p>
              <p class="text-[10px] text-slate-400 font-medium mt-1 uppercase tracking-wider">
                Super Admin
              </p>
            </div>
          </div>
          <button
            v-if="!isSidebarCollapsed"
            @click.stop="handleLogout"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors"
          >
            <i class="pi pi-sign-out text-sm"></i>
          </button>
        </div>

        <button
          v-if="isSidebarCollapsed"
          @click="handleLogout"
          v-tooltip.right="'Sign Out'"
          class="w-full mt-4 flex justify-center text-slate-400 hover:text-red-500 transition-colors"
        >
          <i class="pi pi-sign-out text-xl"></i>
        </button>
      </div>
    </aside>

    <div
      :class="[
        'flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out',
        isSidebarCollapsed ? 'ml-20' : 'ml-64',
      ]"
    >
      <header
        class="h-16 bg-white/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] border-b border-slate-50"
      >
        <div class="flex items-center gap-6">
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

          <div class="w-80 hidden md:block">
            <IconField iconPosition="left">
              <InputIcon class="pi pi-search text-slate-300 text-sm" />
              <InputText
                placeholder="Search anything..."
                class="w-full bg-slate-50 border-none rounded-xl text-sm px-10 py-2.5 placeholder:text-slate-400 focus:ring-2 focus:ring-primary-100 transition-all"
              />
            </IconField>
          </div>
        </div>

        <div class="flex items-center gap-5">
          <button
            class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-slate-50 transition-all relative"
          >
            <i class="pi pi-bell"></i>
            <span
              class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"
            ></span>
          </button>
          <div class="h-8 w-px bg-slate-100"></div>
          <div class="flex items-center gap-3 cursor-pointer group">
            <Avatar
              image="https://api.dicebear.com/7.x/notionists/svg?seed=John"
              shape="circle"
              class="group-hover:ring-4 group-hover:ring-primary-50 transition-all"
            />
            <i
              class="pi pi-chevron-down text-slate-300 text-[10px] group-hover:text-slate-500 transition-colors"
            ></i>
          </div>
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
