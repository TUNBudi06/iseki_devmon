<script lang="ts">
    import { cn } from '$shadcn/utils.ts';

    let { children, class: className = '' } = $props();
</script>

<nav
    class={cn(
        'w-full h-15 border-b border-border bg-background/60 backdrop-blur-sm flex items-center px-4 z-50',
        className,
    )}
>
    {@render children?.()}
</nav>
