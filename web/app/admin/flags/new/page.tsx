import Link from "next/link";
import { FlagForm } from "@/components/FlagForm";
import { createFlagAction } from "@/app/admin/flags/actions";

export default function NewFlagPage() {
  return (
    <main className="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 px-6 py-12">
      <header className="flex items-center gap-4">
        <Link
          href="/admin/flags"
          className="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100"
        >
          ← Flags
        </Link>
        <h1 className="text-3xl font-semibold tracking-tight">New flag</h1>
      </header>
      <FlagForm action={createFlagAction} submitLabel="Create flag" />
    </main>
  );
}
