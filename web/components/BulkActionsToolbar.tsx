"use client";

import { useState, useTransition } from "react";
import { useRouter } from "next/navigation";
import { useFlag } from "@/lib/flags/context";
import { bulkDeleteReports } from "@/lib/api/mutations";

type Props = {
  reportIds: number[];
};

/**
 * Conditional component #1 — rendered only when 'reports.bulk_actions' is on.
 *
 * Server-side flag: reports.bulk_actions
 * Targeting: role = admin
 *
 * Stale-interaction story: even if the user sees this toolbar, the DELETE
 * /reports/bulk endpoint enforces the flag server-side and returns 410 if it
 * has since been disabled.
 */
export function BulkActionsToolbar({ reportIds }: Props) {
  const enabled = useFlag("reports.bulk_actions");
  const router = useRouter();
  const [selected, setSelected] = useState<Set<number>>(new Set());
  const [pending, startTransition] = useTransition();
  const [staleError, setStaleError] = useState<string | null>(null);

  if (!enabled) return null;

  const toggleAll = (checked: boolean) =>
    setSelected(checked ? new Set(reportIds) : new Set());

  const handleDelete = () => {
    if (selected.size === 0) return;
    setStaleError(null);
    startTransition(async () => {
      const result = await bulkDeleteReports([...selected]);
      if (result.type === "feature_disabled") {
        setStaleError("Bulk actions have been disabled. Refresh to update the view.");
      } else if (result.type === "ok") {
        setSelected(new Set());
        router.refresh();
      }
    });
  };

  return (
    <div className="flex items-center gap-3 rounded border border-zinc-200 bg-zinc-50 px-4 py-2 dark:border-zinc-800 dark:bg-zinc-900">
      <label className="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
        <input
          type="checkbox"
          checked={selected.size === reportIds.length && reportIds.length > 0}
          onChange={(e) => toggleAll(e.target.checked)}
          className="h-4 w-4 rounded border-zinc-300"
        />
        Select all ({reportIds.length})
      </label>

      {selected.size > 0 && (
        <button
          onClick={handleDelete}
          disabled={pending}
          className="rounded border border-red-300 px-3 py-1 text-sm font-medium text-red-600 hover:bg-red-50 disabled:opacity-50 dark:border-red-800 dark:text-red-400"
        >
          {pending ? "Deleting…" : `Delete ${selected.size} selected`}
        </button>
      )}

      {staleError && (
        <span className="text-sm text-amber-600 dark:text-amber-400">{staleError}</span>
      )}
    </div>
  );
}
