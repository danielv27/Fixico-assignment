"use client";

import { useFlag } from "@/lib/flags/context";

export function NewReportBanner() {
  const enabled = useFlag("report.new_form_layout");
  if (!enabled) return null;

  return (
    <div className="flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-950/40">
      <div className="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
        <svg className="h-3 w-3 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20">
          <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
        </svg>
      </div>
      <div>
        <p className="text-sm font-medium text-blue-800 dark:text-blue-200">
          Improved reporting flow
        </p>
        <p className="mt-0.5 text-xs text-blue-600 dark:text-blue-400">
          You&apos;re in an early rollout for NL users. This layout will become the default after validation.
        </p>
      </div>
    </div>
  );
}
