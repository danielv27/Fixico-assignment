import type { Metadata } from "next";
import Link from "next/link";
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
        <FlagsProvider flags={flags}>
          <nav className="border-b border-zinc-200 bg-white px-6 py-3 dark:border-zinc-800 dark:bg-zinc-950">
            <div className="mx-auto flex max-w-3xl items-center gap-6 text-sm font-medium">
              <Link href="/" className="text-zinc-900 hover:text-emerald-600 dark:text-zinc-100">
                Reports
              </Link>
              <a href="http://localhost:8000/admin/flags" className="text-zinc-500 hover:text-emerald-600 dark:text-zinc-400">
                Admin · Flags ↗
              </a>
            </div>
          </nav>
          {children}
        </FlagsProvider>
      </body>
    </html>
  );
}
