'use client';

import { useQuery } from '@tanstack/react-query';
import Link from 'next/link';

import { IssueListView } from '@/components/feature/issue/list/IssueListView';
import { AppLayout } from '@/components/layout/AppLayout';
import { Heading } from '@/components/parts/Heading';
import { Text } from '@/components/parts/Text';
import { apiClient } from '@/lib/api-client/client';
import { useAuth } from '@/lib/AuthContext';

export function IssueListPage() {
  const { isLoggedIn, isAuthLoading } = useAuth();

  const { data, isLoading, error } = useQuery({
    queryKey: ['issues'],
    queryFn: async () => {
      const { data, error } = await apiClient.GET('/api/issue');
      if (error) throw new Error('error' in error ? error.error : 'Failed to fetch issues');
      return data;
    },
    enabled: isLoggedIn,
  });

  if (isAuthLoading) {
    return (
      <Text as="p" variant="muted">
        読み込み中...
      </Text>
    );
  }

  if (!isLoggedIn) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-white text-zinc-900">
        <div className="text-center">
          <Heading level={1} className="mb-4">
            Issue Tracker
          </Heading>
          <Text as="p" variant="muted" className="mb-6">
            ログインしてください
          </Text>
          <div className="flex justify-center gap-4">
            <Link
              href="/login"
              className="rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700"
            >
              ログイン
            </Link>
            <Link
              href="/signup"
              className="rounded border border-zinc-300 px-6 py-2 text-zinc-900 hover:bg-zinc-50"
            >
              サインアップ
            </Link>
          </div>
        </div>
      </div>
    );
  }

  return (
    <AppLayout>
      <Heading level={2} className="mb-4">
        課題一覧
      </Heading>
      {isLoading && (
        <Text as="p" variant="muted">
          読み込み中...
        </Text>
      )}
      {error && (
        <Text as="p" variant="error">
          エラーが発生しました: {error.message}
        </Text>
      )}
      {data && data.issues.length === 0 && (
        <Text as="p" variant="muted">
          課題はまだありません。
        </Text>
      )}
      {data && data.issues.length > 0 && <IssueListView issues={data.issues} />}
    </AppLayout>
  );
}
