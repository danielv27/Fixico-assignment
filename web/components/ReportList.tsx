import Link from "next/link";
import type { DamageReport } from "@/lib/api/reports";

const STATUS_CONFIG: Record<DamageReport["status"], { label: string; dot: string; text: string; bg: string }> = {
  draft: {
    label: "Draft",
    dot: "bg-zinc-400",
    text: "text-zinc-600 dark:text-zinc-400",
    bg: "bg-zinc-100 dark:bg-zinc-800",
  },
  submitted: {
    label: "Submitted",
    dot: "bg-blue-500",
    text: "text-blue-700 dark:text-blue-300",
    bg: "bg-blue-50 dark:bg-blue-950/50",
  },
  approved: {
    label: "Approved",
    dot: "bg-emerald-500",
    text: "text-emerald-700 dark:text-emerald-300",
    bg: "bg-emerald-50 dark:bg-emerald-950/50",
  },
};

const STATUS_BORDER: Record<DamageReport["status"], string> = {
  draft: "border-l-zinc-300 dark:border-l-zinc-600",
  submitted: "border-l-blue-400",
  approved: "border-l-emerald-500",
};

export function ReportList({ reports }: { reports: DamageReport[] }) {
  if (reports.length === 0) {
    return (
      <div className="rounded-xl border border-dashed border-zinc-300 bg-white px-6 py-16 text-center dark:border-zinc-700 dark:bg-zinc-900">
        <svg className="mx-auto h-10 w-10 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p className="mt-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">No damage reports yet</p>
        <p className="mt-1 text-sm text-zinc-500">Submit a report when damage is discovered.</p>
        <Link
          href="/reports/new"
          className="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 transition-colors"
        >
          Submit first report
        </Link>
      </div>
    );
  }

  return (
    <ul className="flex flex-col gap-2">
      {reports.map((report) => {
        const s = STATUS_CONFIG[report.status];
        return (
          <li key={report.id}>
            <Link
              href={`/reports/${report.id}`}
              className={`flex items-center gap-4 rounded-xl border border-l-4 border-zinc-200 bg-white px-5 py-4 shadow-sm transition-all hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 ${STATUS_BORDER[report.status]}`}
            >
              <div className="flex flex-1 flex-col gap-0.5 min-w-0">
                <div className="flex items-center gap-2">
                  <span className="font-semibold text-zinc-900 dark:text-zinc-100">
                    {report.vehicle_make} {report.vehicle_model}
                  </span>
                  <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ${s.bg} ${s.text}`}>
                    <span className={`h-1.5 w-1.5 rounded-full ${s.dot}`} />
                    {s.label}
                  </span>
                </div>
                <div className="flex items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
                  <span className="font-mono text-xs bg-zinc-100 dark:bg-zinc-800 rounded px-1.5 py-0.5">{report.license_plate}</span>
                  <span className="truncate max-w-xs">{report.description}</span>
                </div>
              </div>
              <div className="flex-shrink-0 text-right">
                {report.created_at && (
                  <time dateTime={report.created_at} className="block text-xs text-zinc-400">
                    {new Date(report.created_at).toLocaleDateString("en", {
                      day: "numeric", month: "short", year: "numeric",
                    })}
                  </time>
                )}
                <span className="mt-0.5 block text-xs text-zinc-300 dark:text-zinc-600">#{report.id}</span>
              </div>
            </Link>
          </li>
        );
      })}
    </ul>
  );
}
