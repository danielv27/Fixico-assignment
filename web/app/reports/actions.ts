"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import {
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

const ALLOWED_STATUSES: ReportStatus[] = ["draft", "submitted", "approved"];

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
    const report = await createReport(parseInput(formData));
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
    await updateReport(id, parseInput(formData));
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
): Promise<{ deleted: number }> {
  const deleted = await bulkDeleteReports(ids);
  revalidatePath("/");
  return { deleted };
}

export async function savePhotosAction(
  reportId: number,
  urls: string[],
): Promise<void> {
  const db = (await import("@/lib/db")).default;
  db.prepare(
    "UPDATE damage_reports SET photos = ?, updated_at = strftime('%Y-%m-%dT%H:%M:%fZ', 'now') WHERE id = ?",
  ).run(JSON.stringify(urls), reportId);
  revalidatePath(`/reports/${reportId}`);
}
