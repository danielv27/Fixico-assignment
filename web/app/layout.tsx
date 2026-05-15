import type { Metadata } from "next";
import Link from "next/link";
import { cookies } from "next/headers";
import "./globals.css";
import { FeatureFlagsProvider } from "@/lib/flags/context";
import { evaluateFeatureFlags } from "@/lib/flags/server";
import { getUserProfile } from "@/lib/viewer/profile";
import { USER_COOKIE } from "@/lib/viewer/constants";
import { rolloutBucket } from "@/lib/viewer/bucket";
import { UserSwitcher } from "@/components/UserSwitcher";
import { UserInitialiser } from "@/components/UserInitialiser";
import { DemoBanner } from "@/components/DemoBanner";

export const metadata: Metadata = {
  title: "Fixico · Damage Reports",
  description: "Manage car damage reports.",
};

export default async function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  const jar = await cookies();
  const userId = jar.get(USER_COOKIE)?.value ?? "anonymous";
  const profile = await getUserProfile();
  const featureFlags = await evaluateFeatureFlags({ user_id: userId, attributes: profile });
  const bucket = rolloutBucket(userId);

  return (
    <html lang="en" className="h-full">
      <body className="flex min-h-full flex-col bg-zinc-50 text-zinc-900 antialiased">
        <UserInitialiser />
        <FeatureFlagsProvider flags={featureFlags}>
          <DemoBanner />

          <div className="h-0.5 bg-linear-to-r from-emerald-500 via-teal-400 to-emerald-600" />

          <header className="sticky top-0 z-10 border-b border-zinc-200 bg-white/95 backdrop-blur">
            <div className="mx-auto flex max-w-3xl items-center justify-between px-6 py-3">
              <div className="flex items-center gap-5">
                <Link href="/" className="group flex items-center gap-2">
                  <div className="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 shadow-sm transition-transform group-hover:scale-105">
                    <svg className="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                  </div>
                  <span className="text-sm font-bold tracking-tight">Fixico</span>
                </Link>
              </div>
              <UserSwitcher profile={profile} bucket={bucket} />
            </div>
          </header>

          <div className="flex flex-1 flex-col">
            {children}
          </div>
        </FeatureFlagsProvider>
      </body>
    </html>
  );
}
