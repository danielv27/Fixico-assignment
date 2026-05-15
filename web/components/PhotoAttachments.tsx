"use client";

import { useState, useTransition, useRef } from "react";
import { useRouter } from "next/navigation";
import { useFeatureFlag } from "@/lib/flags/context";
import { savePhotosAction } from "@/app/reports/actions";

type Props = { reportId: number };

export function PhotoAttachments({ reportId }: Props) {
  const enabled = useFeatureFlag("reports.photo_attachments");
  const [urls, setUrls] = useState<string[]>([]);
  const [input, setInput] = useState("");
  const [message, setMessage] = useState<{ type: "ok" | "error"; text: string } | null>(null);
  const [pending, startTransition] = useTransition();
  const inputRef = useRef<HTMLInputElement>(null);
  const router = useRouter();

  if (!enabled) return null;

  const addUrl = () => {
    const val = input.trim();
    if (!val || urls.includes(val)) { setInput(""); return; }
    setUrls((prev) => [...prev, val]);
    setInput("");
    inputRef.current?.focus();
  };

  const removeUrl = (url: string) => setUrls((prev) => prev.filter((u) => u !== url));

  const handleSave = () => {
    setMessage(null);
    startTransition(async () => {
      try {
        const result = await savePhotosAction(reportId, urls);
        if ("error" in result) {
          setMessage({ type: "error", text: result.message });
          router.refresh();
          return;
        }

        setMessage({ type: "ok", text: "Photos saved." });
      } catch {
        setMessage({ type: "error", text: "Could not save photos." });
      }
    });
  };

  return (
    <section className="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
      <div className="flex items-start justify-between">
        <div>
          <h2 className="text-sm font-semibold text-zinc-800">
            Photo documentation
          </h2>
          <p className="mt-0.5 text-xs text-zinc-500">Add photo URLs to document the damage.</p>
        </div>
        <span className="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">
          Beta · 25% rollout
        </span>
      </div>

      <div className="mt-4 flex flex-col gap-2">
        {urls.length > 0 && (
          <ul className="flex flex-col gap-1.5">
            {urls.map((url) => (
              <li key={url} className="flex items-center gap-2 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2">
                <svg className="h-3.5 w-3.5 flex-shrink-0 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span className="flex-1 truncate text-xs font-mono text-zinc-700">{url}</span>
                <button onClick={() => removeUrl(url)} className="ml-1 rounded p-0.5 text-zinc-400 hover:bg-zinc-200 hover:text-zinc-600 transition-colors">
                  <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </li>
            ))}
          </ul>
        )}

        <div className="flex gap-2">
          <input
            ref={inputRef}
            type="url"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyDown={(e) => e.key === "Enter" && (e.preventDefault(), addUrl())}
            placeholder="https://example.com/photo.jpg"
            className="flex-1 rounded-lg border border-zinc-300 px-3 py-2 text-sm placeholder-zinc-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
          />
          <button
            type="button"
            onClick={addUrl}
            disabled={!input.trim()}
            className="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 disabled:opacity-40"
          >
            Add
          </button>
        </div>
      </div>

      <div className="mt-4 flex items-center gap-3">
        <button
          onClick={handleSave}
          disabled={pending || urls.length === 0}
          className="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:opacity-50"
        >
          {pending ? "Saving…" : `Save ${urls.length} photo${urls.length === 1 ? "" : "s"}`}
        </button>

        {message && (
          <span className={`text-sm ${message.type === "ok" ? "text-emerald-700" : "text-red-600"}`}>
            {message.text}
          </span>
        )}
      </div>
    </section>
  );
}
