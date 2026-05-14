"use client";

import { useEffect } from "react";

export function SubjectInitialiser() {
  useEffect(() => {
    if (document.cookie.split(";").some((c) => c.trim().startsWith("fixico_subject="))) {
      return;
    }
    const uuid = crypto.randomUUID();
    document.cookie = `fixico_subject=${uuid}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
  }, []);

  return null;
}
