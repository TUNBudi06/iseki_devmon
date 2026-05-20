<script lang="ts">
    import { cn } from '$shadcn/utils.ts';

    let { children, class: className = '', ...rest } = $props();
</script>

<nav
    class={cn(
        'w-full h-15 border-b border-border bg-background/60 backdrop-blur-sm flex items-center px-4 z-50',
        'sticky top-0 z-50 border-b border-white/10 bg-background/70 backdrop-blur-2xl justify-between px-6',
        className,
    )}
    {...rest}
>
    {@render children?.()}
</nav>
