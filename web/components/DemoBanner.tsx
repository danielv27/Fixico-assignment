"use client";

import { useFlag } from "@/lib/flags/context";

export function DemoBanner() {
  const enabled = useFlag("demo.banner");
  if (!enabled) return null;

  return (
    <div className="border-b border-emerald-200 bg-emerald-50 px-6 py-3 text-sm text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950 dark:text-emerald-100">
      Slice 1 sanity check — this banner is gated by the{" "}
      <code className="font-mono">demo.banner</code> flag.
    </div>
  );
}
