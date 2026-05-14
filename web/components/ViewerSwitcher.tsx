"use client";

import { useTransition } from "react";
import { setViewerProfile } from "@/lib/viewer/actions";
import { COUNTRIES_OPTIONS, ROLE_OPTIONS } from "@/lib/viewer/constants";
import type { ViewerProfile } from "@/lib/viewer/profile";

type Props = { profile: ViewerProfile };

/**
 * Demo control — stands in for an authenticated session.
 * Lets the reviewer flip country and role to exercise flag targeting.
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
    <form className="flex items-center gap-2">
      <span className="text-xs font-medium text-zinc-400 dark:text-zinc-500">
        Demo viewer
      </span>
      <div className={`flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-2.5 py-1 transition-opacity dark:border-zinc-700 dark:bg-zinc-900 ${pending ? "opacity-60" : ""}`}>
        <select
          name="country"
          defaultValue={profile.country}
          onChange={handleChange}
          disabled={pending}
          className="border-0 bg-transparent text-xs font-medium text-zinc-700 focus:outline-none focus:ring-0 dark:text-zinc-300"
        >
          {COUNTRIES_OPTIONS.map((c) => (
            <option key={c} value={c}>{c}</option>
          ))}
        </select>
        <span className="text-zinc-300 dark:text-zinc-600">·</span>
        <select
          name="role"
          defaultValue={profile.role}
          onChange={handleChange}
          disabled={pending}
          className="border-0 bg-transparent text-xs font-medium text-zinc-700 focus:outline-none focus:ring-0 dark:text-zinc-300"
        >
          {ROLE_OPTIONS.map((r) => (
            <option key={r} value={r}>{r}</option>
          ))}
        </select>
      </div>
    </form>
  );
}
