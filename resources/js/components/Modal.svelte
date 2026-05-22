<script lang="ts">
    import * as Card from '$shadcn/components/ui/card';

    let {
        show = $bindable(false),
        maxWidth = 'max-w-lg',
        onClose = () => (show = false),
        title,
        description,
        icon,
        children,
        footer,
    }: {
        show: boolean;
        maxWidth?: string;
        onClose?: () => void;
        title?: string;
        description?: string;
        icon?: any;
        children?: import('svelte').Snippet;
        footer?: import('svelte').Snippet;
    } = $props();
</script>

{#if show}
    <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
    <div
        class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        onclick={onClose}
    >
        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <Card.Root
            class="w-full {maxWidth} border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200"
            onclick={(e: MouseEvent) => e.stopPropagation()}
        >
            {#if title || icon}
                <Card.Header>
                    <div class="flex items-center gap-3">
                        {#if icon}
                            <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center shrink-0">
                                <svelte:component this={icon} class="size-5 text-pink-400" />
                            </div>
                        {/if}
                        <div>
                            {#if title}
                                <Card.Title class="text-lg">{title}</Card.Title>
                            {/if}
                            {#if description}
                                <Card.Description>{description}</Card.Description>
                            {/if}
                        </div>
                    </div>
                </Card.Header>
            {/if}

            {#if children}
                <Card.Content class="pt-5 space-y-4 max-h-[75vh] overflow-y-auto">
                    {@render children()}
                </Card.Content>
            {/if}

            {#if footer}
                <Card.Footer class="flex justify-end gap-3 pt-2">
                    {@render footer()}
                </Card.Footer>
            {/if}
        </Card.Root>
    </div>
{/if}
