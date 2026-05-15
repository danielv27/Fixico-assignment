import "server-only";

import { cookies } from "next/headers";
import { getUserProfile } from "@/lib/users/profile";
import { USER_COOKIE } from "@/lib/users/constants";
import type { EvaluateRequest, EvaluateResponse, FlagDecisions } from "./types";

const apiBaseUrl = process.env.API_URL ?? "http://localhost:8000";

/**
 * Server-side flag evaluation.
 *
 * Called from RSC pages/layouts, which run inside the web container and reach
 * the API over the configured base URL.
 *
 * Failures degrade to "no flags enabled" rather than crashing the page. The
 * tradeoff: a broken flag service makes everything look turned off, which is
 * the safer default for a UI that's already coupled to a flag's existence.
 */
export async function evaluateFeatureFlags(
  request: EvaluateRequest,
): Promise<FlagDecisions> {
  try {
    const response = await fetch(`${apiBaseUrl}/api/feature_flags/evaluate`, {
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
        `[feature-flags] evaluate failed: ${response.status} ${response.statusText}`,
      );
      return {};
    }

    const payload = (await response.json()) as EvaluateResponse;
    return payload.flags;
  } catch (error) {
    console.error("[feature-flags] evaluate threw:", error);
    return {};
  }
}

export async function evaluateCurrentUserFeatureFlags(): Promise<FlagDecisions> {
  const jar = await cookies();
  const userId = jar.get(USER_COOKIE)?.value ?? "anonymous";
  const profile = await getUserProfile();

  return evaluateFeatureFlags({ user_id: userId, attributes: profile });
}

export async function isFeatureEnabledForCurrentUser(
  flag: string,
): Promise<boolean> {
  const flags = await evaluateCurrentUserFeatureFlags();

  return flags[flag] ?? false;
}
