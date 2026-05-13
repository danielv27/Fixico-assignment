import Link from "next/link";
import type { DamageReport } from "@/lib/api/reports";

const STATUS_STYLES: Record<DamageReport["status"], string> = {
  draft: "bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300",
  submitted: "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200",
  approved:
    "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200",
};

export function ReportList({ reports }: { reports: DamageReport[] }) {
  if (reports.length === 0) {
    return (
      <p className="text-sm text-zinc-600 dark:text-zinc-400">
        No damage reports yet.{" "}
        <Link
          href="/reports/new"
          className="font-medium text-emerald-700 underline dark:text-emerald-400"
        >
          Submit the first one
        </Link>
        .
      </p>
    );
  }

  return (
    <ul className="flex flex-col divide-y divide-zinc-200 rounded border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800">
      {reports.map((report) => (
        <li key={report.id}>
          <Link
            href={`/reports/${report.id}`}
            className="flex flex-col gap-1 px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-900"
          >
            <div className="flex items-center justify-between">
              <span className="font-medium">
                {report.vehicle_make} {report.vehicle_model}
              </span>
              <span
                className={`rounded-full px-2 py-0.5 text-xs font-medium ${STATUS_STYLES[report.status]}`}
              >
                {report.status}
              </span>
            </div>
            <div className="flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-400">
              <span className="font-mono">{report.license_plate}</span>
              {report.created_at && (
                <time dateTime={report.created_at}>
                  {new Date(report.created_at).toLocaleDateString()}
                </time>
              )}
            </div>
          </Link>
        </li>
      ))}
    </ul>
  );
}
