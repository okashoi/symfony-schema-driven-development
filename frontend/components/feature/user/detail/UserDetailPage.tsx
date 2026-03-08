'use client';

import { useQuery } from '@tanstack/react-query';
import Link from 'next/link';
import { useRouter } from 'next/navigation';

import { AssignedIssueListView } from '@/components/feature/user/detail/AssignedIssueListView';
import { AppLayout } from '@/components/layout/AppLayout';
import { Heading } from '@/components/parts/Heading';
import { Text } from '@/components/parts/Text';
import { apiClient } from '@/lib/api-client/client';

type Props = {
  id: string;
};

export function UserDetailPage({ id }: Props) {
  const router = useRouter();

  const {
    data: user,
    isLoading,
    error,
  } = useQuery({
    queryKey: ['user', id],
    queryFn: async () => {
      const { data, error } = await apiClient.GET('/api/user/{id}', {
        params: { path: { id } },
      });
      if (error) throw new Error('error' in error ? error.error : 'Failed to fetch user');
      return data;
    },
  });

  if (isLoading) {
    return (
      <AppLayout>
        <Text as="p" variant="muted">
          読み込み中...
        </Text>
      </AppLayout>
    );
  }

  if (error || !user) {
    return (
      <AppLayout>
        <div className="text-center">
          <Text as="p" variant="error" className="mb-4">
            {error?.message || 'ユーザーが見つかりません'}
          </Text>
          <Link href="/" className="text-blue-600 hover:underline">
            一覧に戻る
          </Link>
        </div>
      </AppLayout>
    );
  }

  return (
    <AppLayout>
      <button onClick={() => router.back()} className="mb-4 text-sm text-blue-600 hover:underline">
        &larr; 戻る
      </button>

      <div className="rounded-lg border bg-white p-6">
        <Heading level={2} className="mb-4">
          {user.name}
        </Heading>

        <Heading level={3} className="mb-3">
          担当している課題
        </Heading>
        {user.assignedIssues.length === 0 ? (
          <Text as="p" variant="muted">
            担当している課題はありません。
          </Text>
        ) : (
          <AssignedIssueListView issues={user.assignedIssues} />
        )}
      </div>
    </AppLayout>
  );
}
