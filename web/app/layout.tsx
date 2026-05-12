import type { Metadata } from "next";
import "./globals.css";
import { FlagsProvider } from "@/lib/flags/context";
import { evaluateFlags } from "@/lib/flags/server";

export const metadata: Metadata = {
  title: "Fixico Damage Reports",
  description: "Manage car damage reports.",
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  // Slice 1: subject is hardcoded. Slice 5 will introduce a per-browser UUID
  // (cookie-backed) so percentage rollouts can be sticky across reloads.
  const flags = await evaluateFlags({ subject: "anonymous" });

  return (
    <html lang="en" className="h-full antialiased">
      <body className="min-h-full flex flex-col">
        <FlagsProvider flags={flags}>{children}</FlagsProvider>
      </body>
    </html>
  );
}
