"use server";

import { cookies } from "next/headers";
import { revalidatePath } from "next/cache";
import { VIEWER_COOKIE, type ViewerProfile } from "./profile";
import { COUNTRIES_OPTIONS, ROLE_OPTIONS } from "./constants";

export async function setViewerProfile(formData: FormData): Promise<void> {
  const country = formData.get("country")?.toString() ?? "NL";
  const role = formData.get("role")?.toString() ?? "customer";

  const profile: ViewerProfile = {
    country: (COUNTRIES_OPTIONS as readonly string[]).includes(country) ? country : "NL",
    role: (ROLE_OPTIONS as readonly string[]).includes(role) ? role : "customer",
  };

  const jar = await cookies();
  jar.set(VIEWER_COOKIE, JSON.stringify(profile), {
    path: "/",
    httpOnly: false,
    maxAge: 60 * 60 * 24 * 30,
    sameSite: "lax",
  });

  revalidatePath("/", "layout");
}
