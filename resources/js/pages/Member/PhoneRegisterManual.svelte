<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Badge } from '$shadcn/components/ui/badge';
    import { ArrowLeft, Check, Smartphone, Cpu, HardDrive, CalendarDays, Banknote, Tag, SeparatorHorizontal } from '@lucide/svelte';
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Particles from '$shadcn/components/Particles.svelte';
    import { loginMember } from '$routes/user/user';
    import { deviceNotRegister, deviceRegisterManual } from '$routes/user';
    import { saveDeviceAuth } from '$lib/indexedDB';
    import { home } from '$routes';
    import { toast } from 'svelte-sonner';
    import { Toaster } from '$shadcn/components/ui/sonner';
    import { Separator } from '$shadcn/components/ui/separator';
    import { Loader2 } from '@lucide/svelte';

    type Brand = { id: string; name: string };

    let { brands, nextIncrement }: { brands: Brand[]; nextIncrement: string } = $props();

    const form = useForm({
        brand_id: '',
        model_id: '',
        model_name: '',
        model_type: 'Phone',
        buy_date: '',
        price: '',
        ram: '',
        storage: '',
        imei: '',
        mac_address: '',
    });

    const suggestedModelId = $derived.by(() => {
        const type = form.model_type === 'Tablet' ? 't' : 'p';
        const words = form.model_name.trim().split(/\s+/);
        if (words.length === 0 || !words[0]) return '';
        const code = words.map((word) => {
            const first = word[0].toLowerCase();
            const digits = word.match(/\d+/g)?.[0] || '';
            return first + digits;
        }).join('');
        return `dev-${type}${code}-${nextIncrement}`;
    });

    // Auto-fill model_id when suggestion changes but only if user hasn't manually edited it
    let userEditedModelId = $state(false);
    $effect(() => {
        if (suggestedModelId && !userEditedModelId) {
            form.model_id = suggestedModelId;
        }
    });

    let isSubmitting = $state(false);

    async function handleSubmit() {
        isSubmitting = true;

        try {
            const res = await fetch(deviceRegisterManual.store().url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    brand_id: form.brand_id,
                    model_id: form.model_id,
                    model_name: form.model_name,
                    model_type: form.model_type,
                    buy_date: form.buy_date,
                    price: form.price,
                    ram: form.ram,
                    storage: form.storage,
                    imei: form.imei || null,
                    mac_address: form.mac_address || null,
                }),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                const msg = data.message ?? (data.errors ? Object.values(data.errors).flat().join(', ') : null) ?? 'Gagal mengajukan perangkat (HTTP ' + res.status + ')';
                toast.error(msg);
                isSubmitting = false;
                return;
            }

            // Save JWT to IndexedDB
            await saveDeviceAuth(data.device_id, data.jwt);

            toast.success('Perangkat berhasil didaftarkan! Menunggu persetujuan admin.');
            setTimeout(() => router.visit(loginMember(data.device_id).url), 1500);
        } catch (err) {
            console.error('Manual register error:', err);
            toast.error('Gagal mengajukan perangkat. Coba lagi.');
        } finally {
            isSubmitting = false;
        }
    }
    function onModelIdInput() {
        userEditedModelId = form.model_id !== suggestedModelId;
    }
</script>

