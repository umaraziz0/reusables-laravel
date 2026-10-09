import type { ComputedRef, MaybeRefOrGetter } from 'vue';

import { computed, toValue } from 'vue';

export type UseFieldIdsOptions = {
  id: MaybeRefOrGetter<string>;
  description: MaybeRefOrGetter<string | undefined>;
  error: MaybeRefOrGetter<string | undefined>;
  ariaDescribedby: MaybeRefOrGetter<string | undefined>;
};

export type UseFieldIdsReturn = {
  descriptionId: ComputedRef<string | undefined>;
  errorId: ComputedRef<string | undefined>;
  describedBy: ComputedRef<string | undefined>;
};

/**
 * Derives the feedback ids for a form field and the `aria-describedby` that
 * points at them. An error replaces the description, so only one is linked.
 */
export function useFieldIds(options: UseFieldIdsOptions): UseFieldIdsReturn {
  const descriptionId = computed(() =>
    toValue(options.description)
      ? `${toValue(options.id)}-description`
      : undefined,
  );

  const errorId = computed(() =>
    toValue(options.error) ? `${toValue(options.id)}-error` : undefined,
  );

  const describedBy = computed(
    () =>
      [toValue(options.ariaDescribedby), errorId.value ?? descriptionId.value]
        .filter(Boolean)
        .join(' ') || undefined,
  );

  return { descriptionId, errorId, describedBy };
}
