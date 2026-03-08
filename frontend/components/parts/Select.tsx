import { type ComponentProps } from 'react';

type SelectProps = ComponentProps<'select'>;

export function Select({ className, children, ...props }: SelectProps) {
  return (
    <select
      className={`rounded border border-zinc-300 px-2 py-1 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none ${className ?? ''}`}
      {...props}
    >
      {children}
    </select>
  );
}
