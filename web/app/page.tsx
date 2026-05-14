import Link from "next/link";
import { DemoBanner } from "@/components/DemoBanner";
import { ReportList } from "@/components/ReportList";
import { BulkActionsToolbar } from "@/components/BulkActionsToolbar";
import { listReports } from "@/lib/api/reports";

export default async function Home() {
  const reports = await listReports();
  const ids = reports.map((r) => r.id);

  return (
    <>
      <DemoBanner />
      <main className="mx-auto w-full max-w-3xl flex-1 px-6 py-10">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-semibold tracking-tight">Damage Reports</h1>
            <p className="mt-0.5 text-sm text-zinc-500">
              {reports.length === 0
                ? "No reports yet"
                : `${reports.length} report${reports.length === 1 ? "" : "s"}`}
            </p>
          </div>
          <Link
            href="/reports/new"
            className="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700"
          >
            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
            </svg>
            New report
          </Link>
        </div>

        {/* Conditional component #1 — bulk actions toolbar (role = admin) */}
        <div className="mt-4">
          <BulkActionsToolbar reportIds={ids} />
        </div>

        <div className="mt-4">
          <ReportList reports={reports} />
        </div>
      </main>
    </>
  );
}
