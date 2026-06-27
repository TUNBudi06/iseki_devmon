<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import storage from '$routes/storage';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Search as DtSearch, RowsPerPage, RowCount, Pagination } from '@vincjo/datatables';
    import {
        Smartphone, Plus, Edit, Trash2, Search, AppWindow,
        Tablet, Cpu, HardDrive, Image, QrCode, Check, X,
        CircleCheck, CircleX,
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { listDevice } from '$routes/admin';
    import type { PhoneList, DepartemenOption } from './types';

    let {
        allPhones,
        search = $bindable(''),
        departemenFilter = $bindable('all'),
        departemenOptions,
        table,
        onadd,
        onedit,
        ondelete,
        onqr,
    }: {
        allPhones: PhoneList[];
        search?: string;
        departemenFilter?: string;
        departemenOptions: DepartemenOption[];
        table: any | null;
        onadd: () => void;
        onedit: (phone: PhoneList) => void;
        ondelete: (phone: PhoneList) => void;
        onqr: (phone: PhoneList) => void;
    } = $props();

    let approvingId = $state<number | null>(null);

    function handleApprove(phone: PhoneList) {
        approvingId = phone.id;
        router.post(listDevice.phone.approve({ id: phone.id }).url, {}, {
            preserveScroll: true,
            onSuccess: () => { approvingId = null; toast.success('Perangkat disetujui'); },
            onError: () => { approvingId = null; toast.error('Gagal menyetujui perangkat'); },
        });
    }

    function storageUrl(path: string | null): string | null {
        if (!path) return null;
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }

    function formatPrice(val: string): string {
        const num = parseInt(val.replace(/\D/g, ''), 10);
        if (isNaN(num)) return val;
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
    }

    function deptNameColor(id: string) {
        return departemenOptions.find(d => d.id === id);
    }
    const deptMap = $derived.by(() => {
        const m = new Map(departemenOptions.map(d => [d.id, d]));
        return m;
    });
</script>

<div class="flex items-center justify-between gap-4 flex-wrap">
    <div class="relative max-w-sm w-full">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
        <Input bind:value={search} placeholder="Cari model ID, nama, brand..." class="pl-10 h-10 bg-card/60 border-border/60" />
    </div>
    <Button onclick={onadd} class="gap-2 shadow-lg shadow-pink-500/20">
        <Plus class="size-4" /> Tambah Phone
    </Button>
</div>

<!-- Departemen Filter -->
<div class="flex items-center gap-2 flex-wrap">
    <button onclick={() => departemenFilter = 'all'} class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 {departemenFilter === 'all' ? 'bg-pink-500/15 text-pink-500 shadow-sm' : 'text-muted-foreground hover:text-foreground hover:bg-muted/40'}">
        Semua ({allPhones.length})
    </button>
    {#each departemenOptions as dept}
        <button onclick={() => departemenFilter = dept.id} class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all duration-200" style="{departemenFilter === dept.id ? `background: ${dept.color}25; color: ${dept.color}; box-shadow: 0 1px 3px ${dept.color}30;` : 'color: var(--muted-foreground)'}"
            onmouseenter={(e) => { if (departemenFilter !== dept.id) e.currentTarget.style.background = `${dept.color}10`; }}
            onmouseleave={(e) => { if (departemenFilter !== dept.id) e.currentTarget.style.background = 'transparent'; }}
        >
            <span class="size-2 rounded-full inline-block mr-1.5" style="background: {dept.color}"></span>
            {dept.name} ({allPhones.filter(p => p.departemen === dept.id).length})
        </button>
    {/each}
</div>

<Card.Root class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl">
    {#if !table || table.rows.length === 0}
        <div class="p-16 text-center">
            <div class="size-14 rounded-2xl bg-pink-500/10 flex items-center justify-center mx-auto mb-4">
                <AppWindow class="size-7 text-pink-400/60" />
            </div>
            <p class="text-muted-foreground font-medium">
                {search || departemenFilter !== 'all' ? 'Tidak ada phone yang cocok' : 'Belum ada phone terdaftar'}
            </p>
            {#if !search && departemenFilter === 'all'}
                <Button variant="outline" size="sm" onclick={onadd} class="mt-4 gap-2">
                    <Plus class="size-3.5" /> Tambah Phone
                </Button>
            {/if}
        </div>
    {:else}
        <div class="flex items-center gap-3 px-6 py-3 border-b border-border/30 bg-muted/10 flex-wrap">
            <DtSearch table={table} />
            <RowsPerPage table={table} />
        </div>
        <div class="p-4 space-y-3">
            {#each table.rows as phone (phone.id)}
                <div class="rounded-xl border border-border/60 bg-card p-4 space-y-3 {phone.deleted_at ? 'opacity-50' : ''}">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0">
                            {#if phone.thumbnail}
                                <img src={storageUrl(phone.thumbnail)} alt={phone.model_name} class="size-14 rounded-xl object-cover ring-1 ring-pink-300/30" />
                            {:else if phone.list_photos && phone.list_photos.length > 0}
                                <img src={storageUrl(phone.list_photos[0])} alt={phone.model_name} class="size-14 rounded-xl object-cover ring-1 ring-pink-300/30" />
                            {:else}
                                <div class="size-14 rounded-xl bg-pink-500/10 flex items-center justify-center ring-1 ring-pink-300/20"><Image class="size-6 text-pink-400/60" /></div>
                            {/if}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="font-semibold text-sm leading-tight">{phone.model_name}</div>
                                    <code class="text-xs font-mono text-muted-foreground">{phone.model_id}</code>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    {#if !phone.registered}
                                        <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-emerald-500 hover:bg-emerald-500/10" onclick={() => onqr(phone)} title="QR Code"><QrCode class="size-3.5" /></Button>
                                    {/if}
                                    {#if !phone.approved}
                                        <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-amber-500 hover:bg-amber-500/10" onclick={() => handleApprove(phone)} title="Setujui" disabled={approvingId === phone.id}>
                                            {#if approvingId === phone.id}
                                                <div class="size-3.5 border-2 border-amber-500 border-t-transparent rounded-full animate-spin" />
                                            {:else}
                                                <Check class="size-3.5" />
                                            {/if}
                                        </Button>
                                    {/if}
                                    <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-pink-500 hover:bg-pink-500/10" onclick={() => onedit(phone)}><Edit class="size-3.5" /></Button>
                                    <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-red-400 hover:bg-red-500/10" onclick={() => ondelete(phone)}><Trash2 class="size-3.5" /></Button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                        <div class="flex items-center gap-1.5">
                            <Badge variant="outline" class="text-[10px] bg-violet-500/10 border-violet-300/30 text-violet-600 font-medium px-2 py-0">{phone.brand?.name ?? phone.brand_id}</Badge>
                            <Badge style="background: {deptMap.get(phone.departemen)?.color ?? '#f59e0b'}20; color: {deptMap.get(phone.departemen)?.color ?? '#f59e0b'}; border-color: {deptMap.get(phone.departemen)?.color ?? '#f59e0b'}50;" class="text-[10px] px-2 py-0 border">{deptMap.get(phone.departemen)?.name ?? phone.departemen}</Badge>
                        </div>
                        <div>
                            {#if phone.model_type === 'Phone'}
                                <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 gap-1 text-[10px] px-2 py-0"><Smartphone class="size-3" /> Phone</Badge>
                            {:else}
                                <Badge class="bg-amber-500/15 text-amber-600 border-amber-300/30 gap-1 text-[10px] px-2 py-0"><Tablet class="size-3" /> Tablet</Badge>
                            {/if}
                        </div>
                        <div class="flex items-center gap-1"><Cpu class="size-3 text-muted-foreground" /> {phone.ram}</div>
                        <div class="flex items-center gap-1"><HardDrive class="size-3 text-muted-foreground" /> {phone.storage}</div>
                        <div class="text-muted-foreground">📅 {phone.buy_date}</div>
                        <div class="font-semibold text-emerald-600 dark:text-emerald-400">{formatPrice(phone.price)}</div>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-1">
                        {#if phone.imei}
                            <Badge variant="outline" class="bg-primary/10 border-primary/30 text-primary gap-1.5 text-[11px] font-mono font-bold px-2.5 py-1"><span class="text-[9px] uppercase tracking-wider text-primary/70 font-bold">IMEI</span> {phone.imei}</Badge>
                        {/if}
                        {#if phone.mac_address}
                            <Badge variant="outline" class="bg-primary/10 border-primary/30 text-primary gap-1.5 text-[11px] font-mono font-bold px-2.5 py-1"><span class="text-[9px] uppercase tracking-wider text-primary/70 font-bold">MAC</span> {phone.mac_address}</Badge>
                        {/if}
                    </div>
                    <div class="flex flex-wrap gap-1.5 pt-1 border-t border-border/30">
                        {#if phone.registered}
                            <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 gap-1 text-xs"><CircleCheck class="size-3" /> Registered</Badge>
                        {:else}
                            <Badge variant="secondary" class="bg-rose-500/10 text-rose-500 border-rose-300/30 gap-1 text-xs"><CircleX class="size-3" /> Unregistered</Badge>
                        {/if}
                        {#if !phone.approved}
                            <Badge variant="outline" class="bg-amber-500/10 border-amber-300/30 text-amber-500 gap-1 text-xs">Pending</Badge>
                        {:else}
                            <Badge class="bg-sky-500/15 text-sky-600 border-sky-300/30 gap-1 text-xs"><Check class="size-3" /> Disetujui</Badge>
                        {/if}
                        {#if phone.deleted_at}
                            <Badge variant="outline" class="bg-red-500/10 border-red-300/30 text-red-500 gap-1 text-xs"><X class="size-3" /> Deleted</Badge>
                        {/if}
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
