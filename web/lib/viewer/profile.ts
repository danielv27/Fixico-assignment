import "server-only";

import { cookies } from "next/headers";

export const USER_PROFILE_COOKIE = "fixico_user_profile";

export type UserProfile = {
  country: string;
  role: string;
};

export const DEFAULT_PROFILE: UserProfile = { country: "NL", role: "customer" };

export async function getUserProfile(): Promise<UserProfile> {
  const jar = await cookies();
  const raw = jar.get(USER_PROFILE_COOKIE)?.value;

  if (!raw) return DEFAULT_PROFILE;

  try {
    const parsed = JSON.parse(raw) as Partial<UserProfile>;
    return {
      country: typeof parsed.country === "string" ? parsed.country : DEFAULT_PROFILE.country,
      role: typeof parsed.role === "string" ? parsed.role : DEFAULT_PROFILE.role,
    };
  } catch {
    return DEFAULT_PROFILE;
  }
}
