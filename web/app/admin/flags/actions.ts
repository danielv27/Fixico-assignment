"use server";

import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import {
  createFlag,
  deleteFlag,
  FlagValidationError,
  updateFlag,
  type ValidationErrors,
} from "@/lib/api/flags";

export type FormState = {
  errors?: ValidationErrors;
  message?: string;
};

export async function createFlagAction(
  _previous: FormState,
  formData: FormData,
): Promise<FormState> {
  try {
    await createFlag({
      name: formData.get("name")?.toString() ?? "",
      description: formData.get("description")?.toString() || undefined,
      enabled: formData.get("enabled") === "true",
    });

    revalidatePath("/admin/flags");
    redirect("/admin/flags");
  } catch (error) {
    if (error instanceof FlagValidationError) {
      return { errors: error.errors };
    }
    throw error;
  }
}

export async function updateFlagAction(
  id: number,
  _previous: FormState,
  formData: FormData,
): Promise<FormState> {
  try {
    await updateFlag(id, {
      description: formData.get("description")?.toString() || undefined,
      enabled: formData.get("enabled") === "true",
    });

    revalidatePath("/admin/flags");
    revalidatePath(`/admin/flags/${id}`);
    return { message: "Saved." };
  } catch (error) {
    if (error instanceof FlagValidationError) {
      return { errors: error.errors };
    }
    throw error;
  }
}

export async function deleteFlagAction(id: number): Promise<void> {
  await deleteFlag(id);
  revalidatePath("/admin/flags");
  redirect("/admin/flags");
}
