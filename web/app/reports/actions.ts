"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import db from "@/lib/db";
import { isFeatureEnabledForCurrentUser } from "@/lib/feature-flags/server";
import {
  ALLOWED_STATUSES,
  bulkDeleteReports,
  createReport,
  ReportValidationError,
  updateReport,
  type ReportInput,
  type ReportStatus,
  type ValidationErrors,
} from "@/lib/api/reports";

export type FormState = {
  errors?: ValidationErrors;
  message?: string;
};

type FeatureDisabledResult = {
  error: "feature_disabled";
  flag: string;
  message: string;
};

type BulkDeleteResult = { deleted: number } | FeatureDisabledResult;
type SavePhotosResult = { saved: true } | FeatureDisabledResult;

async function disabledResultFor(
  flag: string,
): Promise<FeatureDisabledResult | null> {
  if (await isFeatureEnabledForCurrentUser(flag)) {
    return null;
  }

  return {
    error: "feature_disabled",
    flag,
    message: "This feature is no longer available.",
  };
}

function parseInput(formData: FormData): ReportInput {
  const status = formData.get("status")?.toString();
  return {
    vehicle_make: formData.get("vehicle_make")?.toString() ?? "",
    vehicle_model: formData.get("vehicle_model")?.toString() ?? "",
    license_plate: formData.get("license_plate")?.toString() ?? "",
    description: formData.get("description")?.toString() ?? "",
    ...(status && ALLOWED_STATUSES.includes(status as ReportStatus)
      ? { status: status as ReportStatus }
      : {}),
  };
}

export async function createReportAction(
  _previous: FormState,
  formData: FormData,
): Promise<FormState> {
  try {
    const report = createReport(parseInput(formData));
    revalidatePath("/");
    redirect(`/reports/${report.id}`);
  } catch (error) {
    if (error instanceof ReportValidationError) {
      return { errors: error.errors };
    }
    throw error;
  }
}

export async function updateReportAction(
  id: number,
  _previous: FormState,
  formData: FormData,
): Promise<FormState> {
  try {
    updateReport(id, parseInput(formData));
    revalidatePath("/");
    revalidatePath(`/reports/${id}`);
    return { message: "Saved." };
  } catch (error) {
    if (error instanceof ReportValidationError) {
      return { errors: error.errors };
    }
    throw error;
  }
}

export async function bulkDeleteAction(
  ids: number[],
): Promise<BulkDeleteResult> {
  const disabled = await disabledResultFor("reports.bulk_actions");
  if (disabled) return disabled;

  const deleted = bulkDeleteReports(ids);
  revalidatePath("/");
  return { deleted };
}

export async function savePhotosAction(
  reportId: number,
  urls: string[],
): Promise<SavePhotosResult> {
  const disabled = await disabledResultFor("reports.photo_attachments");
  if (disabled) return disabled;

  db.prepare(
    "UPDATE damage_reports SET photos = ?, updated_at = strftime('%Y-%m-%dT%H:%M:%fZ', 'now') WHERE id = ?",
  ).run(JSON.stringify(urls), reportId);
  revalidatePath(`/reports/${reportId}`);

  return { saved: true };
}
