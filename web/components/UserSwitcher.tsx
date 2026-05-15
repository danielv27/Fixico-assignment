"use client";

import { useTransition } from "react";
import { setUserProfile, resetUser } from "@/lib/viewer/actions";
import { COUNTRIES_OPTIONS, ROLE_OPTIONS } from "@/lib/viewer/constants";
import type { UserProfile } from "@/lib/viewer/profile";

type Props = { profile: UserProfile; bucket: number };

export function UserSwitcher({ profile, bucket }: Props) {
  const [pending, startTransition] = useTransition();

  function handleChange(e: React.ChangeEvent<HTMLSelectElement>) {
    const form = e.currentTarget.closest("form");
    if (!form) return;
    startTransition(async () => {
      await setUserProfile(new FormData(form));
    });
  }

  return (
    <div className="flex items-center gap-2">
      <form
        className={`flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-2.5 py-1 text-xs transition-opacity ${
          pending ? "opacity-60" : ""
        }`}
      >
        <span className="font-medium text-zinc-500">
          Viewing as
        </span>

        <select
          name="country"
          defaultValue={profile.country}
          onChange={handleChange}
          disabled={pending}
          className="border-0 bg-transparent font-semibold text-zinc-700 focus:outline-none focus:ring-0"
        >
          {COUNTRIES_OPTIONS.map((c) => (
            <option key={c} value={c}>
              {c}
            </option>
          ))}
        </select>

        <span className="text-zinc-300">·</span>

        <select
          name="role"
          defaultValue={profile.role}
          onChange={handleChange}
          disabled={pending}
          className="border-0 bg-transparent font-semibold text-zinc-700 focus:outline-none focus:ring-0"
        >
          {ROLE_OPTIONS.map((r) => (
            <option key={r} value={r}>
              {r}
            </option>
          ))}
        </select>

        <span className="text-zinc-300">·</span>

        <button
          type="button"
          onClick={() => startTransition(() => resetUser())}
          disabled={pending}
          title="Re-roll rollout bucket"
          className="flex items-center gap-1 font-semibold text-zinc-500 transition-colors hover:text-zinc-800 disabled:opacity-40"
        >
          <svg className="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round">
            <polyline points="17 1 21 5 17 9" />
            <path d="M3 11V9a4 4 0 0 1 4-4h14" />
            <polyline points="7 23 3 19 7 15" />
            <path d="M21 13v2a4 4 0 0 1-4 4H3" />
          </svg>
          <span>bucket <span className="tabular-nums text-zinc-800">{bucket}</span></span>
        </button>
      </form>

      <div className="group relative">
        <button
          type="button"
          className="flex h-5 w-5 items-center justify-center rounded-full border border-zinc-200 text-zinc-400 transition-colors hover:border-zinc-400 hover:text-zinc-600"
          aria-label="About demo user"
        >
          <svg className="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
            <path
              fillRule="evenodd"
              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
              clipRule="evenodd"
            />
          </svg>
        </button>

        <div className="pointer-events-none absolute right-0 top-7 z-50 w-64 rounded-lg border border-zinc-200 bg-white p-3 text-xs text-zinc-600 opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
          <p className="font-semibold text-zinc-800">
            Demo user context
          </p>
          <p className="mt-1 leading-relaxed">
            Simulates who is using the app — stands in for a real auth session.
            Flip country or role to exercise attribute-based targeting. The
            bucket number (0–99) determines which percentage-gated flags you
            see — shuffle to land in a different one.
          </p>
          <p className="mt-1.5 text-zinc-400">
            This control exists only in this demo.
          </p>
        </div>
      </div>
    </div>
  );
}
