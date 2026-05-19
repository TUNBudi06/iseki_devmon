<script>
    import { cn } from '$shadcn/utils.ts';

    let { children, class: className = '' } = $props();

    let classes = cn('min-h-screen bg-background relative', className);
</script>

<div class={classes}>
    <!-- Blobs punya overflow-hidden sendiri, tidak ganggu sticky -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div
            class="absolute -top-52 -left-40 w-150 h-150 rounded-full bg-gradient-pink opacity-10 blur-3xl"
        ></div>
        <div
            class="absolute -bottom-52 -right-32 w-125 h-125 rounded-full bg-gradient-pink opacity-8 blur-3xl"
        ></div>
    </div>

    {@render children?.()}
</div>
