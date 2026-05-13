import "server-only";

const apiBaseUrl = process.env.INTERNAL_API_BASE_URL ?? "http://api:8000";

export type FeatureFlag = {
  id: number;
  name: string;
  description: string | null;
  enabled: boolean;
  created_at: string | null;
  updated_at: string | null;
};

export type FlagInput = {
  name: string;
  description?: string;
  enabled: boolean;
};

export type FlagUpdateInput = {
  description?: string;
  enabled: boolean;
};

type Envelope<T> = { data: T };

export type ValidationErrors = Record<string, string[]>;

export class FlagValidationError extends Error {
  constructor(public readonly errors: ValidationErrors) {
    super("validation_failed");
  }
}

async function jsonRequest<T>(
  path: string,
  init: RequestInit = {},
): Promise<T> {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    ...init,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...init.headers,
    },
    cache: "no-store",
  });

  if (response.status === 422) {
    const body = (await response.json()) as { errors: ValidationErrors };
    throw new FlagValidationError(body.errors ?? {});
  }

  if (!response.ok) {
    throw new Error(
      `Flags API ${init.method ?? "GET"} ${path} failed: ${response.status} ${response.statusText}`,
    );
  }

  if (response.status === 204) return undefined as T;
  return (await response.json()) as T;
}

export async function listFlags(): Promise<FeatureFlag[]> {
  const payload = await jsonRequest<Envelope<FeatureFlag[]>>("/admin/flags");
  return payload.data;
}

export async function getFlag(id: number): Promise<FeatureFlag> {
  const payload = await jsonRequest<Envelope<FeatureFlag>>(
    `/admin/flags/${id}`,
  );
  return payload.data;
}

export async function createFlag(input: FlagInput): Promise<FeatureFlag> {
  const payload = await jsonRequest<Envelope<FeatureFlag>>("/admin/flags", {
    method: "POST",
    body: JSON.stringify(input),
  });
  return payload.data;
}

export async function updateFlag(
  id: number,
  input: FlagUpdateInput,
): Promise<FeatureFlag> {
  const payload = await jsonRequest<Envelope<FeatureFlag>>(
    `/admin/flags/${id}`,
    {
      method: "PATCH",
      body: JSON.stringify(input),
    },
  );
  return payload.data;
}

export async function deleteFlag(id: number): Promise<void> {
  await jsonRequest<void>(`/admin/flags/${id}`, { method: "DELETE" });
}
