/**
 * Browser-side API calls for mutations from client components.
 * No `import "server-only"` here — these run in the browser.
 */

const apiBaseUrl = process.env.NEXT_PUBLIC_API_BASE_URL ?? "http://localhost:8000";

type BulkDeleteResult =
  | { type: "ok"; deleted: number }
  | { type: "feature_disabled" }
  | { type: "error"; status: number };

export async function bulkDeleteReports(ids: number[]): Promise<BulkDeleteResult> {
  const res = await fetch(`${apiBaseUrl}/api/reports/bulk`, {
    method: "DELETE",
    headers: { "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify({ ids }),
  });

  if (res.status === 410) return { type: "feature_disabled" };
  if (!res.ok) return { type: "error", status: res.status };

  const body = (await res.json()) as { deleted: number };
  return { type: "ok", deleted: body.deleted };
}
