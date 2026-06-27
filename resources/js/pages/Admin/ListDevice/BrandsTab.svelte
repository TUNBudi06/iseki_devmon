<script lang="ts">
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Search as DtSearch, RowsPerPage, RowCount, Pagination } from '@vincjo/datatables';
    import { Smartphone, Plus, Edit, Trash2, Search } from '@lucide/svelte';
    import type { Brand } from './types';

    let {
        search = $bindable(''),
        table,
        onadd,
        onedit,
        ondelete,
    }: {
        search?: string;
        table: any | null;
        onadd: () => void;
        onedit: (brand: Brand) => void;
        ondelete: (brand: Brand) => void;
    } = $props();

    function formatDate(dateStr: string): string {
        return new Date(dateStr).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
    }
</script>

<div class="flex items-center justify-between gap-4 flex-wrap">
    <div class="relative max-w-sm w-full">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
        <Input bind:value={search} placeholder="Cari ID atau nama brand..." class="pl-10 h-10 bg-card/60 border-border/60" />
    </div>
    <Button onclick={onadd} class="gap-2 shadow-lg shadow-pink-500/20">
        <Plus class="size-4" /> Tambah Brand
    </Button>
</div>

<Card.Root class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl">
    {#if !table || table.rows.length === 0}
        <div class="p-16 text-center">
            <div class="size-14 rounded-2xl bg-pink-500/10 flex items-center justify-center mx-auto mb-4">
                <Smartphone class="size-7 text-pink-400/60" />
            </div>
            <p class="text-muted-foreground font-medium">
                {search ? 'Tidak ada brand yang cocok' : 'Belum ada brand terdaftar'}
            </p>
            {#if !search}
                <Button variant="outline" size="sm" onclick={onadd} class="mt-4 gap-2">
                    <Plus class="size-3.5" /> Tambah Brand
                </Button>
            {/if}
        </div>
    {:else}
        <div class="flex items-center gap-3 px-6 py-3 border-b border-border/30 bg-muted/10 flex-wrap">
            <DtSearch table={table} />
            <RowsPerPage table={table} />
        </div>
        <div class="p-4 space-y-3">
            {#each table.rows as brand (brand.id)}
                <div class="rounded-xl border border-border/60 bg-card p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <Badge variant="outline" class="font-mono text-xs bg-pink-500/8 border-pink-300/30 text-pink-600 shrink-0">{brand.id}</Badge>
                        <div class="min-w-0">
                            <span class="font-medium text-sm">{brand.name}</span>
                            <p class="text-xs text-muted-foreground">{formatDate(brand.created_at)}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-pink-500 hover:bg-pink-500/10" onclick={() => onedit(brand)}>
                            <Edit class="size-3.5" />
                        </Button>
                        <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-red-400 hover:bg-red-500/10" onclick={() => ondelete(brand)}>
                            <Trash2 class="size-3.5" />
                        </Button>
                    </div>
                </div>
            {/each}
        </div>
        <div class="flex items-center justify-between px-6 py-3 border-t border-border/30 bg-muted/10">
            <RowCount table={table} />
            <Pagination table={table} />
        </div>
    {/if}
</Card.Root>
