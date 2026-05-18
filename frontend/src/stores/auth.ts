import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api';
import router from '@/router';
import type { User } from '@/types/api';

export const useAuthStore = defineStore('auth', () => {
  const storedUser = localStorage.getItem('user');
  const user = ref<User | null>(storedUser ? JSON.parse(storedUser) : null);

  const token = ref<string | null>(localStorage.getItem('token'));

  const login = async (credentials: { email: string; password: string }) => {
    const response = await api.post('/login', credentials);
    const data = response.data.data;

    token.value = data.token;
    user.value = data.user as User;

    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));

    router.push('/');
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch {}
    token.value = null;
    user.value = null;

    localStorage.removeItem('token');
    localStorage.removeItem('user');

    router.push('/login');
  };

  return {
    user,
    token,
    login,
    logout,
  };
});
