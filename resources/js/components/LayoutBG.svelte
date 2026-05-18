<script>
    import { cn } from '$shadcn/utils.ts';

    let { children, class: className = '' } = $props();

    let classes = cn(
        'min-h-screen bg-background relative overflow-hidden',
        className,
    );
</script>

<div class={classes}>
    <div
        class="absolute -top-52 -left-40 w-150 h-150 rounded-full bg-gradient-pink opacity-10 blur-3xl pointer-events-none"
    ></div>
    <div
        class="absolute -bottom-52 -right-32 w-125 h-125 rounded-full bg-gradient-pink opacity-8 blur-3xl pointer-events-none"
    ></div>
    {@render children?.()}
</div>
