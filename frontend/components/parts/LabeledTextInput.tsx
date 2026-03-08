import { forwardRef } from 'react';

import { TextInput } from '@/components/parts/TextInput';

type LabeledTextInputProps = {
  id: string;
  label: string;
  error?: string;
  placeholder?: string;
} & React.InputHTMLAttributes<HTMLInputElement>;

export const LabeledTextInput = forwardRef<HTMLInputElement, LabeledTextInputProps>(
  ({ id, label, error, ...inputProps }, ref) => {
    return (
      <div>
        <label htmlFor={id} className="mb-1 block text-sm font-medium text-zinc-900">
          {label}
        </label>
        <TextInput id={id} ref={ref} {...inputProps} />
        {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
      </div>
    );
  },
);

LabeledTextInput.displayName = 'LabeledTextInput';
