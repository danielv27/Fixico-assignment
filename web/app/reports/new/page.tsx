import Link from "next/link";
import { ReportForm } from "@/components/ReportForm";
import { NewReportBanner } from "@/components/NewReportBanner";
import { createReportAction } from "@/app/reports/actions";

export default function NewReportPage() {
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
        <h1 className="mt-2 text-2xl font-semibold tracking-tight">New damage report</h1>
      </div>

      {/* Conditional component #3 — new form layout (country = NL, 50 % rollout) */}
      <NewReportBanner />

      <div className="mt-5">
        <ReportForm action={createReportAction} submitLabel="Submit report" />
      </div>
    </main>
  );
}
