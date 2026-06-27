import { dashboard } from '$routes/admin';
import { router } from '@inertiajs/svelte';

/**
 * Shared go-back helper for admin pages.
 * Use in onclick handlers: `onclick={goBack}`
 */
export function goBack() {
    router.visit(dashboard().url);
}
