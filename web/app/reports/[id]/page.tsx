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

export default async function ReportDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
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
    <main className="mx-auto w-full max-w-2xl flex-1 px-6 py-10">
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
        <div className="mt-2 flex items-center gap-3">
          <h1 className="text-2xl font-semibold tracking-tight">
            {report.vehicle_make} {report.vehicle_model}
          </h1>
          <span className={`rounded-full px-2.5 py-0.5 text-xs font-medium ${STATUS_COLORS[report.status]}`}>
            {STATUS_LABELS[report.status]}
          </span>
        </div>
        <p className="mt-1 flex items-center gap-2 text-sm text-zinc-500">
          <span className="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-xs dark:bg-zinc-800">
            {report.license_plate}
          </span>
          <span>·</span>
          <span>Report #{report.id}</span>
        </p>
      </div>

      <ReportForm action={action} initial={report} submitLabel="Save changes" />

      {/* Conditional component #2 — photo attachments (25 % rollout) */}
      <div className="mt-8">
        <PhotoAttachments reportId={reportId} />
      </div>
    </main>
  );
}
