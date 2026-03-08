'use client';

import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { useRouter } from 'next/navigation';
import { useForm } from 'react-hook-form';
import { z } from 'zod';

import { Button } from '@/components/parts/Button';
import { ErrorAlert } from '@/components/parts/ErrorAlert';
import { LabeledTextInput } from '@/components/parts/LabeledTextInput';
import { apiClient } from '@/lib/api-client/client';

const createIssueSchema = z.object({
  summary: z.string().min(1, 'サマリーを入力してください'),
});

type CreateIssueInput = z.infer<typeof createIssueSchema>;

export function CreateIssueForm() {
  const router = useRouter();
  const queryClient = useQueryClient();

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<CreateIssueInput>({
    resolver: zodResolver(createIssueSchema),
  });

  const mutation = useMutation({
    mutationFn: async (data: CreateIssueInput) => {
      const { data: result, error } = await apiClient.POST('/api/issue', {
        body: { summary: data.summary },
      });
      if (error) throw new Error('error' in error ? error.error : 'Failed to create issue');
      return result;
    },
    onSuccess: (result) => {
      void queryClient.invalidateQueries({ queryKey: ['issues'] });
      router.push(`/issues/${result.id}`);
    },
  });

  return (
    <>
      {mutation.error && <ErrorAlert message={mutation.error.message} />}
      <form onSubmit={handleSubmit((data) => mutation.mutate(data))} className="space-y-4">
        <LabeledTextInput
          id="summary"
          label="サマリー"
          type="text"
          placeholder="課題の概要を入力"
          error={errors.summary?.message}
          {...register('summary')}
        />
        <div className="flex gap-2">
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? '作成中...' : '作成'}
          </Button>
          <Button variant="secondary" type="button" onClick={() => router.push('/')}>
            キャンセル
          </Button>
        </div>
      </form>
    </>
  );
}
