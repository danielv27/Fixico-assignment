import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ReportForm } from "@/components/ReportForm";
import { PhotoAttachments } from "@/components/PhotoAttachments";
import { updateReportAction } from "@/app/reports/actions";
import { getReport } from "@/lib/api/reports";

const STATUS_LABELS = { draft: "Draft", submitted: "Submitted", approved: "Approved" } as const;
const STATUS_COLORS = {
  draft: "bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400",
  submitted: "bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300",
  approved: "bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300",
} as const;

type Props = { params: Promise<{ id: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { id } = await params;
  try {
    const report = await import("@/lib/api/reports").then(m => m.getReport(Number(id)));
    return { title: `#${report.id} · ${report.vehicle_make} ${report.vehicle_model} — Fixico` };
  } catch {
    return { title: "Report — Fixico" };
  }
}

export default async function ReportDetailPage({ params }: Props) {
  const { id } = await params;
  const reportId = Number(id);
  if (!Number.isInteger(reportId)) notFound();

  let report;
  try {
    report = await getReport(reportId);
  } catch {
    notFound();
  }

  const action = updateReportAction.bind(null, reportId);

  return (
    <main className="mx-auto w-full max-w-2xl flex-1 px-6 py-8">
      <div className="mb-6">
        <Link
          href="/"
          className="inline-flex items-center gap-1 text-sm text-zinc-500 transition-colors hover:text-zinc-800 dark:hover:text-zinc-200"
        >
          <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
          </svg>
          All reports
        </Link>

        <div className="mt-2 flex items-start justify-between gap-4">
          <div>
            <div className="flex items-center gap-2.5">
              <h1 className="text-xl font-bold tracking-tight">
                {report.vehicle_make} {report.vehicle_model}
              </h1>
              <span className={`rounded-full px-2.5 py-0.5 text-xs font-semibold ${STATUS_COLORS[report.status]}`}>
                {STATUS_LABELS[report.status]}
              </span>
            </div>
            <p className="mt-1 flex items-center gap-2 text-sm text-zinc-500">
              <span className="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-xs dark:bg-zinc-800">
                {report.license_plate}
              </span>
              <span>·</span>
              <span>Report #{report.id}</span>
              {report.updated_at && (
                <>
                  <span>·</span>
                  <span>Updated {new Date(report.updated_at).toLocaleDateString("en", { day: "numeric", month: "short", year: "numeric" })}</span>
                </>
              )}
            </p>
          </div>
        </div>
      </div>

      <ReportForm action={action} initial={report} submitLabel="Save changes" />

      <div className="mt-8">
        <PhotoAttachments reportId={reportId} />
      </div>
    </main>
  );
}
