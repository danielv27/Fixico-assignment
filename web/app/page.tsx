import Link from "next/link";
import { ReportList } from "@/components/ReportList";
import { BulkActionsToolbar } from "@/components/BulkActionsToolbar";
import { listReports } from "@/lib/reports/repository";

export default function Home() {
  const reports = listReports();
  const ids = reports.map((r) => r.id);

  return (
    <main className="mx-auto w-full max-w-3xl flex-1 px-6 py-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-xl font-bold tracking-tight">Damage Reports</h1>
            <p className="mt-0.5 text-sm text-zinc-500">
              {reports.length === 0
                ? "No reports yet"
                : `${reports.length} report${reports.length === 1 ? "" : "s"} · most recent first`}
            </p>
          </div>
          <Link
            href="/reports/new"
            className="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-emerald-700 hover:shadow-md active:scale-95"
          >
            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
            </svg>
            New report
          </Link>
        </div>

        {ids.length > 0 && (
          <div className="mt-4">
            <BulkActionsToolbar reportIds={ids} />
          </div>
        )}

        <div className="mt-4">
          <ReportList reports={reports} />
        </div>
    </main>
  );
}
