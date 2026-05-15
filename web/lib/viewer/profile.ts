import "server-only";

import { cookies } from "next/headers";

export const VIEWER_COOKIE = "fixico_viewer";

export type ViewerProfile = {
  country: string;
  role: string;
};

export const DEFAULT_PROFILE: ViewerProfile = { country: "NL", role: "customer" };

export async function getViewerProfile(): Promise<ViewerProfile> {
  const jar = await cookies();
  const raw = jar.get(VIEWER_COOKIE)?.value;

  if (!raw) return DEFAULT_PROFILE;

  try {
    const parsed = JSON.parse(raw) as Partial<ViewerProfile>;
    return {
      country: typeof parsed.country === "string" ? parsed.country : DEFAULT_PROFILE.country,
      role: typeof parsed.role === "string" ? parsed.role : DEFAULT_PROFILE.role,
    };
  } catch {
    return DEFAULT_PROFILE;
  }
}
