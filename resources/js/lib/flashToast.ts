import type { FlashToast } from '@/types/ui';

import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export function initializeFlashToast(): void {
  router.on('flash', (event) => {
    // SAFETY: Inertia emits the flash event as a CustomEvent with the page flash payload.
    const flash = (event as CustomEvent).detail?.flash;
    // SAFETY: The server flash payload is constrained to the FlashToast shape before use.
    const data = flash?.toast as FlashToast | undefined;

    if (!data) {
      return;
    }

    toast[data.type](data.message);
  });
}
