import { type ComponentProps, type ElementType } from 'react';

type TextVariant = 'body' | 'muted' | 'error' | 'label';

type TextProps<T extends ElementType = 'span'> = {
  as?: T;
  variant?: TextVariant;
} & ComponentProps<T>;

const variantClasses: Record<TextVariant, string> = {
  body: 'text-sm text-zinc-900',
  muted: 'text-sm text-zinc-700',
  error: 'text-sm text-red-600',
  label: 'text-sm font-medium text-zinc-900',
};

export function Text<T extends ElementType = 'span'>({
  as,
  variant = 'body',
  className,
  ...props
}: TextProps<T>) {
  const Component = as ?? 'span';
  const variantClass = variantClasses[variant as TextVariant];
  return <Component className={`${variantClass} ${className ?? ''}`} {...props} />;
}
