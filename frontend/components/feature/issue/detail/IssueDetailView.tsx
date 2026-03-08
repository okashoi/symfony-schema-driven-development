'use client';

import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import Link from 'next/link';
import { useState } from 'react';

import { Button } from '@/components/parts/Button';
import { Heading } from '@/components/parts/Heading';
import { Select } from '@/components/parts/Select';
import { StatusBadge } from '@/components/parts/StatusBadge';
import { Text } from '@/components/parts/Text';
import { TextInput } from '@/components/parts/TextInput';
import { apiClient } from '@/lib/api-client/client';

type Issue = {
  summary: string;
  assignee?: { id: string; name: string } | null;
  createdAt: string;
  updatedAt: string;
  closedAt?: string | null;
};

type Props = {
  issue: Issue;
  id: string;
};

export function IssueDetailView({ issue, id }: Props) {
  const queryClient = useQueryClient();

  const [isEditing, setIsEditing] = useState(false);
  const [editSummary, setEditSummary] = useState('');
  const [isAssigning, setIsAssigning] = useState(false);
  const [selectedUserId, setSelectedUserId] = useState('');

  const { data: users } = useQuery({
    queryKey: ['users'],
    queryFn: async () => {
      const { data, error } = await apiClient.GET('/api/user');
      if (error) throw new Error('error' in error ? error.error : 'Failed to fetch users');
      return data;
    },
    enabled: isAssigning,
  });

  const updateMutation = useMutation({
    mutationFn: async (summary: string) => {
      const { error } = await apiClient.PUT('/api/issue/{id}', {
        params: { path: { id } },
        body: { summary },
      });
      if (error) throw new Error('error' in error ? error.error : 'Failed to update issue');
    },
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['issue', id] });
      void queryClient.invalidateQueries({ queryKey: ['issues'] });
      setIsEditing(false);
    },
  });

  const closeMutation = useMutation({
    mutationFn: async () => {
      const { error } = await apiClient.POST('/api/issue/{id}/close', {
        params: { path: { id } },
      });
      if (error) throw new Error('error' in error ? error.error : 'Failed to close issue');
    },
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['issue', id] });
      void queryClient.invalidateQueries({ queryKey: ['issues'] });
    },
  });

  const assignMutation = useMutation({
    mutationFn: async (assigneeId: string) => {
      const { error } = await apiClient.POST('/api/assignment', {
        body: { issueId: id, assigneeId },
      });
      if (error) throw new Error('error' in error ? error.error : 'Failed to assign issue');
    },
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: ['issue', id] });
      void queryClient.invalidateQueries({ queryKey: ['issues'] });
      setIsAssigning(false);
      setSelectedUserId('');
    },
  });

  const isClosed = !!issue.closedAt;

  return (
    <div className="rounded-lg border bg-white p-6">
      <div className="mb-4 flex items-start justify-between">
        {isEditing ? (
          <div className="mr-4 flex-1">
            <TextInput
              type="text"
              value={editSummary}
              onChange={(e) => setEditSummary(e.target.value)}
              className="text-xl font-semibold"
            />
            <div className="mt-2 flex gap-2">
              <Button
                variant="primary"
                size="sm"
                onClick={() => updateMutation.mutate(editSummary)}
                disabled={updateMutation.isPending || !editSummary.trim()}
              >
                保存
              </Button>
              <Button variant="secondary" size="sm" onClick={() => setIsEditing(false)}>
                キャンセル
              </Button>
            </div>
            {updateMutation.error && (
              <Text as="p" variant="error" className="mt-2">
                {updateMutation.error.message}
              </Text>
            )}
          </div>
        ) : (
          <Heading level={2}>{issue.summary}</Heading>
        )}
        <div className="flex gap-2">
          {!isEditing && (
            <>
              <Button
                variant="secondary"
                size="sm"
                onClick={() => {
                  setEditSummary(issue.summary);
                  setIsEditing(true);
                }}
              >
                編集
              </Button>
              {!isClosed && (
                <Button
                  variant="danger"
                  size="sm"
                  onClick={() => closeMutation.mutate()}
                  disabled={closeMutation.isPending}
                >
                  クローズ
                </Button>
              )}
            </>
          )}
        </div>
      </div>

      {closeMutation.error && (
        <Text as="p" variant="error" className="mb-4">
          {closeMutation.error.message}
        </Text>
      )}

      <div className="space-y-3 text-sm text-zinc-900">
        <div className="flex">
          <Text variant="label" className="w-24">
            ステータス
          </Text>
          <StatusBadge isClosed={isClosed} />
        </div>
        <div className="flex items-center">
          <Text variant="label" className="w-24">
            担当者
          </Text>
          <div className="flex items-center gap-2">
            {issue.assignee ? (
              <Link href={`/users/${issue.assignee.id}`} className="text-blue-600 hover:underline">
                {issue.assignee.name}
              </Link>
            ) : (
              <Text>未割り当て</Text>
            )}
            {!isAssigning && (
              <Button
                variant="secondary"
                size="sm"
                className="px-2 py-0.5 text-xs"
                onClick={() => setIsAssigning(true)}
              >
                変更
              </Button>
            )}
          </div>
        </div>
        {isAssigning && (
          <div className="ml-24 flex items-center gap-2">
            <Select value={selectedUserId} onChange={(e) => setSelectedUserId(e.target.value)}>
              <option value="">選択してください</option>
              {users?.users.map((user) => (
                <option key={user.id} value={user.id}>
                  {user.name}
                </option>
              ))}
            </Select>
            <Button
              variant="primary"
              size="sm"
              className="text-xs"
              onClick={() => assignMutation.mutate(selectedUserId)}
              disabled={!selectedUserId || assignMutation.isPending}
            >
              割り当て
            </Button>
            <Button
              variant="secondary"
              size="sm"
              className="text-xs"
              onClick={() => {
                setIsAssigning(false);
                setSelectedUserId('');
              }}
            >
              キャンセル
            </Button>
            {assignMutation.error && (
              <Text variant="error" className="text-xs">
                {assignMutation.error.message}
              </Text>
            )}
          </div>
        )}
        <div className="flex">
          <Text variant="label" className="w-24">
            作成日
          </Text>
          <Text>{new Date(issue.createdAt).toLocaleString('ja-JP')}</Text>
        </div>
        <div className="flex">
          <Text variant="label" className="w-24">
            更新日
          </Text>
          <Text>{new Date(issue.updatedAt).toLocaleString('ja-JP')}</Text>
        </div>
        {issue.closedAt && (
          <div className="flex">
            <Text variant="label" className="w-24">
              クローズ日
            </Text>
            <Text>{new Date(issue.closedAt).toLocaleString('ja-JP')}</Text>
          </div>
        )}
      </div>
    </div>
  );
}
