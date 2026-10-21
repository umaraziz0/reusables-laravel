import { InertiaLinkProps } from '@inertiajs/vue3';

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
  // oxlint-disable-next-line anti-slop/no-runtime-typeof -- Inertia href is string | { url }
  return typeof href === 'string' ? href : href?.url;
}
