"use client";

import { useEffect } from "react";
import { USER_COOKIE } from "@/lib/users/constants";

export function UserInitialiser() {
  useEffect(() => {
    if (document.cookie.split(";").some((c) => c.trim().startsWith(`${USER_COOKIE}=`))) {
      return;
    }
    const uuid = crypto.randomUUID();
    document.cookie = `${USER_COOKIE}=${uuid}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
  }, []);

  return null;
}
