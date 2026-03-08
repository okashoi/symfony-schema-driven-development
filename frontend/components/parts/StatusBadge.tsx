export function StatusBadge({ isClosed }: { isClosed: boolean }) {
  if (isClosed) {
    return (
      <span className="inline-block rounded-full bg-zinc-200 px-2 py-0.5 text-xs text-zinc-700">
        クローズ
      </span>
    );
  }
  return (
    <span className="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">
      オープン
    </span>
  );
}
