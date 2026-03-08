export function ErrorAlert({ message }: { message: string }) {
  return <div className="mb-4 rounded bg-red-50 p-3 text-sm text-red-600">{message}</div>;
}
