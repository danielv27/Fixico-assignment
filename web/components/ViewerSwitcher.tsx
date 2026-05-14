"use client";

import { useTransition } from "react";
import { setViewerProfile } from "@/lib/viewer/actions";
import { COUNTRIES_OPTIONS, ROLE_OPTIONS } from "@/lib/viewer/constants";
import type { ViewerProfile } from "@/lib/viewer/profile";

type Props = { profile: ViewerProfile };

/**
 * Demo control — stands in for an authenticated user session.
 * Lets the reviewer flip country and role to exercise flag targeting without
 * building a full auth system. Mentioned explicitly as a demo affordance in
 * the README.
 */
export function ViewerSwitcher({ profile }: Props) {
  const [pending, startTransition] = useTransition();

  function handleChange(e: React.ChangeEvent<HTMLSelectElement>) {
    const form = e.currentTarget.closest("form");
    if (!form) return;
    const data = new FormData(form);
    startTransition(async () => {
      await setViewerProfile(data);
    });
  }

  return (
    <form className="flex items-center gap-2 text-xs text-zinc-500">
      <span className="font-medium">Demo viewer:</span>
      <select
        name="country"
        defaultValue={profile.country}
        onChange={handleChange}
        disabled={pending}
        className="rounded border border-zinc-300 bg-white px-1.5 py-0.5 text-xs dark:border-zinc-700 dark:bg-zinc-900"
      >
        {COUNTRIES_OPTIONS.map((c) => (
          <option key={c} value={c}>
            {c}
          </option>
        ))}
      </select>

      <select
        name="role"
        defaultValue={profile.role}
        onChange={handleChange}
        disabled={pending}
        className="rounded border border-zinc-300 bg-white px-1.5 py-0.5 text-xs dark:border-zinc-700 dark:bg-zinc-900"
      >
        {ROLE_OPTIONS.map((r) => (
          <option key={r} value={r}>
            {r}
          </option>
        ))}
      </select>

      {pending && <span className="text-zinc-400">…</span>}
    </form>
  );
}
