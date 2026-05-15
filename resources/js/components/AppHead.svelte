<script lang="ts">
    import type { Snippet } from 'svelte';
    import { Toaster } from '$shadcn/components/ui/sonner/index.ts';

    let {
        title = '',
        children,
    }: {
        title?: string;
        children?: Snippet;
    } = $props();

    const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
    const fullTitle = $derived(title ? `${title} - ${appName}` : appName);
</script>

<Toaster richColors position="top-right" duration={3000} />

<svelte:head>
    <title>{fullTitle}</title>
    {@render children?.()}
</svelte:head>
