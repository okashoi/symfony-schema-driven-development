'use client';

import { useQuery } from '@tanstack/react-query';
import Link from 'next/link';
import { useRouter } from 'next/navigation';

import { IssueDetailView } from '@/components/feature/issue/detail/IssueDetailView';
import { AppLayout } from '@/components/layout/AppLayout';
import { Text } from '@/components/parts/Text';
import { apiClient } from '@/lib/api-client/client';

type Props = {
  id: string;
};

export function IssueDetailPage({ id }: Props) {
  const router = useRouter();

  const {
    data: issue,
    isLoading,
    error,
  } = useQuery({
    queryKey: ['issue', id],
    queryFn: async () => {
      const { data, error } = await apiClient.GET('/api/issue/{id}', {
        params: { path: { id } },
      });
      if (error) throw new Error('error' in error ? error.error : 'Failed to fetch issue');
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

  if (error || !issue) {
    return (
      <AppLayout>
        <div className="text-center">
          <Text as="p" variant="error" className="mb-4">
            {error?.message || '課題が見つかりません'}
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
      <button
        onClick={() => router.push('/')}
        className="mb-4 text-sm text-blue-600 hover:underline"
      >
        &larr; 一覧に戻る
      </button>
      <IssueDetailView issue={issue} id={id} />
    </AppLayout>
  );
}
