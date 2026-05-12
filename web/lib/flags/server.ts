import "server-only";

import type { EvaluateRequest, EvaluateResponse, FlagDecisions } from "./types";

const apiBaseUrl = process.env.INTERNAL_API_BASE_URL ?? "http://api:8000";

/**
 * Server-side flag evaluation.
 *
 * Called from RSC pages/layouts, which run inside the web container and reach
 * the API over the docker network. The browser never sees this URL — public
 * (browser-side) calls go through NEXT_PUBLIC_API_BASE_URL on a different code path.
 *
 * Failures degrade to "no flags enabled" rather than crashing the page. The
 * tradeoff: a broken flag service makes everything look turned off, which is
 * the safer default for a UI that's already coupled to a flag's existence.
 */
export async function evaluateFlags(
  request: EvaluateRequest,
): Promise<FlagDecisions> {
  try {
    const response = await fetch(`${apiBaseUrl}/flags/evaluate`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(request),
      cache: "no-store",
    });

    if (!response.ok) {
      console.error(
        `[flags] evaluate failed: ${response.status} ${response.statusText}`,
      );
      return {};
    }

    const payload = (await response.json()) as EvaluateResponse;
    return payload.flags;
  } catch (error) {
    console.error("[flags] evaluate threw:", error);
    return {};
  }
}
