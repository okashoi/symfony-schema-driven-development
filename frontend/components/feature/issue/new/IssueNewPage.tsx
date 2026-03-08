'use client';

import { useRouter } from 'next/navigation';

import { CreateIssueForm } from '@/components/feature/issue/new/CreateIssueForm';
import { AppLayout } from '@/components/layout/AppLayout';
import { Heading } from '@/components/parts/Heading';

export function IssueNewPage() {
  const router = useRouter();

  return (
    <AppLayout>
      <button
        onClick={() => router.push('/')}
        className="mb-4 text-sm text-blue-600 hover:underline"
      >
        &larr; 一覧に戻る
      </button>

      <div className="rounded-lg border bg-white p-6">
        <Heading level={2} className="mb-4">
          課題を作成
        </Heading>
        <CreateIssueForm />
      </div>
    </AppLayout>
  );
}
