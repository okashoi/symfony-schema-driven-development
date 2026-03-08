'use client';

import Link from 'next/link';

import { LoginForm } from '@/components/feature/auth/login/LoginForm';
import { Heading } from '@/components/parts/Heading';
import { Text } from '@/components/parts/Text';

export function LoginPage() {
  return (
    <div className="flex min-h-screen items-center justify-center bg-white text-zinc-900">
      <div className="w-full max-w-md rounded-lg bg-white p-8 shadow">
        <Heading level={1} className="mb-6">
          ログイン
        </Heading>
        <LoginForm />
        <Text as="p" variant="muted" className="mt-4 text-center">
          アカウントをお持ちでない方は{' '}
          <Link href="/signup" className="text-blue-600 hover:underline">
            サインアップ
          </Link>
        </Text>
      </div>
    </div>
  );
}
