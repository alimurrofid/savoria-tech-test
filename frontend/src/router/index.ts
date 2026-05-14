import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/auth/Login.vue'),
      meta: { requiresAuth: false }
    },
    {
      path: '/',
      component: () => import('@/components/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '/dashboard',
          name: 'dashboard',
          component: () => import('@/pages/dashboard/Index.vue')
        },
        {
          path: '/products',
          name: 'products',
          component: () => import('@/pages/products/Index.vue')
        }
      ]
    }
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

  return true;
});

export default router;
