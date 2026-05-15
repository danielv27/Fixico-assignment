import "server-only";
import db from "@/lib/db";

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

export type ValidationErrors = Record<string, string[]>;

export class ReportValidationError extends Error {
  constructor(public readonly errors: ValidationErrors) {
    super("validation_failed");
  }
}

export const ALLOWED_STATUSES: ReportStatus[] = ["draft", "submitted", "approved"];

function validate(input: ReportInput): ValidationErrors | null {
  const errors: ValidationErrors = {};
  if (!input.vehicle_make?.trim()) errors.vehicle_make = ["The vehicle make field is required."];
  if (!input.vehicle_model?.trim()) errors.vehicle_model = ["The vehicle model field is required."];
  if (!input.license_plate?.trim()) errors.license_plate = ["The license plate field is required."];
  if (!input.description?.trim()) errors.description = ["The description field is required."];
  if (input.description && input.description.length > 2000)
    errors.description = ["The description may not be greater than 2000 characters."];
  return Object.keys(errors).length ? errors : null;
}

type Row = {
  id: number;
  vehicle_make: string;
  vehicle_model: string;
  license_plate: string;
  description: string;
  status: string;
  created_at: string;
  updated_at: string;
};

function mapRow(r: Row): DamageReport {
  return {
    id: r.id,
    vehicle_make: r.vehicle_make,
    vehicle_model: r.vehicle_model,
    license_plate: r.license_plate,
    description: r.description,
    status: r.status as ReportStatus,
    created_at: r.created_at,
    updated_at: r.updated_at,
  };
}

export function listReports(): DamageReport[] {
  const rows = db
    .prepare("SELECT * FROM damage_reports ORDER BY created_at DESC")
    .all() as Row[];
  return rows.map(mapRow);
}

export function getReport(id: number): DamageReport {
  const row = db
    .prepare("SELECT * FROM damage_reports WHERE id = ?")
    .get(id) as Row | undefined;
  if (!row) throw new Error(`Report ${id} not found`);
  return mapRow(row);
}

export function createReport(input: ReportInput): DamageReport {
  const errors = validate(input);
  if (errors) throw new ReportValidationError(errors);
  const status =
    input.status && ALLOWED_STATUSES.includes(input.status) ? input.status : "draft";
  const result = db
    .prepare(
      `INSERT INTO damage_reports (vehicle_make, vehicle_model, license_plate, description, status)
       VALUES (?, ?, ?, ?, ?)`,
    )
    .run(
      input.vehicle_make.trim(),
      input.vehicle_model.trim(),
      input.license_plate.trim(),
      input.description.trim(),
      status,
    );
  return getReport(result.lastInsertRowid as number);
}

export function updateReport(id: number, input: Partial<ReportInput>): DamageReport {
  const current = getReport(id);
  const merged: ReportInput = {
    vehicle_make: input.vehicle_make ?? current.vehicle_make,
    vehicle_model: input.vehicle_model ?? current.vehicle_model,
    license_plate: input.license_plate ?? current.license_plate,
    description: input.description ?? current.description,
    status: input.status ?? current.status,
  };
  const errors = validate(merged);
  if (errors) throw new ReportValidationError(errors);
  const status =
    merged.status && ALLOWED_STATUSES.includes(merged.status) ? merged.status : current.status;
  db.prepare(
    `UPDATE damage_reports
     SET vehicle_make = ?, vehicle_model = ?, license_plate = ?, description = ?, status = ?,
         updated_at = strftime('%Y-%m-%dT%H:%M:%fZ', 'now')
     WHERE id = ?`,
  ).run(
    merged.vehicle_make.trim(),
    merged.vehicle_model.trim(),
    merged.license_plate.trim(),
    merged.description.trim(),
    status,
    id,
  );
  return getReport(id);
}

export function bulkDeleteReports(ids: number[]): number {
  if (ids.length === 0) return 0;
  const placeholders = ids.map(() => "?").join(", ");
  const result = db
    .prepare(`DELETE FROM damage_reports WHERE id IN (${placeholders})`)
    .run(...ids);
  return result.changes;
}
