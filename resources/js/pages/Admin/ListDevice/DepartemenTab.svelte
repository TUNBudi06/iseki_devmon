<script lang="ts">
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Layers, Plus, Edit, Trash2, Search } from '@lucide/svelte';
    import type { DepartemenOption } from './types';

    let {
        departemenOptions,
        search,
        phoneCountByDept,
        onadd,
        onedit,
        ondelete,
    }: {
        departemenOptions: DepartemenOption[];
        search: string;
        phoneCountByDept: (id: string) => number;
        onadd: () => void;
        onedit: (dept: DepartemenOption) => void;
        ondelete: (dept: DepartemenOption) => void;
    } = $props();
</script>

<div class="flex items-center justify-between gap-4 flex-wrap">
    <div class="relative max-w-sm w-full">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
        <Input bind:value={search} placeholder="Cari ID atau nama departemen..." class="pl-10 h-10 bg-card/60 border-border/60" />
    </div>
    <Button onclick={onadd} class="gap-2 shadow-lg shadow-pink-500/20">
        <Plus class="size-4" /> Tambah Departemen
    </Button>
</div>

<Card.Root class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl">
    {#if departemenOptions.length === 0}
        <div class="p-16 text-center">
            <div class="size-14 rounded-2xl bg-pink-500/10 flex items-center justify-center mx-auto mb-4">
                <Layers class="size-7 text-pink-400/60" />
            </div>
            <p class="text-muted-foreground font-medium">Belum ada departemen</p>
        </div>
    {:else}
        <div class="p-4 space-y-3">
            {#each departemenOptions as dept}
                {@const deptCount = phoneCountByDept(dept.id)}
                <div class="rounded-xl border border-border/60 bg-card p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="size-3 rounded-full shrink-0" style="background: {dept.color}"></span>
                        <Badge style="background: {dept.color}20; color: {dept.color}; border-color: {dept.color}50;" class="px-3 py-1 border">{dept.name}</Badge>
                        <span class="text-xs text-muted-foreground">{deptCount} perangkat</span>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-pink-500 hover:bg-pink-500/10" onclick={() => onedit(dept)}>
                            <Edit class="size-3.5" />
                        </Button>
                        <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-red-400 hover:bg-red-500/10" onclick={() => ondelete(dept)} disabled={deptCount > 0}>
                            <Trash2 class="size-3.5" />
                        </Button>
                    </div>
                </div>
            {/each}
        </div>
    {/if}
</Card.Root>
