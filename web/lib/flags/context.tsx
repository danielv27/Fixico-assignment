"use client";

import { createContext, useContext, type ReactNode } from "react";
import type { FlagDecisions } from "./types";

const FeatureFlagsContext = createContext<FlagDecisions | null>(null);

export function FeatureFlagsProvider({
  flags,
  children,
}: {
  flags: FlagDecisions;
  children: ReactNode;
}) {
  return <FeatureFlagsContext value={flags}>{children}</FeatureFlagsContext>;
}

export function useFeatureFlag(name: string, defaultValue = false): boolean {
  const flags = useContext(FeatureFlagsContext);
  if (flags === null) {
    throw new Error("useFeatureFlag must be used inside <FeatureFlagsProvider>");
  }
  return flags[name] ?? defaultValue;
}
