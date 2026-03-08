'use client';

import Link from 'next/link';

import { SignupForm } from '@/components/feature/auth/signup/SignupForm';
import { Heading } from '@/components/parts/Heading';
import { Text } from '@/components/parts/Text';

export function SignupPage() {
  return (
    <div className="flex min-h-screen items-center justify-center bg-white text-zinc-900">
      <div className="w-full max-w-md rounded-lg bg-white p-8 shadow">
        <Heading level={1} className="mb-6">
          サインアップ
        </Heading>
        <SignupForm />
        <Text as="p" variant="muted" className="mt-4 text-center">
          既にアカウントをお持ちの方は{' '}
          <Link href="/login" className="text-blue-600 hover:underline">
            ログイン
          </Link>
        </Text>
      </div>
    </div>
  );
}
