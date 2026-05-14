"use client";

import { useFlag } from "@/lib/flags/context";

export function DemoBanner() {
  const enabled = useFlag("demo.banner");
  if (!enabled) return null;

  return (
    <div className="flex items-center justify-center gap-2 border-b border-emerald-100 bg-emerald-50 px-6 py-2.5 text-sm text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-950/60 dark:text-emerald-300">
      <span className="flex h-1.5 w-1.5 rounded-full bg-emerald-500">
        <span className="h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
      </span>
      Flag service active — manage flags at{" "}
      <a
        href="http://localhost:8000/admin/flags"
        target="_blank"
        rel="noopener noreferrer"
        className="font-medium underline underline-offset-2 hover:text-emerald-900 dark:hover:text-emerald-100"
      >
        localhost:8000/admin/flags
      </a>
    </div>
  );
}
