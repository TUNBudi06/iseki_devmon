<script lang="ts">
    import { onDestroy, tick } from 'svelte';
    import { router } from '@inertiajs/svelte';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Separator } from '$shadcn/components/ui/separator';
    import storage from '$routes/storage';
    import {
        ShieldCheck,
        ArrowLeft,
        CheckCircle2,
        XCircle,
        Smartphone,
        Cpu,
        HardDrive,
        ScanQrCode,
        Camera,
        CameraOff,
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

    type VerifyResult = {
        valid: boolean;
        token_input: string;
        matched_by: string | null;
        message: string;
        device: DeviceInfo | null;
    };

    let checking = $state(false);
    let result = $state<VerifyResult | null>(null);
    let scanning = $state(false);
    let scanningError = $state('');
    let videoRef = $state<HTMLVideoElement | null>(null);
    let scannerInst: any = null;

    function assetUrl(path: string | null): string | null {
        if (!path) return null;
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }

    async function verifyToken(token: string) {
        checking = true;
        result = null;

        try {
            const res = await fetch(checkDevice.verify().url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ token }),
            });

            const data: VerifyResult = await res.json();
            result = data;
        } catch {
            result = { valid: false, token_input: token, matched_by: null, message: 'Gagal memverifikasi. Coba lagi.', device: null };
        } finally {
            checking = false;
        }
    }

    async function startScanner() {
        scanningError = '';
        scanning = true;
        await tick();

        const video = videoRef;
        if (!video) return;

        try {
            const { default: QrScanner } = await import('qr-scanner');
            scannerInst = new QrScanner(video, (res: any) => {
                if (res?.data) {
                    const token = res.data;
                    stopScanner();
                    verifyToken(token);
                }
            }, { highlightScanRegion: true, highlightCodeOutline: true });
            await scannerInst.start();
        } catch {
            scanningError = 'Kamera tidak tersedia atau izin ditolak.';
            scanning = false;
        }
    }

    function stopScanner() {
        if (scannerInst) {
            scannerInst.stop();
            scannerInst.destroy();
            scannerInst = null;
        }
        scanning = false;
    }

    onDestroy(() => {
        stopScanner();
    });

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
                Scan QR code dari perangkat untuk verifikasi
            </p>
        </div>
    </div>

    <!-- ──────── Scanner Card ──────── -->
    <Card.Root class="max-w-xl border-border/60 bg-card/60 backdrop-blur-xl">
        <Card.Content class="p-6 space-y-4">
            {#if !scanning && !result}
                <div class="text-center py-8 space-y-4">
                    <div class="size-20 rounded-full bg-emerald-500/20 flex items-center justify-center mx-auto">
                        <Camera class="size-10 text-emerald-400" />
                    </div>
                    <div>
                        <p class="font-medium">Scan QR Code Perangkat</p>
                        <p class="text-sm text-muted-foreground mt-1">
                            Arahkan kamera ke QR code yang ada di halaman dashboard perangkat
                        </p>
                    </div>
                    <Button size="lg" onclick={startScanner} class="gap-2">
                        <Camera class="size-4" />
                        Buka Kamera
                    </Button>
                </div>
            {/if}

            {#if scanning}
                <div class="relative rounded-xl overflow-hidden border border-border/60 bg-black">
                    <video bind:this={videoRef} class="w-full h-72 object-cover" muted playsinline />

                    {#if checking}
                        <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                            <div class="text-center text-white">
                                <div class="size-8 border-3 border-white border-t-transparent rounded-full animate-spin mx-auto mb-2" />
                                <p class="text-sm">Memverifikasi...</p>
                            </div>
                        </div>
                    {/if}

                    <div class="absolute top-3 right-3">
                        <Button size="sm" variant="secondary" onclick={stopScanner} class="gap-1.5 text-xs">
                            <CameraOff class="size-3.5" />
                            Tutup Kamera
                        </Button>
                    </div>
                </div>
            {/if}

            {#if scanningError}
                <div class="rounded-lg bg-red-500/10 p-3 text-sm text-red-400 text-center">
                    {scanningError}
                </div>
            {/if}

            {#if checking && !scanning}
                <div class="flex items-center justify-center gap-2 text-sm text-muted-foreground py-4">
                    <div class="size-4 border-2 border-primary border-t-transparent rounded-full animate-spin" />
                    Memverifikasi token...
                </div>
            {/if}
        </Card.Content>
    </Card.Root>

    <!-- ──────── Result ──────── -->
    {#if result}
        <Card.Root class="max-w-xl border-border/60 bg-card/60 backdrop-blur-xl overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r {result.valid ? 'from-emerald-500 to-emerald-400' : 'from-red-500 to-red-400'}" />

            <Card.Content class="p-6 space-y-5">
                <!-- Status Header -->
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded-full {result.valid ? 'bg-emerald-500/20 ring-emerald-500/30' : 'bg-red-500/20 ring-red-500/30'} flex items-center justify-center shrink-0 ring-2">
                        {#if result.valid}
                            <CheckCircle2 class="size-6 text-emerald-400" />
                        {:else}
                            <XCircle class="size-6 text-red-400" />
                        {/if}
                    </div>
                    <div>
                        <div class="font-bold text-lg {result.valid ? 'text-emerald-400' : 'text-red-400'}">
                            {result.valid ? '✓ Token Cocok' : '✗ Token Tidak Cocok'}
                        </div>
                        <p class="text-sm text-muted-foreground">{result.message}</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <Button variant="outline" size="sm" onclick={startScanner} class="gap-1.5">
                        <Camera class="size-3.5" />
                        Scan Lagi
                    </Button>
                </div>

                <!-- Device Info (hanya kalau valid) -->
                {#if result.valid && result.device}
                    <Separator class="opacity-40" />

                    <div class="space-y-3">
                        <div class="flex items-center gap-1.5 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            <Smartphone class="size-3.5" />
                            Informasi Perangkat
                        </div>

                        <div class="flex gap-4">
                            {#if result.device.thumbnail}
                                <img src={assetUrl(result.device.thumbnail)} alt={result.device.model_name} class="size-20 rounded-xl object-cover ring-1 ring-emerald-300/30 shrink-0" />
                            {:else}
                                <div class="size-20 rounded-xl bg-emerald-500/10 flex items-center justify-center ring-1 ring-emerald-300/20 shrink-0">
                                    <Smartphone class="size-8 text-emerald-400/60" />
                                </div>
                            {/if}

                            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm flex-1">
                                <div class="col-span-2">
                                    <span class="text-xs text-muted-foreground">Nama Perangkat</span>
                                    <div class="font-semibold flex items-center gap-2">
                                        {result.device.model_name}
                                        <Badge variant="outline" class="font-mono text-xs">{result.device.model_id}</Badge>
                                    </div>
                                </div>
                                {#if result.device.brand_name}
                                    <div>
                                        <span class="text-xs text-muted-foreground">Brand</span>
                                        <div class="font-medium">{result.device.brand_name}</div>
                                    </div>
                                {/if}
                                <div>
                                    <span class="text-xs text-muted-foreground">Tipe</span>
                                    <div class="font-medium">{result.device.model_type}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">RAM</span>
                                    <div class="font-medium flex items-center gap-1"><Cpu class="size-3.5 text-muted-foreground/60" /> {result.device.ram}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">Storage</span>
                                    <div class="font-medium flex items-center gap-1"><HardDrive class="size-3.5 text-muted-foreground/60" /> {result.device.storage}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">Registered</span>
                                    <div>{#if result.device.registered}<Badge class="bg-emerald-500/15 text-emerald-600 text-xs">Registered</Badge>{:else}<Badge variant="secondary" class="text-xs">Unregistered</Badge>{/if}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-muted-foreground">Approved</span>
                                    <div>{#if result.device.approved}<Badge class="bg-sky-500/15 text-sky-600 text-xs">Disetujui</Badge>{:else}<Badge variant="outline" class="text-xs">Pending</Badge>{/if}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    {/if}
</div>
