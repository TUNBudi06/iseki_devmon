import { onMount, onDestroy } from 'svelte';
import { router } from '@inertiajs/svelte';

type PollingOptions = {
    only: string[];
    interval?: number;
};

export function usePolling({ only, interval = 10000 }: PollingOptions) {
    let pollInterval: ReturnType<typeof setInterval> | null = null;

    onMount(() => {
        pollInterval = setInterval(() => {
            router.reload({ only });
        }, interval);
    });

    onDestroy(() => {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
    });
}
