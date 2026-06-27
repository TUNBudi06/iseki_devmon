<script lang="ts">
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Trash2 } from '@lucide/svelte';

    let {
        target,
        title,
        description,
        form,
        onclose,
        ondelete,
    }: {
        target: { id: string | number; name: string; model_id?: string; model_name?: string } | null;
        title: string;
        description: string;
        form: { processing: boolean };
        onclose: () => void;
        ondelete: () => void;
    } = $props();
</script>

{#if target}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-red-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                        <Trash2 class="size-5 text-red-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">{title}</Card.Title>
                        <Card.Description>
                            {@html description}
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Card.Footer class="flex justify-end gap-3">
                <Button variant="outline" onclick={onclose} disabled={form.processing}>Batal</Button>
                <Button variant="destructive" onclick={ondelete} disabled={form.processing} class="gap-2">
                    {#if form.processing}
                        <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        Menghapus...
                    {:else}
                        <Trash2 class="size-4" /> Hapus
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}
