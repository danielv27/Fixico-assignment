"use client";

import { useActionState } from "react";
import type { FeatureFlag } from "@/lib/api/flags";
import type { FormState } from "@/app/admin/flags/actions";

type Props = {
  action: (state: FormState, formData: FormData) => Promise<FormState>;
  initial?: FeatureFlag;
  submitLabel: string;
};

export function FlagForm({ action, initial, submitLabel }: Props) {
  const [state, formAction, pending] = useActionState<FormState, FormData>(
    action,
    {},
  );

  return (
    <form action={formAction} className="flex flex-col gap-4">
      {!initial && (
        <div className="flex flex-col gap-1">
          <label htmlFor="name" className="text-sm font-medium">
            Name{" "}
            <span className="font-normal text-zinc-500">
              (lowercase, dots, hyphens — e.g. reports.bulk_delete)
            </span>
          </label>
          <input
            id="name"
            name="name"
            type="text"
            defaultValue=""
            placeholder="reports.my_feature"
            className="rounded border border-zinc-300 bg-white px-3 py-2 text-sm font-mono dark:border-zinc-700 dark:bg-zinc-900"
          />
          {state.errors?.name?.[0] && (
            <p className="text-sm text-red-600">{state.errors.name[0]}</p>
          )}
        </div>
      )}

      <div className="flex flex-col gap-1">
        <label htmlFor="description" className="text-sm font-medium">
          Description
        </label>
        <textarea
          id="description"
          name="description"
          rows={2}
          defaultValue={initial?.description ?? ""}
          className="rounded border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
        />
        {state.errors?.description?.[0] && (
          <p className="text-sm text-red-600">{state.errors.description[0]}</p>
        )}
      </div>

      <input type="hidden" name="enabled" value="false" />
      <label className="flex cursor-pointer items-center gap-3">
        <input
          type="checkbox"
          name="enabled"
          value="true"
          defaultChecked={initial?.enabled ?? false}
          className="h-4 w-4 rounded border-zinc-300"
          onChange={(e) => {
            const hidden = e.currentTarget
              .closest("form")
              ?.querySelector<HTMLInputElement>('input[name="enabled"][type="hidden"]');
            if (hidden) hidden.value = e.currentTarget.checked ? "true" : "false";
          }}
        />
        <span className="text-sm font-medium">Enabled</span>
      </label>

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
