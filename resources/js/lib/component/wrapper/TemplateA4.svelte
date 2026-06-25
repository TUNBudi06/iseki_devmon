<script lang="ts">
    import { cn } from '$lib/utils.ts';

    let {
        class: className = '',
        ref = $bindable(),
        children,
        orientation = 'landscape' as 'portrait' | 'landscape',
        ...restProps
    } = $props();

    const dimensions = {
        portrait: { width: 210, height: 297 },
        landscape: { width: 297, height: 210 },
    };

    const { width: w, height: h } = dimensions[orientation];

    let containerClass = cn('relative bg-white box-border print:shadow-none print:border-0', className);
</script>

<div
    bind:this={ref}
    class={containerClass}
    style="width: {w * 3.78}px; min-width: {w * 3.78}px; height: {h * 3.78}px; min-height: {h * 3.78}px;"
    {...restProps}
>
    {@render children?.()}
</div>

<style>
    @media print {
        :global(body) {
            margin: 0;
            padding: 0;
            background: white;
        }
    }
</style>
