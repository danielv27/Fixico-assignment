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
      <main className="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 px-6 py-12">
        <header className="flex items-center justify-between">
          <h1 className="text-3xl font-semibold tracking-tight">Damage Reports</h1>
          <Link
            href="/reports/new"
            className="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            New report
          </Link>
        </header>

        {/* Conditional component #1 — gated by reports.bulk_actions (role = admin) */}
        <BulkActionsToolbar reportIds={ids} />

        <ReportList reports={reports} />
      </main>
    </>
  );
}
