'use client';

import { zodResolver } from '@hookform/resolvers/zod';
import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { z } from 'zod';

import { Button } from '@/components/parts/Button';
import { ErrorAlert } from '@/components/parts/ErrorAlert';
import { LabeledTextInput } from '@/components/parts/LabeledTextInput';
import { useAuth } from '@/lib/AuthContext';

const signupSchema = z.object({
  name: z.string().min(1, 'ユーザー名を入力してください'),
  password: z.string().min(1, 'パスワードを入力してください'),
});

type SignupInput = z.infer<typeof signupSchema>;

export function SignupForm() {
  const { signup } = useAuth();
  const [error, setError] = useState('');
  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<SignupInput>({
    resolver: zodResolver(signupSchema),
  });

  const onSubmit = async (data: SignupInput) => {
    setError('');
    try {
      await signup(data.name, data.password);
    } catch (e) {
      setError(e instanceof Error ? e.message : 'サインアップに失敗しました');
    }
  };

  return (
    <>
      {error && <ErrorAlert message={error} />}
      <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
        <LabeledTextInput
          id="name"
          label="ユーザー名"
          type="text"
          error={errors.name?.message}
          {...register('name')}
        />
        <LabeledTextInput
          id="password"
          label="パスワード"
          type="password"
          error={errors.password?.message}
          {...register('password')}
        />
        <Button type="submit" disabled={isSubmitting} className="w-full">
          {isSubmitting ? '登録中...' : 'サインアップ'}
        </Button>
      </form>
    </>
  );
}
