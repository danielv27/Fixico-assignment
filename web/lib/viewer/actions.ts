"use server";

import { cookies } from "next/headers";
import { revalidatePath } from "next/cache";
import { VIEWER_COOKIE, type ViewerProfile } from "./profile";

const COUNTRIES = ["NL", "BE", "DE", "FR", "GB"] as const;
const ROLES = ["customer", "mechanic", "admin"] as const;

export async function setViewerProfile(formData: FormData): Promise<void> {
  const country = formData.get("country")?.toString() ?? "NL";
  const role = formData.get("role")?.toString() ?? "customer";

  const profile: ViewerProfile = {
    country: (COUNTRIES as readonly string[]).includes(country) ? country : "NL",
    role: (ROLES as readonly string[]).includes(role) ? role : "customer",
  };

  const jar = await cookies();
  jar.set(VIEWER_COOKIE, JSON.stringify(profile), {
    path: "/",
    httpOnly: false, // readable client-side so JS can display current selection
    maxAge: 60 * 60 * 24 * 30, // 30 days
    sameSite: "lax",
  });

  revalidatePath("/", "layout");
}

export const COUNTRIES_OPTIONS = COUNTRIES;
export const ROLE_OPTIONS = ROLES;
