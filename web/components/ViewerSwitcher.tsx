"use client";

import { useTransition } from "react";
import { setViewerProfile } from "@/lib/viewer/actions";
import { COUNTRIES_OPTIONS, ROLE_OPTIONS } from "@/lib/viewer/constants";
import type { ViewerProfile } from "@/lib/viewer/profile";

type Props = { profile: ViewerProfile };

export function ViewerSwitcher({ profile }: Props) {
  const [pending, startTransition] = useTransition();

  function handleChange(e: React.ChangeEvent<HTMLSelectElement>) {
    const form = e.currentTarget.closest("form");
    if (!form) return;
    startTransition(async () => {
      await setViewerProfile(new FormData(form));
    });
  }

  return (
    <div className="flex items-center gap-2">
      {/* Pill — country · role */}
      <form
        className={`flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-2.5 py-1 text-xs transition-opacity dark:border-zinc-700 dark:bg-zinc-900 ${
          pending ? "opacity-60" : ""
        }`}
      >
        <span className="font-medium text-zinc-500 dark:text-zinc-400">
          Viewing as
        </span>

        <select
          name="country"
          defaultValue={profile.country}
          onChange={handleChange}
          disabled={pending}
          className="border-0 bg-transparent font-semibold text-zinc-700 focus:outline-none focus:ring-0 dark:text-zinc-300"
        >
          {COUNTRIES_OPTIONS.map((c) => (
            <option key={c} value={c}>
              {c}
            </option>
          ))}
        </select>

        <span className="text-zinc-300 dark:text-zinc-600">·</span>

        <select
          name="role"
          defaultValue={profile.role}
          onChange={handleChange}
          disabled={pending}
          className="border-0 bg-transparent font-semibold text-zinc-700 focus:outline-none focus:ring-0 dark:text-zinc-300"
        >
          {ROLE_OPTIONS.map((r) => (
            <option key={r} value={r}>
              {r}
            </option>
          ))}
        </select>
      </form>

      {/* Info icon — far right, explains the demo switcher */}
      <div className="group relative">
        <button
          type="button"
          className="flex h-5 w-5 items-center justify-center rounded-full border border-zinc-200 text-zinc-400 transition-colors hover:border-zinc-400 hover:text-zinc-600 dark:border-zinc-600 dark:hover:border-zinc-500"
          aria-label="About demo viewer"
        >
          <svg className="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
            <path
              fillRule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clipRule="evenodd"
            />
          </svg>
        </button>

        {/* Tooltip — anchored to the right so it doesn't overflow */}
        <div className="pointer-events-none absolute right-0 top-7 z-50 w-64 rounded-lg border border-zinc-200 bg-white p-3 text-xs text-zinc-600 opacity-0 shadow-lg transition-opacity group-hover:opacity-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
          <p className="font-semibold text-zinc-800 dark:text-zinc-200">
            Demo viewer context
          </p>
          <p className="mt-1 leading-relaxed">
            Simulates who is using the app — stands in for a real auth
            session. Flip country or role to exercise flag targeting rules
            (e.g. <code className="font-mono">country=NL</code> unlocks the
            new report layout).
          </p>
          <p className="mt-1.5 text-zinc-400">
            This control exists only in this demo.
          </p>
        </div>
      </div>
    </div>
  );
}
