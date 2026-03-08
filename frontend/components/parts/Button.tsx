import { type ComponentProps } from 'react';

type ButtonVariant = 'primary' | 'secondary' | 'danger';
type ButtonSize = 'default' | 'sm';

type ButtonProps = {
  variant?: ButtonVariant;
  size?: ButtonSize;
} & ComponentProps<'button'>;

const variantClasses: Record<ButtonVariant, string> = {
  primary: 'bg-blue-600 text-white hover:bg-blue-700',
  secondary: 'border border-zinc-300 text-zinc-900 hover:bg-zinc-50',
  danger: 'border border-red-300 text-red-600 hover:bg-red-50',
};

const sizeClasses: Record<ButtonSize, string> = {
  default: 'px-4 py-2 text-sm',
  sm: 'px-3 py-1 text-sm',
};

export function Button({
  variant = 'primary',
  size = 'default',
  className,
  ...props
}: ButtonProps) {
  return (
    <button
      className={`rounded disabled:opacity-50 ${variantClasses[variant]} ${sizeClasses[size]} ${className ?? ''}`}
      {...props}
    />
  );
}
