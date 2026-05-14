import Link from "next/link";
import type { DamageReport } from "@/lib/api/reports";

const STATUS_CONFIG: Record<
  DamageReport["status"],
  { label: string; badge: string; border: string; dot: string }
> = {
  draft: {
    label: "Draft",
    badge: "bg-zinc-100 text-zinc-600",
    border: "border-l-zinc-300",
    dot: "bg-zinc-400",
  },
  submitted: {
    label: "Submitted",
    badge: "bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200",
    border: "border-l-blue-500",
    dot: "bg-blue-500",
  },
  approved: {
    label: "Approved",
    badge: "bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200",
    border: "border-l-emerald-500",
    dot: "bg-emerald-500",
  },
};

export function ReportList({ reports }: { reports: DamageReport[] }) {
  if (reports.length === 0) {
    return (
      <div className="rounded-xl border border-dashed border-zinc-300 bg-white px-6 py-16 text-center">
        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100">
          <svg className="h-6 w-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <p className="mt-3 text-sm font-semibold text-zinc-700">No damage reports yet</p>
        <p className="mt-1 text-sm text-zinc-500">Submit your first report to get started.</p>
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
              className={`group flex items-stretch rounded-xl border border-l-[3px] border-zinc-200 bg-white shadow-sm transition-all hover:shadow-md hover:-translate-y-px ${s.border}`}
            >
              <div className="flex flex-1 flex-col gap-1.5 px-5 py-4">
                <div className="flex items-center gap-2.5">
                  <span className="font-semibold text-zinc-900 group-hover:text-emerald-700 transition-colors">
                    {report.vehicle_make} {report.vehicle_model}
                  </span>
                  <span className={`rounded-full px-2 py-0.5 text-xs font-semibold ${s.badge}`}>
                    {s.label}
                  </span>
                </div>
                <div className="flex items-center gap-2.5 text-sm text-zinc-500">
                  <span className="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-xs font-medium">
                    {report.license_plate}
                  </span>
                  <span className="truncate max-w-xs">{report.description}</span>
                </div>
              </div>

              <div className="flex flex-shrink-0 flex-col items-end justify-center gap-0.5 border-l border-zinc-100 px-4 py-4">
                {report.created_at && (
                  <time dateTime={report.created_at} className="text-xs text-zinc-400">
                    {new Date(report.created_at).toLocaleDateString("en", {
                      day: "numeric", month: "short",
                    })}
                  </time>
                )}
                <span className="text-xs font-medium text-zinc-300">#{report.id}</span>
              </div>
            </Link>
          </li>
        );
      })}
    </ul>
  );
}
