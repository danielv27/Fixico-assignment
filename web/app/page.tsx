import { DemoBanner } from "@/components/DemoBanner";

export default function Home() {
  return (
    <>
      <DemoBanner />
      <main className="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 px-6 py-16">
        <h1 className="text-3xl font-semibold tracking-tight">
          Fixico Damage Reports
        </h1>
        <p className="text-zinc-600 dark:text-zinc-400">
          The damage report UI lands in Slice 2. This page only exists to prove
          the feature flag pipeline works end to end — toggle{" "}
          <code className="font-mono">demo.banner</code> in the database and
          refresh to see the banner appear or disappear.
        </p>
      </main>
    </>
  );
}
