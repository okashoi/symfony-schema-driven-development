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

type AssignedIssue = {
  id: string;
  summary: string;
  isClosed: boolean;
};

type Props = {
  issues: AssignedIssue[];
};

export function AssignedIssueListView({ issues }: Props) {
  const router = useRouter();

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHeaderCell>サマリー</TableHeaderCell>
          <TableHeaderCell>ステータス</TableHeaderCell>
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
              <StatusBadge isClosed={issue.isClosed} />
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}
