"use client";

import { useState, useTransition } from "react";
import { useFlag } from "@/lib/flags/context";

type Props = { reportId: number };

/**
 * Conditional component #2 — rendered only when 'reports.photo_attachments' is on.
 *
 * Server-side flag: reports.photo_attachments
 * Targeting: all users, 25 % rollout
 *
 * In this demo the photos are stored as a simple newline-separated list of
 * URLs. The "save" action deliberately hits the API to demonstrate the
 * server-side stale-interaction path even for a purely UI feature.
 */
export function PhotoAttachments({ reportId }: Props) {
  const enabled = useFlag("reports.photo_attachments");
  const [urls, setUrls] = useState("");
  const [message, setMessage] = useState<{ type: "ok" | "stale" | "error"; text: string } | null>(null);
  const [pending, startTransition] = useTransition();

  if (!enabled) return null;

  const handleSave = () => {
    setMessage(null);
    startTransition(async () => {
      try {
        const res = await fetch(
          `${process.env.NEXT_PUBLIC_API_BASE_URL}/reports/${reportId}/photos`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json" },
            body: JSON.stringify({ urls: urls.split("\n").map((u) => u.trim()).filter(Boolean) }),
          },
        );

        if (res.status === 410) {
          setMessage({ type: "stale", text: "Photo attachments are no longer available. Refresh to update the view." });
          return;
        }

        if (!res.ok) {
          setMessage({ type: "error", text: `Save failed (${res.status}).` });
          return;
        }

        setMessage({ type: "ok", text: "Photos saved." });
      } catch {
        setMessage({ type: "error", text: "Could not reach the server." });
      }
    });
  };

  return (
    <section className="flex flex-col gap-3">
      <h2 className="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
        Photo documentation
        <span className="ml-2 rounded bg-emerald-100 px-1.5 py-0.5 text-xs font-normal text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
          Beta · 25 % rollout
        </span>
      </h2>

      <textarea
        value={urls}
        onChange={(e) => setUrls(e.target.value)}
        rows={3}
        placeholder={"https://example.com/photo1.jpg\nhttps://example.com/photo2.jpg"}
        className="rounded border border-zinc-300 bg-white px-3 py-2 text-sm font-mono dark:border-zinc-700 dark:bg-zinc-900"
      />

      <div className="flex items-center gap-3">
        <button
          onClick={handleSave}
          disabled={pending}
          className="rounded bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
        >
          {pending ? "Saving…" : "Save photos"}
        </button>

        {message && (
          <span className={`text-sm ${
            message.type === "ok" ? "text-emerald-700 dark:text-emerald-400" :
            message.type === "stale" ? "text-amber-600 dark:text-amber-400" :
            "text-red-600 dark:text-red-400"
          }`}>
            {message.text}
          </span>
        )}
      </div>
    </section>
  );
}
