import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';
import router from '@/router';

export const useAuthStore = defineStore('auth', () => {
  const user = ref<any>(null);
  const token = ref<string | null>(localStorage.getItem('token'));

  const login = async (credentials: any) => {
    const response = await api.post('/login', credentials);
    token.value = response.data.data.token;
    user.value = response.data.data.user;
    localStorage.setItem('token', response.data.data.token);
    router.push('/');
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch (e) {
      // Ignore error if already unauthenticated
    }
    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
    router.push('/login');
  };

  return {
    user,
    token,
    login,
    logout,
  };
});
