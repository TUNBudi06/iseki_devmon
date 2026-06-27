<script lang="ts">
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Separator } from '$shadcn/components/ui/separator';
    import { Plus, Edit, Check } from '@lucide/svelte';

    let {
        mode,
        target,
        form,
        show,
        onclose,
        onsave,
    }: {
        mode: 'add' | 'edit';
        target: { id: string; name: string } | null;
        form: { id?: string; name: string; errors: Record<string, string>; processing: boolean };
        show: boolean;
        onclose: () => void;
        onsave: () => void;
    } = $props();

    let isAdd = $derived(mode === 'add');
    let icon = $derived(isAdd ? Plus : Edit);
    let iconBg = $derived(isAdd ? 'bg-pink-500/20' : 'bg-emerald-500/20');
    let iconColor = $derived(isAdd ? 'text-pink-400' : 'text-emerald-400');
    let title = $derived(isAdd ? 'Tambah Brand' : 'Edit Brand');
    let subtitle = $derived(isAdd ? 'Masukkan ID dan nama brand baru' : '');
</script>

{#if (isAdd && show) || (!isAdd && target)}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl {iconBg} flex items-center justify-center">
                        <svelte:component this={icon} class="size-5 {iconColor}" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">{title}</Card.Title>
                        <Card.Description>
                            {#if isAdd}
                                {subtitle}
                            {:else if target}
                                <span class="font-mono text-xs">{target.id}</span>
                            {/if}
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                {#if isAdd}
                    <div class="space-y-2">
                        <Label for="brand-id" class="text-sm font-medium">Device ID</Label>
                        <Input id="brand-id" bind:value={form.id!} placeholder="Contoh: DEV-011" class="h-10" />
                        {#if form.errors.id}<p class="text-xs text-rose-400">{form.errors.id}</p>{/if}
                    </div>
                {/if}
                <div class="space-y-2">
                    <Label for="brand-name" class="text-sm font-medium">Device Name</Label>
                    <Input id="brand-name" bind:value={form.name} placeholder="Contoh: Samsung Galaxy A55" class="h-10" />
                    {#if form.errors.name}<p class="text-xs text-rose-400">{form.errors.name}</p>{/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={onclose} disabled={form.processing}>Batal</Button>
                <Button onclick={onsave} disabled={form.processing || !(isAdd ? (form.id?.trim() && form.name.trim()) : form.name.trim())} class="gap-2">
                    {#if form.processing}
                        <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menyimpan...
                    {:else}
                        <Check class="size-4" /> Simpan
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}
