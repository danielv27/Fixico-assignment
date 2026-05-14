"use client";

import { useActionState } from "react";
import type { DamageReport, ReportStatus } from "@/lib/api/reports";
import type { FormState } from "@/app/reports/actions";

type Props = {
  action: (state: FormState, formData: FormData) => Promise<FormState>;
  initial?: DamageReport;
  submitLabel: string;
};

const STATUS_OPTIONS: { value: ReportStatus; label: string; description: string }[] = [
  { value: "draft", label: "Draft", description: "Report is being prepared" },
  { value: "submitted", label: "Submitted", description: "Submitted for review" },
  { value: "approved", label: "Approved", description: "Assessment approved" },
];

export function ReportForm({ action, initial, submitLabel }: Props) {
  const [state, formAction, pending] = useActionState<FormState, FormData>(action, {});

  return (
    <form action={formAction} className="flex flex-col gap-5">

      {/* Vehicle details */}
      <fieldset className="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <legend className="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">
          Vehicle
        </legend>
        <div className="grid grid-cols-2 gap-4">
          <Field
            name="vehicle_make"
            label="Make"
            placeholder="Volkswagen"
            defaultValue={initial?.vehicle_make}
            error={state.errors?.vehicle_make?.[0]}
          />
          <Field
            name="vehicle_model"
            label="Model"
            placeholder="Golf"
            defaultValue={initial?.vehicle_model}
            error={state.errors?.vehicle_model?.[0]}
          />
        </div>
        <div className="mt-4">
          <Field
            name="license_plate"
            label="License plate"
            placeholder="AB-123-CD"
            defaultValue={initial?.license_plate}
            error={state.errors?.license_plate?.[0]}
            mono
          />
        </div>
      </fieldset>

      {/* Damage description */}
      <fieldset className="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <legend className="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">
          Damage
        </legend>
        <div className="flex flex-col gap-1.5">
          <label htmlFor="description" className="text-sm font-medium text-zinc-700 dark:text-zinc-300">
            Description
          </label>
          <textarea
            id="description"
            name="description"
            rows={3}
            defaultValue={initial?.description}
            placeholder="Describe the damage — location, severity, circumstances…"
            className="rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm placeholder-zinc-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
          />
          {state.errors?.description?.[0] && (
            <p className="text-xs text-red-600">{state.errors.description[0]}</p>
          )}
        </div>
      </fieldset>

      {/* Status (edit only) */}
      {initial && (
        <fieldset className="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
          <legend className="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">
            Status
          </legend>
          <div className="grid grid-cols-3 gap-2">
            {STATUS_OPTIONS.map((opt) => (
              <label
                key={opt.value}
                className="relative flex cursor-pointer flex-col gap-0.5 rounded-lg border border-zinc-200 bg-zinc-50 p-3 transition-all has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 dark:border-zinc-700 dark:bg-zinc-800 dark:has-[:checked]:border-emerald-600 dark:has-[:checked]:bg-emerald-950/40"
              >
                <input
                  type="radio"
                  name="status"
                  value={opt.value}
                  defaultChecked={initial.status === opt.value}
                  className="sr-only"
                />
                <span className="text-sm font-medium text-zinc-800 dark:text-zinc-200">{opt.label}</span>
                <span className="text-xs text-zinc-500 dark:text-zinc-400">{opt.description}</span>
              </label>
            ))}
          </div>
          {state.errors?.status?.[0] && (
            <p className="mt-2 text-xs text-red-600">{state.errors.status[0]}</p>
          )}
        </fieldset>
      )}

      {/* Actions */}
      <div className="flex items-center gap-3">
        <button
          type="submit"
          disabled={pending}
          className="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
        >
          {pending ? (
            <span className="flex items-center gap-2">
              <svg className="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Saving…
            </span>
          ) : submitLabel}
        </button>

        {state.message && (
          <span className="flex items-center gap-1.5 text-sm text-emerald-700 dark:text-emerald-400">
            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7"/>
            </svg>
            {state.message}
          </span>
        )}
      </div>
    </form>
  );
}

function Field({
  name, label, placeholder, defaultValue, error, mono,
}: {
  name: string; label: string; placeholder?: string;
  defaultValue?: string; error?: string; mono?: boolean;
}) {
  return (
    <div className="flex flex-col gap-1.5">
      <label htmlFor={name} className="text-sm font-medium text-zinc-700 dark:text-zinc-300">
        {label}
      </label>
      <input
        id={name}
        name={name}
        type="text"
        defaultValue={defaultValue}
        placeholder={placeholder}
        className={`rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm placeholder-zinc-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 ${error ? "border-red-400 focus:border-red-400 focus:ring-red-400" : ""} ${mono ? "font-mono" : ""}`}
      />
      {error && <p className="text-xs text-red-600">{error}</p>}
    </div>
  );
}
