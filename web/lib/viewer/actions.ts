"use server";

import { cookies } from "next/headers";
import { revalidatePath } from "next/cache";
import { USER_PROFILE_COOKIE, type UserProfile } from "./profile";
import { COUNTRIES_OPTIONS, ROLE_OPTIONS, USER_COOKIE } from "./constants";

export async function setUserProfile(formData: FormData): Promise<void> {
  const country = formData.get("country")?.toString() ?? "NL";
  const role = formData.get("role")?.toString() ?? "customer";

  const profile: UserProfile = {
    country: (COUNTRIES_OPTIONS as readonly string[]).includes(country) ? country : "NL",
    role: (ROLE_OPTIONS as readonly string[]).includes(role) ? role : "customer",
  };

  const jar = await cookies();
  jar.set(USER_PROFILE_COOKIE, JSON.stringify(profile), {
    path: "/",
    httpOnly: false,
    maxAge: 60 * 60 * 24 * 30,
    sameSite: "lax",
  });

  revalidatePath("/", "layout");
}

export async function resetUser(): Promise<void> {
  const jar = await cookies();
  jar.set(USER_COOKIE, crypto.randomUUID(), {
    path: "/",
    httpOnly: false,
    maxAge: 60 * 60 * 24 * 365,
    sameSite: "lax",
  });

  revalidatePath("/", "layout");
}
