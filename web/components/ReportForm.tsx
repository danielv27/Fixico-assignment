"use client";

import { useActionState } from "react";
import type { DamageReport, ReportStatus } from "@/lib/api/reports";
import type { FormState } from "@/app/reports/actions";

type Props = {
  action: (state: FormState, formData: FormData) => Promise<FormState>;
  initial?: DamageReport;
  submitLabel: string;
};

const STATUS_OPTIONS: { value: ReportStatus; label: string }[] = [
  { value: "draft", label: "Draft" },
  { value: "submitted", label: "Submitted" },
  { value: "approved", label: "Approved" },
];

export function ReportForm({ action, initial, submitLabel }: Props) {
  const [state, formAction, pending] = useActionState<FormState, FormData>(
    action,
    {},
  );

  return (
    <form action={formAction} className="flex flex-col gap-4">
      <Field
        name="vehicle_make"
        label="Vehicle make"
        defaultValue={initial?.vehicle_make}
        error={state.errors?.vehicle_make?.[0]}
      />
      <Field
        name="vehicle_model"
        label="Vehicle model"
        defaultValue={initial?.vehicle_model}
        error={state.errors?.vehicle_model?.[0]}
      />
      <Field
        name="license_plate"
        label="License plate"
        defaultValue={initial?.license_plate}
        error={state.errors?.license_plate?.[0]}
      />
      <div className="flex flex-col gap-1">
        <label htmlFor="description" className="text-sm font-medium">
          Description
        </label>
        <textarea
          id="description"
          name="description"
          rows={4}
          defaultValue={initial?.description}
          className="rounded border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
        />
        {state.errors?.description?.[0] && (
          <p className="text-sm text-red-600">{state.errors.description[0]}</p>
        )}
      </div>
      {initial && (
        <div className="flex flex-col gap-1">
          <label htmlFor="status" className="text-sm font-medium">
            Status
          </label>
          <select
            id="status"
            name="status"
            defaultValue={initial.status}
            className="rounded border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
          >
            {STATUS_OPTIONS.map((option) => (
              <option key={option.value} value={option.value}>
                {option.label}
              </option>
            ))}
          </select>
          {state.errors?.status?.[0] && (
            <p className="text-sm text-red-600">{state.errors.status[0]}</p>
          )}
        </div>
      )}
      <div className="flex items-center gap-3">
        <button
          type="submit"
          disabled={pending}
          className="rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
        >
          {pending ? "Saving…" : submitLabel}
        </button>
        {state.message && (
          <span className="text-sm text-emerald-700 dark:text-emerald-400">
            {state.message}
          </span>
        )}
      </div>
    </form>
  );
}

function Field({
  name,
  label,
  defaultValue,
  error,
}: {
  name: string;
  label: string;
  defaultValue?: string;
  error?: string;
}) {
  return (
    <div className="flex flex-col gap-1">
      <label htmlFor={name} className="text-sm font-medium">
        {label}
      </label>
      <input
        id={name}
        name={name}
        type="text"
        defaultValue={defaultValue}
        className="rounded border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
      />
      {error && <p className="text-sm text-red-600">{error}</p>}
    </div>
  );
}
