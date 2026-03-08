'use client';

import { useQueryClient } from '@tanstack/react-query';
import { useRouter } from 'next/navigation';
import { createContext, useCallback, useContext, useEffect, useState } from 'react';

import { apiClient } from '@/lib/api-client/client';

type AuthContextType = {
  isLoggedIn: boolean;
  isAuthLoading: boolean;
  login: (name: string, password: string) => Promise<void>;
  signup: (name: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
};

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const router = useRouter();
  const queryClient = useQueryClient();

  useEffect(() => {
    void apiClient
      .GET('/api/auth/me')
      .then(({ error }) => {
        if (!error) {
          setIsLoggedIn(true);
        }
      })
      .finally(() => {
        setIsLoading(false);
      });
  }, []);

  const login = useCallback(
    async (name: string, password: string) => {
      const { error } = await apiClient.POST('/api/auth/login', {
        body: { name, password },
      });
      if (error) {
        throw new Error('error' in error ? error.error : 'Login failed');
      }
      setIsLoggedIn(true);
      router.push('/');
    },
    [router],
  );

  const signup = useCallback(
    async (name: string, password: string) => {
      const { error } = await apiClient.POST('/api/auth/signup', {
        body: { name, password },
      });
      if (error) {
        throw new Error('error' in error ? error.error : 'Signup failed');
      }
      await login(name, password);
    },
    [login],
  );

  const logout = useCallback(async () => {
    await apiClient.POST('/api/auth/logout');
    setIsLoggedIn(false);
    queryClient.clear();
    router.push('/login');
  }, [router, queryClient]);

  return (
    <AuthContext value={{ isLoggedIn, isAuthLoading: isLoading, login, signup, logout }}>
      {children}
    </AuthContext>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
