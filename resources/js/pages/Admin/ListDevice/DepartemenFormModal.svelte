<script lang="ts">
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Separator } from '$shadcn/components/ui/separator';
    import { Layers, Edit, Check } from '@lucide/svelte';

    let {
        mode,
        target,
        form,
        show,
        onclose,
        onsave,
    }: {
        mode: 'add' | 'edit';
        target: { id: string; name: string; color: string } | null;
        form: { id?: string; name: string; color: string; errors: Record<string, string>; processing: boolean };
        show: boolean;
        onclose: () => void;
        onsave: () => void;
    } = $props();

    let isAdd = $derived(mode === 'add');
    let iconBg = $derived(isAdd ? 'bg-pink-500/20' : 'bg-emerald-500/20');
    let iconColor = $derived(isAdd ? 'text-pink-400' : 'text-emerald-400');
    let title = $derived(isAdd ? 'Tambah Departemen' : 'Edit Departemen');
    let subtitle = $derived(isAdd ? 'Masukkan ID dan nama departemen baru' : '');
</script>

{#if (isAdd && show) || (!isAdd && target)}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl {iconBg} flex items-center justify-center">
                        {#if isAdd}
                            <Layers class="size-5 {iconColor}" />
                        {:else}
                            <Edit class="size-5 {iconColor}" />
                        {/if}
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
                        <Label for="dept-id" class="text-sm font-medium">ID Departemen</Label>
                        <Input id="dept-id" bind:value={form.id!} placeholder="Contoh: QC" class="h-10" />
                        {#if form.errors.id}<p class="text-xs text-rose-400">{form.errors.id}</p>{/if}
                    </div>
                {/if}
                <div class="space-y-2">
                    <Label for="dept-name" class="text-sm font-medium">Nama Departemen</Label>
                    <Input id="dept-name" bind:value={form.name} placeholder="Contoh: Quality Control" class="h-10" />
                    {#if form.errors.name}<p class="text-xs text-rose-400">{form.errors.name}</p>{/if}
                </div>
                <div class="space-y-2">
                    <Label for="dept-color" class="text-sm font-medium">Warna</Label>
                    <div class="flex items-center gap-3">
                        <input id="dept-color" type="color" bind:value={form.color} class="size-10 rounded-lg border border-input bg-transparent cursor-pointer p-1" />
                        <span class="text-xs font-mono text-muted-foreground">{form.color}</span>
                    </div>
                    {#if form.errors.color}<p class="text-xs text-rose-400">{form.errors.color}</p>{/if}
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
