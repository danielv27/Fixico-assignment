"use client";

import { useEffect } from "react";

/**
 * Sets a stable per-browser UUID cookie on first visit.  The cookie is read
 * server-side in the layout so percentage rollouts are sticky across requests.
 * Because cookies can only be set from client-side JS or a server function,
 * and we need it to exist on the very first render, we initialise it here on
 * mount.  If the cookie already exists this is a no-op.
 */
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
