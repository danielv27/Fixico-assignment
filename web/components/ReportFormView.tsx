"use client";

import { type ReactNode } from "react";
import { useFeatureFlag } from "@/lib/flags/context";
import { ReportForm } from "@/components/ReportForm";
import type { DamageReport } from "@/lib/api/reports";
import type { FormState } from "@/app/reports/actions";

type Props = {
  action: (state: FormState, formData: FormData) => Promise<FormState>;
  initial?: DamageReport;
  submitLabel: string;
  children?: ReactNode;
};

export function ReportFormView({ action, initial, submitLabel, children }: Props) {
  const descriptionFirst = useFeatureFlag("form.description_first");
  return (
    <ReportForm action={action} initial={initial} submitLabel={submitLabel} descriptionFirst={descriptionFirst}>
      {children}
    </ReportForm>
  );
}