<LayoutBG class="min-h-screen bg-background flex flex-col items-center justify-center p-4 md:p-6 relative overflow-hidden">
    <Particles particleCount={200} particleColors={['#000000', '#ff00ae', '#ffffff']} particleBaseSize={100} speed={0.05} class="absolute inset-0 z-0 opacity-30" />

    <div class="w-full max-w-lg relative z-10">
        <button onclick={() => router.visit(deviceNotRegister().url)} class="mb-4 flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors">
            <ArrowLeft class="size-4" />
            Kembali
        </button>

        <Card.Root class="border-border/60 bg-card/80 backdrop-blur-xl">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                        <Smartphone class="size-5 text-pink-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Daftarkan Perangkat Manual</Card.Title>
                        <Card.Description>Data akan ditinjau admin sebelum dapat digunakan</Card.Description>
                    </div>
                </div>
                <Badge variant="outline" class="mt-3 bg-amber-500/10 border-amber-300/30 text-amber-500 text-xs">Menunggu Persetujuan Admin</Badge>
            </Card.Header>

            <Card.Content class="space-y-4">
                <!-- Brand -->
                <div class="space-y-2">
                    <Label for="brand" class="text-sm font-medium">Brand</Label>
                    <select id="brand" bind:value={form.brand_id} class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        <option value="">Pilih Brand...</option>
                        {#each brands as brand}
                            <option value={brand.id}>{brand.name}</option>
                        {/each}
                    </select>
                    {#if form.errors.brand_id}<p class="text-xs text-rose-400">{form.errors.brand_id}</p>{/if}
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="model_id" class="text-sm font-medium">Model ID</Label>
                        <Input id="model_id" bind:value={form.model_id} oninput={onModelIdInput} placeholder="Otomatis terisi..." class="h-10" />
                        {#if form.errors.model_id}<p class="text-xs text-rose-400">{form.errors.model_id}</p>{/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="model_name" class="text-sm font-medium">Model Name</Label>
                        <Input id="model_name" bind:value={form.model_name} placeholder="Samsung Galaxy S24" class="h-10" />
                        {#if form.errors.model_name}<p class="text-xs text-rose-400">{form.errors.model_name}</p>{/if}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="model_type" class="text-sm font-medium">Tipe</Label>
                        <select id="model_type" bind:value={form.model_type} class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            <option value="Phone">Phone</option>
                            <option value="Tablet">Tablet</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <Label for="buy_date" class="text-sm font-medium">Tanggal Pembelian</Label>
                        <Input id="buy_date" bind:value={form.buy_date} placeholder="2025-01-15" class="h-10" />
                        {#if form.errors.buy_date}<p class="text-xs text-rose-400">{form.errors.buy_date}</p>{/if}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="ram" class="text-sm font-medium">RAM</Label>
                        <Input id="ram" bind:value={form.ram} placeholder="8GB" class="h-10" />
                        {#if form.errors.ram}<p class="text-xs text-rose-400">{form.errors.ram}</p>{/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="storage" class="text-sm font-medium">Storage</Label>
                        <Input id="storage" bind:value={form.storage} placeholder="256GB" class="h-10" />
                        {#if form.errors.storage}<p class="text-xs text-rose-400">{form.errors.storage}</p>{/if}
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="price" class="text-sm font-medium">Harga</Label>
                    <Input id="price" bind:value={form.price} placeholder="5000000" class="h-10" />
                    {#if form.errors.price}<p class="text-xs text-rose-400">{form.errors.price}</p>{/if}
                </div>

                <!-- ─── IMEI & MAC Address ─── -->
                <Separator class="opacity-50" />
                <div>
                    <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1">Identifikasi Hardware</p>
                    <p class="text-xs text-muted-foreground mb-3">Isi minimal salah satu (IMEI untuk HP, MAC Address untuk tablet/hp). Lihat di <b>Settings &gt; About Phone</b>.</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="imei" class="text-sm font-medium">IMEI <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                            <Input id="imei" bind:value={form.imei} placeholder="352678094561230" maxlength={15} class="h-10 font-mono" />
                            {#if form.errors.imei}<p class="text-xs text-rose-400">{form.errors.imei}</p>{/if}
                        </div>
                        <div class="space-y-2">
                            <Label for="mac" class="text-sm font-medium">MAC Address <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                            <Input id="mac" bind:value={form.mac_address} placeholder="00:1A:2B:3C:4D:5E" maxlength={17} class="h-10 font-mono" />
                            {#if form.errors.mac_address}<p class="text-xs text-rose-400">{form.errors.mac_address}</p>{/if}
                        </div>
                    </div>
                </div>
            </Card.Content>

            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={() => router.visit(deviceNotRegister().url)} disabled={isSubmitting}>
                    Batal
                </Button>
                <Button onclick={handleSubmit} disabled={isSubmitting || !form.brand_id || !form.model_id.trim() || !form.model_name.trim()} class="gap-2">
                    {#if isSubmitting}
                        <Loader2 class="size-4 animate-spin" />
                        Mengirim...
                    {:else}
                        <Check class="size-4" />
                        Ajukan
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>

    <Toaster richColors position="top-right" duration={3000} />
</LayoutBG>
