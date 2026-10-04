// oxlint-disable-next-line anti-slop/no-unsafe-dictionary-type -- Inertia auth payloads may contain server-provided user attributes.
export type User = {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  two_factor_enabled?: boolean;
  created_at: string;
  updated_at: string;
  [key: string]: unknown;
};

export type Auth = {
  // oxlint-disable-next-line anti-slop/no-unsafe-dictionary-type -- Auth carries the server-provided user payload.
  user: User;
};

/* @chisel-passkeys */
export type Passkey = {
  id: number;
  name: string;
  authenticator: string | null;
  created_at_diff: string;
  last_used_at_diff: string | null;
};
/* @end-chisel-passkeys */

export type TwoFactorConfigContent = {
  title: string;
  description: string;
  buttonText: string;
};
