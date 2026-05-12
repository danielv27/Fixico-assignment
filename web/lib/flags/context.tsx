"use client";

import { createContext, useContext, type ReactNode } from "react";
import type { FlagDecisions } from "./types";

const FlagsContext = createContext<FlagDecisions | null>(null);

export function FlagsProvider({
  flags,
  children,
}: {
  flags: FlagDecisions;
  children: ReactNode;
}) {
  return <FlagsContext value={flags}>{children}</FlagsContext>;
}

export function useFlag(name: string): boolean {
  const flags = useContext(FlagsContext);
  if (flags === null) {
    throw new Error("useFlag must be used inside <FlagsProvider>");
  }
  return flags[name] ?? false;
}
