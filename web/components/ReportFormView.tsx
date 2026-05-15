"use client";

import { type ReactNode } from "react";
import { useFeatureFlag } from "@/lib/feature-flags/context";
import { ReportForm } from "@/components/ReportForm";
import type { DamageReport } from "@/lib/reports/repository";
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
