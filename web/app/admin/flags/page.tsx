import Link from "next/link";
import { listFlags } from "@/lib/api/flags";

export default async function AdminFlagsPage() {
  const flags = await listFlags();

  return (
    <main className="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 px-6 py-12">
      <header className="flex items-center justify-between">
        <h1 className="text-3xl font-semibold tracking-tight">Feature flags</h1>
        <Link
          href="/admin/flags/new"
          className="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          New flag
        </Link>
      </header>

      {flags.length === 0 ? (
        <p className="text-zinc-500">No flags yet.</p>
      ) : (
        <ul className="flex flex-col divide-y divide-zinc-200 rounded border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800">
          {flags.map((flag) => (
            <li key={flag.id} className="flex items-center gap-4 px-4 py-3">
              <span
                className={`h-2.5 w-2.5 flex-shrink-0 rounded-full ${
                  flag.enabled ? "bg-emerald-500" : "bg-zinc-300"
                }`}
              />
              <div className="flex min-w-0 flex-1 flex-col">
                <span className="truncate font-mono text-sm font-medium">
                  {flag.name}
                </span>
                {flag.description && (
                  <span className="truncate text-xs text-zinc-500">
                    {flag.description}
                  </span>
                )}
              </div>
              <Link
                href={`/admin/flags/${flag.id}`}
                className="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100"
              >
                Edit →
              </Link>
            </li>
          ))}
        </ul>
      )}
    </main>
  );
}
