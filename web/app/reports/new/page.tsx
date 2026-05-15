import type { Metadata } from "next";
import Link from "next/link";
import { ReportFormView } from "@/components/ReportFormView";
import { createReportAction } from "@/app/reports/actions";

export const metadata: Metadata = {
  title: "New Report — Fixico",
};

export default function NewReportPage() {
  return (
    <main className="mx-auto w-full max-w-2xl flex-1 px-6 py-8">
      <div className="mb-6">
        <Link
          href="/"
          className="inline-flex items-center gap-1 text-sm text-zinc-500 transition-colors hover:text-zinc-800"
        >
          <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
          </svg>
          All reports
        </Link>
        <h1 className="mt-2 text-xl font-bold tracking-tight">Submit damage report</h1>
        <p className="mt-0.5 text-sm text-zinc-500">Fill in the details of the damage for assessment.</p>
      </div>

      <ReportFormView action={createReportAction} submitLabel="Submit report" />
    </main>
  );
}
