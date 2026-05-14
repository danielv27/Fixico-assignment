import type { Metadata } from "next";
import Link from "next/link";
import { cookies } from "next/headers";
import "./globals.css";
import { FlagsProvider } from "@/lib/flags/context";
import { evaluateFlags } from "@/lib/flags/server";
import { getViewerProfile } from "@/lib/viewer/profile";
import { ViewerSwitcher } from "@/components/ViewerSwitcher";
import { SubjectInitialiser } from "@/components/SubjectInitialiser";

export const metadata: Metadata = {
  title: "Fixico Damage Reports",
  description: "Manage car damage reports.",
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const jar = await cookies();
  // Stable per-browser UUID for percentage-rollout stickiness.
  // On the very first request the cookie isn't set yet; SubjectInitialiser
  // sets it client-side and subsequent requests use it.
  const subject = jar.get("fixico_subject")?.value ?? "anonymous";

  const profile = await getViewerProfile();
  const flags = await evaluateFlags({ subject, attributes: profile });

  return (
    <html lang="en" className="h-full antialiased">
      <body className="min-h-full flex flex-col">
        <SubjectInitialiser />
        <FlagsProvider flags={flags}>
          <nav className="border-b border-zinc-200 bg-white px-6 py-3 dark:border-zinc-800 dark:bg-zinc-950">
            <div className="mx-auto flex max-w-3xl items-center justify-between gap-6 text-sm font-medium">
              <div className="flex items-center gap-6">
                <Link href="/" className="text-zinc-900 hover:text-emerald-600 dark:text-zinc-100">
                  Reports
                </Link>
                <a href="http://localhost:8000/admin/flags" className="text-zinc-500 hover:text-emerald-600 dark:text-zinc-400">
                  Admin · Flags ↗
                </a>
              </div>
              <ViewerSwitcher profile={profile} />
            </div>
          </nav>
          {children}
        </FlagsProvider>
      </body>
    </html>
  );
}
