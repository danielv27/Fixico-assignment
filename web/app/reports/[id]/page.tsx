import Link from "next/link";
import { notFound } from "next/navigation";
import { ReportForm } from "@/components/ReportForm";
import { PhotoAttachments } from "@/components/PhotoAttachments";
import { updateReportAction } from "@/app/reports/actions";
import { getReport } from "@/lib/api/reports";

export default async function ReportDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const reportId = Number(id);
  if (!Number.isInteger(reportId)) {
    notFound();
  }

  let report;
  try {
    report = await getReport(reportId);
  } catch {
    notFound();
  }

  const action = updateReportAction.bind(null, reportId);

  return (
    <main className="mx-auto flex w-full max-w-2xl flex-1 flex-col gap-6 px-6 py-12">
      <div>
        <Link
          href="/"
          className="text-sm text-zinc-600 underline dark:text-zinc-400"
        >
          ← Back to reports
        </Link>
        <h1 className="mt-2 text-3xl font-semibold tracking-tight">
          Report #{report.id}
        </h1>
        <p className="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
          {report.vehicle_make} {report.vehicle_model} ·{" "}
          <span className="font-mono">{report.license_plate}</span>
        </p>
      </div>

      <ReportForm action={action} initial={report} submitLabel="Save changes" />

      {/* Conditional component #2 — gated by reports.photo_attachments (25 % rollout) */}
      <hr className="border-zinc-200 dark:border-zinc-800" />
      <PhotoAttachments reportId={reportId} />
    </main>
  );
}
