import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/auth/Login.vue'),
      meta: { requiresAuth: false },
    },
    {
      path: '/',
      component: () => import('@/components/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true },
      redirect: '/dashboard',
      children: [
        {
          path: '/dashboard',
          name: 'dashboard',
          component: () => import('@/pages/dashboard/Index.vue'),
        },
        {
          path: '/profile',
          name: 'profile',
          component: () => import('@/pages/profile/Index.vue'),
        },
        // ── Admin-only routes ─────────────────────────────────────────────────
        {
          path: '/applications',
          name: 'applications.index',
          component: () => import('@/pages/applications/Index.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/applications/create',
          name: 'applications.create',
          component: () => import('@/pages/applications/Create.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/applications/:id/edit',
          name: 'applications.edit',
          component: () => import('@/pages/applications/Edit.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/access-management',
          name: 'access-management.index',
          component: () => import('@/pages/access-management/Index.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/departments',
          name: 'departments.index',
          component: () => import('@/pages/departments/Index.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/departments/create',
          name: 'departments.create',
          component: () => import('@/pages/departments/Create.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/departments/:id/edit',
          name: 'departments.edit',
          component: () => import('@/pages/departments/Edit.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/roles',
          name: 'roles.index',
          component: () => import('@/pages/roles/Index.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/roles/create',
          name: 'roles.create',
          component: () => import('@/pages/roles/Create.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/roles/:id/edit',
          name: 'roles.edit',
          component: () => import('@/pages/roles/Edit.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/users',
          name: 'users.index',
          component: () => import('@/pages/users/Index.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/users/create',
          name: 'users.create',
          component: () => import('@/pages/users/Create.vue'),
          meta: { requiresAdmin: true },
        },
        {
          path: '/users/:id/edit',
          name: 'users.edit',
          component: () => import('@/pages/users/Edit.vue'),
          meta: { requiresAdmin: true },
        },
      ],
    },
  ],
});

router.beforeEach((to) => {
  const authStore = useAuthStore();
  const isAuthenticated = !!authStore.token;

  if (to.meta.requiresAuth && !isAuthenticated) {
    return { name: 'login' };
  }

  if (to.name === 'login' && isAuthenticated) {
    return { name: 'dashboard' };
  }

  // Admin-only route accessed by a regular user → redirect silently
  if (to.meta.requiresAdmin && !authStore.user?.is_admin) {
    return { name: 'dashboard' };
  }

  return true;
});

export default router;
