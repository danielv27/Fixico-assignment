"use client";

import { useFlag } from "@/lib/flags/context";

/**
 * Conditional component #3 — rendered only when 'report.new_form_layout' is on.
 *
 * Server-side flag: report.new_form_layout
 * Targeting: country = NL, rollout_percentage = 50
 *
 * Shows a redesigned header for the new-report page that is being rolled out
 * to NL users, 50 % at a time.
 */
export function NewReportBanner() {
  const enabled = useFlag("report.new_form_layout");
  if (!enabled) return null;

  return (
    <div className="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-900/40 dark:bg-blue-950 dark:text-blue-200">
      <p className="font-medium">New streamlined report form</p>
      <p className="mt-0.5 text-blue-700 dark:text-blue-300">
        You&apos;re seeing our improved reporting flow, rolling out to NL users.
        Your feedback matters — let us know what you think.
      </p>
    </div>
  );
}
