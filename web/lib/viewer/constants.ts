export const USER_COOKIE = "fixico_subject";

export const COUNTRIES_OPTIONS = ["NL", "BE", "DE", "FR", "GB"] as const;
export const ROLE_OPTIONS = ["customer", "mechanic", "admin"] as const;

export type Country = (typeof COUNTRIES_OPTIONS)[number];
export type Role = (typeof ROLE_OPTIONS)[number];
