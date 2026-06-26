import { router } from '@inertiajs/svelte';
import { dashboard } from '$routes/admin';

/**
 * Shared go-back helper for admin pages.
 * Use in onclick handlers: `onclick={goBack}`
 */
export function goBack() {
    router.visit(dashboard().url);
}
