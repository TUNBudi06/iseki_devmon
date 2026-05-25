<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Badge } from '$shadcn/components/ui/badge';
    import storage from '$routes/storage';
    import {
        ShieldCheck,
        Search,
        ArrowLeft,
        CheckCircle2,
        XCircle,
        Smartphone,
        Cpu,
        HardDrive,
        QrCode,
        ScanQrCode,
    } from '@lucide/svelte';
    import { dashboard, checkDevice } from '$routes/admin';

    type DeviceInfo = {
        model_id: string;
        model_name: string;
        model_type: string;
        brand_name: string | null;
        registered: boolean;
        approved: boolean;
        ram: string;
        storage: string;
        thumbnail: string | null;
        hash_token: string | null;
    };

    let token = $state('');
    let checking = $state(false);
    let device = $state<DeviceInfo | null>(null);
    let errorMsg = $state('');

    function assetUrl(path: string | null): string | null {
        if (!path) return null;
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }

    async function handleCheck() {
        if (!token.trim()) return;

        checking = true;
        errorMsg = '';
        device = null;

        try {
            const res = await fetch(checkDevice.verify().url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ token: token.trim() }),
            });

            const data = await res.json();

            if (!res.ok || !data.valid) {
                errorMsg = data.message ?? 'Token tidak cocok';
                checking = false;
                return;
            }

            device = data.device;
        } catch {
            errorMsg = 'Gagal memverifikasi. Coba lagi.';
        } finally {
            checking = false;
        }
    }

    function goBack() {
        router.visit(dashboard().url);
    }
</script>

<div class="min-h-screen bg-mesh-pink p-6 space-y-6">
    <!-- ──────── Header ──────── -->
    <div class="flex items-center gap-4">
        <Button variant="ghost" size="icon" onclick={goBack}>
            <ArrowLeft class="size-5" />
        </Button>
        <div>
            <div class="flex items-center gap-2">
                <ScanQrCode class="size-6 text-emerald-400" />
                <h1 class="text-2xl font-bold">Check Device</h1>
            </div>
            <p class="text-sm text-muted-foreground mt-1">
                Scan atau masukkan token QR untuk verifikasi perangkat
            </p>
        </div>
    </div>

    <!-- ──────── Input Card ──────── -->
    <Card.Root class="max-w-xl border-border/60 bg-card/60 backdrop-blur-xl">
        <Card.Content class="p-6 space-y-4">
            <div class="space-y-2">
                <Label for="token" class="text-sm font-medium">Token / Kode QR</Label>
                <div class="relative">
                    <QrCode class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                    <Input
                        id="token"
                        bind:value={token}
                        placeholder="Masukkan hash_token atau scan QR..."
                        class="pl-10 h-12 font-mono text-sm"
                    />
                </div>
            </div>

            <Button
                class="w-full gap-2 h-11"
                disabled={checking || !token.trim()}
                onclick={handleCheck}
            >
                {#if checking}
                    <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                    Memverifikasi...
                {:else}
                    <ShieldCheck class="size-4" />
                    Verifikasi Perangkat
                {/if}
            </Button>
        </Card.Content>
    </Card.Root>

    <!-- ──────── Error Result ──────── -->
    {#if errorMsg}
        <Card.Root class="max-w-xl border-red-500/30 bg-card/60 backdrop-blur-xl">
            <Card.Content class="p-6">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded-full bg-red-500/20 flex items-center justify-center shrink-0">
                        <XCircle class="size-6 text-red-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-lg text-red-400">Tidak Cocok</div>
                        <p class="text-sm text-muted-foreground">{errorMsg}</p>
                    </div>
                </div>
            </Card.Content>
        </Card.Root>
    {/if}

    <!-- ──────── Device Info Result ──────── -->
    {#if device}
        <Card.Root class="max-w-xl border-emerald-500/30 bg-card/60 backdrop-blur-xl overflow-hidden">
            <!-- Top gradient bar -->
            <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-400" />

            <Card.Content class="p-6 space-y-5">
                <!-- Header -->
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0 ring-2 ring-emerald-500/30">
                        <CheckCircle2 class="size-7 text-emerald-400" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-lg">{device.model_name}</span>
                            <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 text-xs">
                                <CheckCircle2 class="size-3 mr-1" /> Terverifikasi
                            </Badge>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-muted-foreground mt-0.5">
                            <Badge variant="outline" class="font-mono text-xs">{device.model_id}</Badge>
                            {#if device.brand_name}
                                <span>·</span>
                                <span>{device.brand_name}</span>
                            {/if}
                            <span>·</span>
                            <span>{device.model_type}</span>
                        </div>
                    </div>
                </div>

                <!-- Thumbnail + Specs -->
                <div class="flex gap-4">
                    {#if device.thumbnail}
                        <img src={assetUrl(device.thumbnail)} alt={device.model_name} class="size-20 rounded-xl object-cover ring-1 ring-emerald-300/30 shrink-0" />
                    {:else}
                        <div class="size-20 rounded-xl bg-emerald-500/10 flex items-center justify-center ring-1 ring-emerald-300/20 shrink-0">
                            <Smartphone class="size-8 text-emerald-400/60" />
                        </div>
                    {/if}

                    <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm flex-1">
                        <div>
                            <span class="text-xs text-muted-foreground">RAM</span>
                            <div class="font-medium flex items-center gap-1">
                                <Cpu class="size-3.5 text-muted-foreground/60" />
                                {device.ram}
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-muted-foreground">Storage</span>
                            <div class="font-medium flex items-center gap-1">
                                <HardDrive class="size-3.5 text-muted-foreground/60" />
                                {device.storage}
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-muted-foreground">Registered</span>
                            <div>
                                {#if device.registered}
                                    <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 text-xs">Registered</Badge>
                                {:else}
                                    <Badge variant="secondary" class="text-xs">Unregistered</Badge>
                                {/if}
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-muted-foreground">Approved</span>
                            <div>
                                {#if device.approved}
                                    <Badge class="bg-sky-500/15 text-sky-600 border-sky-300/30 text-xs">Disetujui</Badge>
                                {:else}
                                    <Badge variant="outline" class="text-xs">Pending</Badge>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hash token -->
                {#if device.hash_token}
                    <div class="rounded-lg bg-muted/30 p-3">
                        <div class="text-xs text-muted-foreground mb-1">Hash Token</div>
                        <code class="text-xs font-mono break-all text-emerald-600 dark:text-emerald-400">{device.hash_token}</code>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    {/if}
</div>
