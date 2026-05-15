"use client";

import { useState } from "react";
import { useFeatureFlag } from "@/lib/feature-flags/context";

export function DemoBanner() {
  const enabled = useFeatureFlag("demo.banner");
  const [dismissed, setDismissed] = useState(false);

  if (!enabled || dismissed) return null;

  return (
    <div className="flex items-center justify-between gap-4 border-b border-emerald-100 bg-emerald-50 px-6 py-2">
      <p className="flex items-center gap-2 text-xs text-emerald-800">
        <span className="relative flex h-2 w-2">
          <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75" />
          <span className="relative inline-flex h-2 w-2 rounded-full bg-emerald-500" />
        </span>
        Feature flag service is active. Manage feature flags at{" "}
        <a
          href="http://localhost:8000/admin"
          target="_blank"
          rel="noopener noreferrer"
          className="font-semibold underline underline-offset-2 hover:text-emerald-900"
        >
          localhost:8000/admin ↗
        </a>
      </p>
      <button
        onClick={() => setDismissed(true)}
        className="flex-shrink-0 text-emerald-400 hover:text-emerald-700 transition-colors"
        aria-label="Dismiss"
      >
        <svg
          className="h-3.5 w-3.5"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>
  );
}
