<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import storage from '$routes/storage';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Separator } from '$shadcn/components/ui/separator';
    import { Checkbox } from '$shadcn/components/ui/checkbox';
    import {
        ArrowLeft, Search, Printer, CheckSquare, Square,
        Smartphone, Tablet, QrCode, Image, Check, X, Layers,
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { goBack } from '$lib/navigation.ts';
    import { printPage } from '$routes/admin';
    // ─── Types ───────────────────────────────────────────────────
    type PhoneList = {
        id: number; brand_id: string; model_id: string; model_name: string;
        model_type: string; ram: string; storage: string; price: string;
        thumbnail: string | null; list_photos: string[] | null;
        registered: boolean; approved: boolean; hash_token: string | null;
        brand: { id: string; name: string } | null; departemen: string;
    };

    type DepartemenOption = { id: string; name: string; color: string };

    let { phoneLists, departemenOptions }: {
        phoneLists: PhoneList[];
        departemenOptions: DepartemenOption[];
    } = $props();

    // ─── State ───────────────────────────────────────────────────
    let search = $state('');
    let departemenFilter = $state('all');
    let selectedIds = $state<Set<number>>(new Set());

    // ─── Derived ─────────────────────────────────────────────────
    const filtered = $derived.by(() => {
        let items = phoneLists;
        if (departemenFilter !== 'all') items = items.filter(p => p.departemen === departemenFilter);
        if (search.trim()) {
            const q = search.toLowerCase();
            items = items.filter(p =>
                p.model_id.toLowerCase().includes(q) || p.model_name.toLowerCase().includes(q) ||
                p.brand?.name.toLowerCase().includes(q) || p.model_type.toLowerCase().includes(q)
            );
        }
        return items;
    });

    const allFilteredSelected = $derived(filtered.length > 0 && filtered.every(p => selectedIds.has(p.id)));
    const totalSelected = $derived(selectedIds.size);

    // ─── Actions ─────────────────────────────────────────────────
    function toggleSelect(id: number) {
        const next = new Set(selectedIds);
        if (next.has(id)) next.delete(id); else next.add(id);
        selectedIds = next;
    }

    function toggleSelectAll() {
        if (allFilteredSelected) {
            // Deselect all visible
            const next = new Set(selectedIds);
            for (const p of filtered) next.delete(p.id);
            selectedIds = next;
        } else {
            // Select all visible
            const next = new Set(selectedIds);
            for (const p of filtered) next.add(p.id);
            selectedIds = next;
        }
    }

    function goToPrint() {
        if (selectedIds.size === 0) {
            toast.error('Pilih minimal satu device');
            return;
        }
        const ids = Array.from(selectedIds).join(',');
        router.visit(printPage().url + '?ids=' + ids);
    }

    function storageUrl(path: string | null): string | null {
        if (!path) return null;
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }

    function deptColor(id: string) {
        return departemenOptions.find(d => d.id === id);
    }

    // Pre-compute departemen lookup for template use
    const deptMap = $derived.by(() => {
        const m = new Map<string, DepartemenOption>();
        for (const d of departemenOptions) m.set(d.id, d);
        return m;
    });
</script>

<svelte:head>
    <title>Pilih Device untuk Print QR</title>
</svelte:head>

<div class="min-h-screen bg-linear-to-br from-pink-50 via-pink-100 to-pink-50">
    <!-- Header -->
    <div class="sticky top-0 z-40 border-b border-border/60 bg-card/80 backdrop-blur-xl">
        <div class="px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" onclick={goBack}><ArrowLeft class="size-5" /></Button>
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-xl bg-pink-500/20 flex items-center justify-center"><QrCode class="size-5 text-pink-400" /></div>
                        <h1 class="text-xl font-bold tracking-tight">Pilih Device untuk Print QR</h1>
                    </div>
                    <p class="text-xs text-muted-foreground ml-0.5 mt-0.5">Pilih device yang ingin dicetak QR-nya</p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6 space-y-6 max-w-5xl mx-auto">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="relative max-w-sm w-full">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                    <Input bind:value={search} placeholder="Cari device..." class="pl-10 h-10 bg-card/60 border-border/60 w-64" />
                </div>
                <!-- Departemen Filter -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button onclick={() => departemenFilter = 'all'} class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all {departemenFilter === 'all' ? 'bg-pink-500/15 text-pink-500 shadow-sm' : 'text-muted-foreground hover:text-foreground hover:bg-muted/40'}">
                        Semua ({phoneLists.length})
                    </button>
                    {#each departemenOptions as dept}
                        <button onclick={() => departemenFilter = dept.id} class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all" style="{departemenFilter === dept.id ? `background: ${dept.color}25; color: ${dept.color}; box-shadow: 0 1px 3px ${dept.color}30;` : 'color: var(--muted-foreground)'}">
                            <span class="size-2 rounded-full inline-block mr-1" style="background: {dept.color}"></span>
                            {dept.name} ({phoneLists.filter(p => p.departemen === dept.id).length})
                        </button>
                    {/each}
                </div>
            </div>
        </div>

        <!-- Select All + Selected Count -->
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                <Checkbox checked={allFilteredSelected} onCheckedChange={toggleSelectAll} />
                Pilih Semua ({filtered.length} device)
            </label>
            <div class="flex items-center gap-3">
                {#if totalSelected > 0}
                    <Badge class="bg-pink-500/15 text-pink-600 border-pink-300/30 gap-1.5 px-3 py-1">
                        <CheckSquare class="size-3.5" /> {totalSelected} device dipilih
                    </Badge>
                {/if}
                <Button onclick={goToPrint} disabled={totalSelected === 0} class="gap-2 shadow-lg shadow-pink-500/20 bg-linear-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700">
                    <Printer class="size-4" />
                    Generate QR ({totalSelected})
                </Button>
            </div>
        </div>

        <!-- Device List -->
        <Card.Root class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl">
            {#if filtered.length === 0}
                <div class="p-16 text-center">
                    <div class="size-14 rounded-2xl bg-pink-500/10 flex items-center justify-center mx-auto mb-4">
                        <QrCode class="size-7 text-pink-400/60" />
                    </div>
                    <p class="text-muted-foreground font-medium">Tidak ada device yang cocok</p>
                </div>
            {:else}
                <div class="divide-y divide-border/30">
                    {#each filtered as phone (phone.id)}
                        {@const isSelected = selectedIds.has(phone.id)}
                        <div
                            class="flex items-center gap-4 p-4 transition-colors cursor-pointer hover:bg-pink-500/5 {isSelected ? 'bg-pink-500/8 ring-1 ring-inset ring-pink-500/20' : ''}"
                            onclick={() => toggleSelect(phone.id)}
                            onkeydown={(e: KeyboardEvent) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleSelect(phone.id); } }}
                            role="checkbox"
                            tabindex="0"
                            aria-checked={isSelected}
                        >
                            <!-- Checkbox -->
                            <div class="shrink-0">
                                <Checkbox checked={isSelected} />
                            </div>

                            <!-- Thumbnail -->
                            <div class="shrink-0">
                                {#if phone.thumbnail}
                                    <img src={storageUrl(phone.thumbnail)} alt={phone.model_name} class="size-12 rounded-lg object-cover ring-1 ring-pink-300/30" />
                                {:else if phone.list_photos && phone.list_photos.length > 0}
                                    <img src={storageUrl(phone.list_photos[0])} alt={phone.model_name} class="size-12 rounded-lg object-cover ring-1 ring-pink-300/30" />
                                {:else}
                                    <div class="size-12 rounded-lg bg-pink-500/10 flex items-center justify-center ring-1 ring-pink-300/20"><Image class="size-5 text-pink-400/60" /></div>
                                {/if}
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-sm">{phone.model_name}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <code class="text-xs font-mono text-muted-foreground">{phone.model_id}</code>
                                    <Badge variant="outline" class="text-[10px] bg-violet-500/10 border-violet-300/30 text-violet-600 px-1.5 py-0">{phone.brand?.name ?? phone.brand_id}</Badge>
                                    {#if deptMap.has(phone.departemen)}
                                        {@const dc = deptMap.get(phone.departemen)!}
                                        <Badge style="background: {dc.color}20; color: {dc.color}; border-color: {dc.color}50;" class="text-[10px] px-1.5 py-0 border">{dc.name}</Badge>
                                    {/if}
                                    {#if phone.model_type === 'Phone'}
                                        <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 gap-0.5 text-[10px] px-1.5 py-0"><Smartphone class="size-2.5" /> Phone</Badge>
                                    {:else}
                                        <Badge class="bg-amber-500/15 text-amber-600 border-amber-300/30 gap-0.5 text-[10px] px-1.5 py-0"><Tablet class="size-2.5" /> Tablet</Badge>
                                    {/if}
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="shrink-0 flex items-center gap-1.5">
                                {#if phone.registered}
                                    <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 gap-0.5 text-[10px] px-1.5 py-0"><Check class="size-2.5" /> Registered</Badge>
                                {:else}
                                    <Badge variant="secondary" class="bg-rose-500/10 text-rose-500 border-rose-300/30 gap-0.5 text-[10px] px-1.5 py-0"><X class="size-2.5" /> Unregistered</Badge>
                                {/if}
                            </div>

                            <!-- Check indicator -->
                            <div class="shrink-0 size-6 rounded-full border-2 flex items-center justify-center transition-all {isSelected ? 'bg-pink-500 border-pink-500' : 'border-muted-foreground/30'}">
                                {#if isSelected}
                                    <Check class="size-3.5 text-white" />
                                {/if}
                            </div>
                        </div>
                    {/each}
                </div>
            {/if}
        </Card.Root>

        <!-- Bottom Sticky Bar -->
        {#if totalSelected > 0}
            <div class="sticky bottom-4 z-40 flex items-center justify-between rounded-2xl bg-card/95 backdrop-blur-xl border border-border/60 shadow-2xl px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center"><QrCode class="size-5 text-pink-400" /></div>
                    <div>
                        <p class="font-semibold text-sm">{totalSelected} device dipilih</p>
                        <p class="text-xs text-muted-foreground">QR code akan digenerate untuk device yang dipilih</p>
                    </div>
                </div>
                <Button onclick={goToPrint} class="gap-2 shadow-lg shadow-pink-500/20 bg-linear-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 px-6">
                    <Printer class="size-4" />
                    Generate QR ({totalSelected})
                </Button>
            </div>
        {/if}
    </div>
</div>
