import Link from "next/link";
import { ReportForm } from "@/components/ReportForm";
import { NewReportBanner } from "@/components/NewReportBanner";
import { createReportAction } from "@/app/reports/actions";

export default function NewReportPage() {
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
          New damage report
        </h1>
      </div>

      {/* Conditional component #3 — gated by report.new_form_layout (NL, 50 %) */}
      <NewReportBanner />

      <ReportForm action={createReportAction} submitLabel="Submit report" />
    </main>
  );
}
