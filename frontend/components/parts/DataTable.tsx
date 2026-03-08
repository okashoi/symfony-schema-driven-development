import { type ComponentProps } from 'react';

export function Table({ className, children, ...props }: ComponentProps<'table'>) {
  return (
    <div className="overflow-hidden rounded-lg border border-zinc-200 bg-white">
      <table className={`w-full ${className ?? ''}`} {...props}>
        {children}
      </table>
    </div>
  );
}

export function TableHeader({ className, children, ...props }: ComponentProps<'thead'>) {
  return (
    <thead className={`bg-zinc-50 ${className ?? ''}`} {...props}>
      {children}
    </thead>
  );
}

export function TableBody({ className, children, ...props }: ComponentProps<'tbody'>) {
  return (
    <tbody className={`divide-y divide-zinc-100 ${className ?? ''}`} {...props}>
      {children}
    </tbody>
  );
}

type TableRowProps = {
  clickable?: boolean;
} & ComponentProps<'tr'>;

export function TableRow({ clickable, className, children, ...props }: TableRowProps) {
  return (
    <tr
      className={`${clickable ? 'cursor-pointer hover:bg-zinc-50' : ''} ${className ?? ''}`}
      {...props}
    >
      {children}
    </tr>
  );
}

export function TableHeaderCell({ className, children, ...props }: ComponentProps<'th'>) {
  return (
    <th
      className={`px-4 py-3 text-left text-sm font-medium text-zinc-900 ${className ?? ''}`}
      {...props}
    >
      {children}
    </th>
  );
}

export function TableCell({ className, children, ...props }: ComponentProps<'td'>) {
  return (
    <td className={`px-4 py-3 ${className ?? ''}`} {...props}>
      {children}
    </td>
  );
}
