'use client';

import { useRouter } from 'next/navigation';

import {
  Table,
  TableBody,
  TableCell,
  TableHeader,
  TableHeaderCell,
  TableRow,
} from '@/components/parts/DataTable';
import { StatusBadge } from '@/components/parts/StatusBadge';
import { Text } from '@/components/parts/Text';

type Issue = {
  id: string;
  summary: string;
  assignee?: string | null;
  isClosed: boolean;
  createdAt: string;
};

type Props = {
  issues: Issue[];
};

export function IssueListView({ issues }: Props) {
  const router = useRouter();

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHeaderCell>サマリー</TableHeaderCell>
          <TableHeaderCell>担当者</TableHeaderCell>
          <TableHeaderCell>ステータス</TableHeaderCell>
          <TableHeaderCell>作成日</TableHeaderCell>
        </TableRow>
      </TableHeader>
      <TableBody>
        {issues.map((issue) => (
          <TableRow key={issue.id} clickable onClick={() => router.push(`/issues/${issue.id}`)}>
            <TableCell>
              <Text className={issue.isClosed ? 'text-zinc-500 line-through' : ''}>
                {issue.summary}
              </Text>
            </TableCell>
            <TableCell>
              <Text>{issue.assignee || '-'}</Text>
            </TableCell>
            <TableCell>
              <StatusBadge isClosed={issue.isClosed} />
            </TableCell>
            <TableCell>
              <Text>{new Date(issue.createdAt).toLocaleDateString('ja-JP')}</Text>
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}
