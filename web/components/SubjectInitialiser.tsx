"use client";

import { useEffect } from "react";
import { SUBJECT_COOKIE } from "@/lib/viewer/constants";

export function SubjectInitialiser() {
  useEffect(() => {
    if (document.cookie.split(";").some((c) => c.trim().startsWith(`${SUBJECT_COOKIE}=`))) {
      return;
    }
    const uuid = crypto.randomUUID();
    document.cookie = `${SUBJECT_COOKIE}=${uuid}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
  }, []);

  return null;
}
