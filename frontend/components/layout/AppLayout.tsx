'use client';

import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { useEffect } from 'react';

import { Button } from '@/components/parts/Button';
import { Text } from '@/components/parts/Text';
import { useAuth } from '@/lib/AuthContext';

export function AppLayout({ children }: { children: React.ReactNode }) {
  const { isLoggedIn, isAuthLoading, logout } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!isAuthLoading && !isLoggedIn) {
      router.replace('/login');
    }
  }, [isAuthLoading, isLoggedIn, router]);

  if (isAuthLoading) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-white">
        <Text as="p" variant="muted">
          読み込み中...
        </Text>
      </div>
    );
  }

  if (!isLoggedIn) {
    return null;
  }

  return (
    <div className="min-h-screen bg-white text-zinc-900">
      <header className="border-b bg-white">
        <div className="mx-auto flex max-w-4xl items-center justify-between px-4 py-3">
          <h1 className="text-xl font-bold">
            <Link href="/">Issue Tracker</Link>
          </h1>
          <div className="flex items-center gap-4">
            <Link
              href="/issues/new"
              className="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
            >
              新規作成
            </Link>
            <Button variant="secondary" onClick={logout}>
              ログアウト
            </Button>
          </div>
        </div>
      </header>
      <main className="mx-auto max-w-4xl px-4 py-6">{children}</main>
    </div>
  );
}
