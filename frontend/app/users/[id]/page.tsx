'use client';

import { useParams } from 'next/navigation';

import { UserDetailPage } from '@/components/feature/user/detail/UserDetailPage';

export default function Page() {
  const { id } = useParams<{ id: string }>();

  return <UserDetailPage id={id} />;
}
