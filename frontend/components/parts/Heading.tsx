import { type ComponentProps } from 'react';

type HeadingLevel = 1 | 2 | 3;

type HeadingProps = {
  level: HeadingLevel;
} & ComponentProps<'h1'>;

const levelClasses: Record<HeadingLevel, string> = {
  1: 'text-2xl font-bold text-zinc-900',
  2: 'text-xl font-semibold text-zinc-900',
  3: 'text-lg font-medium text-zinc-900',
};

export function Heading({ level, className, ...props }: HeadingProps) {
  const Tag = `h${level}` as const;
  return <Tag className={`${levelClasses[level]} ${className ?? ''}`} {...props} />;
}
