import type { ComputedRef, Ref } from 'vue';

import { useHttp } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { qrCode, recoveryCodes, secretKey } from '@/routes/two-factor';

type QrCodeResponse = {
  svg: string;
  url: string;
};

type SetupKeyResponse = {
  secretKey: string;
};

export type UseTwoFactorAuthReturn = {
  qrCodeSvg: Ref<string | null>;
  manualSetupKey: Ref<string | null>;
  recoveryCodesList: Ref<string[]>;
  errors: Ref<string[]>;
  hasSetupData: ComputedRef<boolean>;
  clearSetupData: () => void;
  clearErrors: () => void;
  clearTwoFactorAuthData: () => void;
  fetchQrCode: () => Promise<void>;
  fetchSetupKey: () => Promise<void>;
  fetchSetupData: () => Promise<void>;
  fetchRecoveryCodes: () => Promise<void>;
};

const errors = ref<string[]>([]);

const manualSetupKey = ref<string | null>(null);

const qrCodeSvg = ref<string | null>(null);

const recoveryCodesList = ref<string[]>([]);

const hasSetupData = computed<boolean>(
  () => qrCodeSvg.value !== null && manualSetupKey.value !== null,
);

export const useTwoFactorAuth = (): UseTwoFactorAuthReturn => {
  const qrHttp = useHttp<Record<string, never>, QrCodeResponse>();
  const setupKeyHttp = useHttp<Record<string, never>, SetupKeyResponse>();
  const recoveryHttp = useHttp<Record<string, never>, string[]>();

  const fetchQrCode = async (): Promise<void> => {
    try {
      const { svg } = await qrHttp.submit(qrCode());

      qrCodeSvg.value = svg;
    } catch {
      errors.value.push('Failed to fetch QR code');
      qrCodeSvg.value = null;
    }
  };

  const fetchSetupKey = async (): Promise<void> => {
    try {
      const { secretKey: key } = await setupKeyHttp.submit(secretKey());

      manualSetupKey.value = key;
    } catch {
      errors.value.push('Failed to fetch a setup key');
      manualSetupKey.value = null;
    }
  };

  const clearSetupData = (): void => {
    manualSetupKey.value = null;
    qrCodeSvg.value = null;
    clearErrors();
  };

  const clearErrors = (): void => {
    errors.value = [];
  };

  const clearTwoFactorAuthData = (): void => {
    clearSetupData();
    clearErrors();
    recoveryCodesList.value = [];
  };

  const fetchRecoveryCodes = async (): Promise<void> => {
    try {
      clearErrors();
      recoveryCodesList.value = await recoveryHttp.submit(recoveryCodes());
    } catch {
      errors.value.push('Failed to fetch recovery codes');
      recoveryCodesList.value = [];
    }
  };

  const fetchSetupData = async (): Promise<void> => {
    try {
      clearErrors();
      await Promise.all([fetchQrCode(), fetchSetupKey()]);
    } catch {
      qrCodeSvg.value = null;
      manualSetupKey.value = null;
    }
  };

  return {
    qrCodeSvg,
    manualSetupKey,
    recoveryCodesList,
    errors,
    hasSetupData,
    clearSetupData,
    clearErrors,
    clearTwoFactorAuthData,
    fetchQrCode,
    fetchSetupKey,
    fetchSetupData,
    fetchRecoveryCodes,
  };
};
