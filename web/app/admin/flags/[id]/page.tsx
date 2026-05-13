import Link from "next/link";
import { notFound } from "next/navigation";
import { FlagForm } from "@/components/FlagForm";
import { getFlag } from "@/lib/api/flags";
import { deleteFlagAction, updateFlagAction } from "@/app/admin/flags/actions";

type Props = { params: Promise<{ id: string }> };

export default async function EditFlagPage({ params }: Props) {
  const { id } = await params;
  const flag = await getFlag(Number(id)).catch(() => null);

  if (!flag) notFound();

  const update = updateFlagAction.bind(null, flag.id);

  return (
    <main className="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 px-6 py-12">
      <header className="flex items-center gap-4">
        <Link
          href="/admin/flags"
          className="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100"
        >
          ← Flags
        </Link>
        <h1 className="truncate text-3xl font-semibold tracking-tight font-mono">
          {flag.name}
        </h1>
      </header>

      <FlagForm action={update} initial={flag} submitLabel="Save changes" />

      <hr className="border-zinc-200 dark:border-zinc-800" />

      <form
        action={async () => {
          "use server";
          await deleteFlagAction(flag.id);
        }}
      >
        <button
          type="submit"
          className="rounded border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950"
        >
          Delete flag
        </button>
      </form>
    </main>
  );
}
