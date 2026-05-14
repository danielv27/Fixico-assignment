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
  title: "Fixico · Damage Reports",
  description: "Manage car damage reports.",
};

export default async function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  const jar = await cookies();
  const subject = jar.get("fixico_subject")?.value ?? "anonymous";
  const profile = await getViewerProfile();
  const flags = await evaluateFlags({ subject, attributes: profile });

  return (
    <html lang="en" className="h-full">
      <body className="flex min-h-full flex-col bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <SubjectInitialiser />
        <FlagsProvider flags={flags}>
          <header className="sticky top-0 z-10 border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">
            <div className="mx-auto flex max-w-3xl items-center justify-between px-6 py-4">
              <nav className="flex items-center gap-1">
                <Link
                  href="/"
                  className="rounded-md px-3 py-1.5 text-sm font-semibold text-zinc-900 transition-colors hover:bg-zinc-100 dark:text-zinc-100 dark:hover:bg-zinc-800"
                >
                  Fixico
                </Link>
                <span className="text-zinc-300 dark:text-zinc-700">·</span>
                <Link
                  href="/"
                  className="rounded-md px-3 py-1.5 text-sm font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                >
                  Reports
                </Link>
                <a
                  href="http://localhost:8000/admin/flags"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="rounded-md px-3 py-1.5 text-sm font-medium text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
                >
                  Flags ↗
                </a>
              </nav>
              <ViewerSwitcher profile={profile} />
            </div>
          </header>
          <div className="flex flex-1 flex-col">
            {children}
          </div>
        </FlagsProvider>
      </body>
    </html>
  );
}
