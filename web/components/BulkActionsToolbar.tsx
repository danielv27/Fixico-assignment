"use client";

import { useState, useTransition } from "react";
import { useRouter } from "next/navigation";
import { useFeatureFlag } from "@/lib/feature-flags/context";
import { bulkDeleteAction } from "@/app/reports/actions";

type Props = { reportIds: number[] };

export function BulkActionsToolbar({ reportIds }: Props) {
  const enabled = useFeatureFlag("reports.bulk_actions");
  const router = useRouter();
  const [selected, setSelected] = useState<Set<number>>(new Set());
  const [message, setMessage] = useState<string | null>(null);
  const [pending, startTransition] = useTransition();

  if (!enabled) return null;

  const allSelected = selected.size === reportIds.length && reportIds.length > 0;
  const toggleAll = (checked: boolean) =>
    setSelected(checked ? new Set(reportIds) : new Set());

  const handleDelete = () => {
    if (selected.size === 0) return;
    if (!confirm(`Delete ${selected.size} report${selected.size === 1 ? "" : "s"}? This cannot be undone.`)) return;
    startTransition(async () => {
      setMessage(null);
      const result = await bulkDeleteAction([...selected]);
      if ("error" in result) {
        setMessage(result.message);
        router.refresh();
        return;
      }

      setSelected(new Set());
      router.refresh();
    });
  };

  return (
    <div className="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white px-4 py-3 shadow-sm">
      <label className="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-zinc-700">
        <input
          type="checkbox"
          checked={allSelected}
          onChange={(e) => toggleAll(e.target.checked)}
          className="h-4 w-4 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500"
        />
        {selected.size === 0
          ? `Select all (${reportIds.length})`
          : `${selected.size} selected`}
      </label>

      {selected.size > 0 && (
        <button
          onClick={handleDelete}
          disabled={pending}
          className="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 disabled:opacity-50"
        >
          <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          {pending ? "Deleting…" : `Delete ${selected.size}`}
        </button>
      )}

      {message && (
        <span className="text-xs font-medium text-red-600">
          {message}
        </span>
      )}

      <span className="ml-auto text-xs font-medium text-violet-600">
        Admin only
      </span>
    </div>
  );
}
