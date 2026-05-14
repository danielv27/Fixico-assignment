import "server-only";

const apiBaseUrl = process.env.INTERNAL_API_BASE_URL ?? "http://localhost:8000";

export type ReportStatus = "draft" | "submitted" | "approved";

export type DamageReport = {
  id: number;
  vehicle_make: string;
  vehicle_model: string;
  license_plate: string;
  description: string;
  status: ReportStatus;
  created_at: string | null;
  updated_at: string | null;
};

export type ReportInput = {
  vehicle_make: string;
  vehicle_model: string;
  license_plate: string;
  description: string;
  status?: ReportStatus;
};

type Envelope<T> = { data: T };

export type ValidationErrors = Record<string, string[]>;

export class ReportValidationError extends Error {
  constructor(public readonly errors: ValidationErrors) {
    super("validation_failed");
  }
}

async function jsonRequest<T>(
  path: string,
  init: RequestInit = {},
): Promise<T> {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    ...init,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...init.headers,
    },
    cache: "no-store",
  });

  if (response.status === 422) {
    const body = (await response.json()) as { errors: ValidationErrors };
    throw new ReportValidationError(body.errors ?? {});
  }

  if (!response.ok) {
    throw new Error(
      `Report API ${init.method ?? "GET"} ${path} failed: ${response.status} ${response.statusText}`,
    );
  }

  return (await response.json()) as T;
}

export async function listReports(): Promise<DamageReport[]> {
  const payload = await jsonRequest<Envelope<DamageReport[]>>("/reports");
  return payload.data;
}

export async function getReport(id: number): Promise<DamageReport> {
  const payload = await jsonRequest<Envelope<DamageReport>>(`/reports/${id}`);
  return payload.data;
}

export async function createReport(
  input: ReportInput,
): Promise<DamageReport> {
  const payload = await jsonRequest<Envelope<DamageReport>>("/reports", {
    method: "POST",
    body: JSON.stringify(input),
  });
  return payload.data;
}

export async function updateReport(
  id: number,
  input: Partial<ReportInput>,
): Promise<DamageReport> {
  const payload = await jsonRequest<Envelope<DamageReport>>(`/reports/${id}`, {
    method: "PATCH",
    body: JSON.stringify(input),
  });
  return payload.data;
}
