import { type ComponentProps, forwardRef } from 'react';

type TextInputProps = ComponentProps<'input'>;

export const TextInput = forwardRef<HTMLInputElement, TextInputProps>(
  ({ className, ...props }, ref) => {
    return (
      <input
        ref={ref}
        className={`w-full rounded border border-zinc-300 px-3 py-2 text-zinc-900 placeholder:text-zinc-500 focus:border-blue-500 focus:outline-none ${className ?? ''}`}
        {...props}
      />
    );
  },
);

TextInput.displayName = 'TextInput';
