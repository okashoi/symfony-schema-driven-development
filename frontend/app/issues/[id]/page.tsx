'use client';

import { useParams } from 'next/navigation';

import { IssueDetailPage } from '@/components/feature/issue/detail/IssueDetailPage';

export default function Page() {
  const { id } = useParams<{ id: string }>();

  return <IssueDetailPage id={id} />;
}
