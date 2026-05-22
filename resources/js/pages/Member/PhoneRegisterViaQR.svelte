<script lang="ts">
    import { onDestroy, tick } from 'svelte';
    import { router } from '@inertiajs/svelte';
    import {
        QrCode,
        ArrowLeft,
        CheckCircle,
        AlertCircle,
        Smartphone,
        X,
        Loader2,
        Camera,
        FlipHorizontal,
        Zap,
    } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';

    import LayoutBG from '$/components/LayoutBG.svelte';
    import { Button } from '$shadcn/components/ui/button';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import * as Card from '$shadcn/components/ui/card';
    import * as Alert from '$shadcn/components/ui/alert';
    import QrScanner from 'qr-scanner';
    import { home } from '$routes';
    import { loginMember, deviceNotRegister, deviceVerify, deviceRegister } from '$routes/user';
    import { saveDeviceAuth } from '$lib/indexedDB';

    let videoElement: HTMLVideoElement | null = null;
    let qrScanner: QrScanner | null = null;
    let scanning = $state(false);
    let manualCode = $state('');
    let isLoading = $state(false);
    let error = $state('');
    let hasFlash = $state(false);
    let isFlashOn = $state(false);
    let showDeviceInfo = $state(false);

    let deviceInfo = $state<null | {
        model_id: string;
        model_name: string;
        model_type: string;
        brand_name: string | null;
        thumbnail: string | null;
    }>(null);

    async function startScan() {
        scanning = true;
        await tick();
        if (!videoElement) return;
        error = '';

        try {
            qrScanner = new QrScanner(
                videoElement,
                (result) => {
                    if (result?.data && !isLoading && !showDeviceInfo) {
                        verifyCode(result.data);
                    }
                },
                {
                    onDecodeError: () => {},
                    preferredCamera: 'environment',
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                    maxScansPerSecond: 10,
                    returnDetailedScanResult: true,
                },
            );

            await qrScanner.start();
            hasFlash = await qrScanner.hasFlash();
        } catch (err) {
            error = 'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.';
            scanning = false;
        }
    }

    async function stopScan() {
        if (qrScanner) {
            try {
                await qrScanner.stop();
                qrScanner.destroy();
                qrScanner = null;
            } catch (err) {
                console.error('Stop scan error:', err);
            }
        }
        scanning = false;
    }

    async function toggleFlash() {
        if (qrScanner && hasFlash) {
            try {
                if (isFlashOn) {
                    await qrScanner.turnFlashOff();
                    isFlashOn = false;
                } else {
                    await qrScanner.turnFlashOn();
                    isFlashOn = true;
                }
            } catch (err) {
                console.error('Flash error:', err);
            }
        }
    }

    async function switchCamera() {
        if (qrScanner) {
            try {
                const cameras = await QrScanner.listCameras(true);
                const currentCamera = await qrScanner.getCamera();
                const currentIndex = cameras.findIndex((cam) => cam.id === currentCamera);
                const nextCamera = cameras[(currentIndex + 1) % cameras.length];
                await qrScanner.setCamera(nextCamera.id);
            } catch (err) {
                console.error('Switch camera error:', err);
            }
        }
    }

    async function verifyCode(code: string) {
        await stopScan();
        isLoading = true;
        error = '';

        try {
            const res = await fetch(deviceVerify().url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ code }),
            });

            const data = await res.json();

            if (!res.ok) {
                error = data.message ?? 'Gagal memverifikasi perangkat';
                isLoading = false;
                return;
            }

            if (data.found && !data.registered) {
                deviceInfo = data.device;
                showDeviceInfo = true;
            } else {
                error = data.message ?? 'Perangkat tidak valid';
            }
        } catch {
            error = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
        } finally {
            isLoading = false;
        }
    }

    async function handleManualSubmit() {
        if (!manualCode.trim()) {
            error = 'Harap masukkan kode perangkat';
            return;
        }
        await verifyCode(manualCode.trim());
    }

    let isRegistering = $state(false);

    async function handleRegister() {
        if (!deviceInfo) return;
        isRegistering = true;
        error = '';

        try {
            const res = await fetch(deviceRegister().url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ model_id: deviceInfo.model_id }),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                error = data.message ?? 'Gagal mendaftarkan perangkat';
                isRegistering = false;
                return;
            }

            // Save JWT to IndexedDB
            await saveDeviceAuth(data.device_id, data.jwt);

            // Redirect to login member with device_id
            router.visit(loginMember({ query: { device_id: data.device_id } }).url);
        } catch {
            error = 'Gagal mendaftarkan perangkat. Silakan coba lagi.';
        } finally {
            isRegistering = false;
        }
    }

    function cancelRegistration() {
        scanning = false;
        manualCode = '';
        deviceInfo = null;
        error = '';
        isFlashOn = false;
        showDeviceInfo = false;
    }

    onDestroy(() => {
        if (qrScanner) {
            qrScanner.destroy();
            qrScanner = null;
        }
    });
</script>

<LayoutBG
    class="min-h-screen bg-background flex flex-col items-center justify-center p-6 md:p-8 gap-8 md:gap-12 relative overflow-hidden"
>
    <Particles
        particleCount={350}
        particleColors={['#000000', '#ff00ae', '#ffffff']}
        particleBaseSize={130}
        speed={0.1}
        class="absolute inset-0 z-0 opacity-50"
    />

    <!-- Back button -->
    <div class="w-full max-w-4xl relative z-10">
        <button
            onclick={() => router.visit(deviceNotRegister().url)}
            class="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors group"
        >
            <ArrowLeft class="size-4 group-hover:-translate-x-1 transition-transform" />
            Kembali
        </button>
    </div>

    <!-- Hero section -->
    <div class="text-center relative z-10 space-y-4 md:space-y-6 max-w-4xl mx-auto">
        <div class="inline-flex items-center gap-2 bg-primary/10 backdrop-blur-sm border border-primary/20 text-primary text-xs font-medium px-4 py-1.5 rounded-full animate-in fade-in slide-in-from-top-5 duration-500">
            <QrCode class="size-3" />
            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
            Pendaftaran Cepat
        </div>

        <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold tracking-tight">
            <span class="text-foreground">Scan QR Code</span>
        </h1>

        <p class="text-sm md:text-base text-muted-foreground max-w-2xl mx-auto">
            Scan QR code pada perangkat atau masukkan kode manual untuk memulai pendaftaran
        </p>
    </div>

    <!-- Main Content -->
    <div class="w-full max-w-2xl relative z-10 px-4">
        {#if !showDeviceInfo}
            <Card.Root class="border-border/60 bg-card/80 backdrop-blur-sm">
                <Card.Header>
                    <Card.Title class="text-xl font-bold text-center">
                        {scanning ? 'Arahkan Kamera ke QR Code' : 'Pilih Metode'}
                    </Card.Title>
                    <Card.Description class="text-center">
                        {scanning
                            ? 'Tempatkan QR code di dalam area scanner'
                            : 'Scan menggunakan kamera atau masukkan kode perangkat'}
                    </Card.Description>
                </Card.Header>

                <Card.Content class="space-y-6">
                    {#if error}
                        <Alert.Root variant="destructive">
                            <AlertCircle class="size-4" />
                            <Alert.Title>Gagal</Alert.Title>
                            <Alert.Description>{error}</Alert.Description>
                        </Alert.Root>
                    {/if}

                    {#if isLoading}
                        <div class="p-16 flex flex-col items-center gap-4">
                            <Loader2 class="size-10 text-primary animate-spin" />
                            <p class="text-sm text-muted-foreground">Memverifikasi perangkat...</p>
                        </div>
                    {:else if scanning}
                        <!-- Scanner -->
                        <div class="relative aspect-square w-full max-w-sm mx-auto rounded-xl overflow-hidden border-2 border-primary/30 bg-black">
                            <video bind:this={videoElement} class="w-full h-full object-cover"></video>
                            <div class="absolute inset-0 pointer-events-none">
                                <div class="absolute inset-0 border-2 border-primary/50 rounded-xl"></div>
                                <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-primary animate-scan"></div>
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 border-2 border-primary rounded-xl"></div>
                            </div>
                        </div>

                        <div class="flex justify-center gap-3 flex-wrap">
                            {#if hasFlash}
                                <Button onclick={toggleFlash} variant="outline" class="gap-2">
                                    <Zap class="size-4 {isFlashOn ? 'text-yellow-500 fill-yellow-500' : ''}" />
                                    {isFlashOn ? 'Flash On' : 'Flash Off'}
                                </Button>
                            {/if}
                            <Button onclick={switchCamera} variant="outline" class="gap-2">
                                <FlipHorizontal class="size-4" /> Ganti Kamera
                            </Button>
                            <Button onclick={stopScan} variant="outline" class="gap-2">
                                <X class="size-4" /> Batal
                            </Button>
                        </div>

                        <div class="text-center text-xs text-muted-foreground">
                            <p>Pastikan QR code terlihat jelas dan dalam pencahayaan yang cukup</p>
                        </div>
                    {:else}
                        <!-- Method selection -->
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                onclick={startScan}
                                class="group flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-border hover:border-primary/50 bg-card/50 hover:bg-primary/5 transition-all duration-300"
                            >
                                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <Camera class="size-6 text-primary" />
                                </div>
                                <div>
                                    <div class="font-semibold text-sm">Scan Kamera</div>
                                    <p class="text-xs text-muted-foreground mt-1">Gunakan kamera perangkat</p>
                                </div>
                            </button>
                            <button
                                onclick={() => document.getElementById('manual-input')?.scrollIntoView({ behavior: 'smooth' })}
                                class="group flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-border hover:border-primary/50 bg-card/50 hover:bg-primary/5 transition-all duration-300"
                            >
                                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <Smartphone class="size-6 text-primary" />
                                </div>
                                <div>
                                    <div class="font-semibold text-sm">Input Manual</div>
                                    <p class="text-xs text-muted-foreground mt-1">Masukkan model ID perangkat</p>
                                </div>
                            </button>
                        </div>

                        <div id="manual-input" class="space-y-4 pt-4 border-t border-border">
                            <div class="space-y-2">
                                <Label for="qr-code" class="text-sm font-medium">Model ID / Kode Perangkat</Label>
                                <div class="relative">
                                    <QrCode class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                                    <Input
                                        id="qr-code"
                                        type="text"
                                        bind:value={manualCode}
                                        placeholder="Contoh: Samsung-S24-001"
                                        class="pl-10 bg-background/50 border-border/60 focus:border-primary"
                                    />
                                </div>
                            </div>
                            <Button onclick={handleManualSubmit} disabled={isLoading || !manualCode.trim()} class="w-full gap-2">
                                {#if isLoading}
                                    <Loader2 class="size-4 animate-spin" /> Memverifikasi...
                                {:else}
                                    Verifikasi Kode <CheckCircle class="size-4" />
                                {/if}
                            </Button>
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>
        {:else if deviceInfo}
            <!-- Device Info Step -->
            <Card.Root class="border-border/60 bg-card/80 backdrop-blur-sm animate-in zoom-in-95 duration-200">
                <Card.Header class="text-center">
                    <div class="mx-auto size-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center mb-3">
                        <Smartphone class="size-7 text-emerald-400" />
                    </div>
                    <Card.Title class="text-xl font-bold">Perangkat Ditemukan!</Card.Title>
                    <Card.Description>Verifikasi berhasil — perangkat siap didaftarkan</Card.Description>
                </Card.Header>

                <Card.Content class="space-y-4">
                    <div class="rounded-xl bg-muted/30 p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">Model ID</span>
                            <code class="text-sm font-mono text-primary font-semibold">{deviceInfo.model_id}</code>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">Nama Perangkat</span>
                            <span class="text-sm font-semibold">{deviceInfo.model_name}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">Brand</span>
                            <span class="text-sm font-semibold">{deviceInfo.brand_name ?? '-'}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground">Tipe</span>
                            <span class="text-sm">{deviceInfo.model_type}</span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <Button variant="outline" onclick={cancelRegistration} class="flex-1 gap-2">
                            <X class="size-4" /> Batal
                        </Button>
                        <Button onclick={handleRegister} disabled={isRegistering} class="flex-1 gap-2">
                            {#if isRegistering}
                                <Loader2 class="size-4 animate-spin" /> Mendaftarkan...
                            {:else}
                                <CheckCircle class="size-4" /> Daftarkan
                            {/if}
                        </Button>
                    </div>
                </Card.Content>
            </Card.Root>
        {/if}
    </div>

    <!-- Footer -->
    <div class="relative z-10 pt-4">
        <p class="text-xs text-muted-foreground/60">&copy; 2025 DevControl — Sistem Manajemen Perangkat Lapangan</p>
    </div>
</LayoutBG>

<style>
    @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slide-in-from-top-5 { from { transform: translateY(-5px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes scan { 0% { top: 0%; } 100% { top: 100%; } }
    .animate-in { animation-duration: 0.5s; animation-fill-mode: both; }
    .fade-in { animation-name: fade-in; }
    .slide-in-from-top-5 { animation-name: slide-in-from-top-5; }
    .animate-scan { animation: scan 2s linear infinite; }
    .zoom-in-95 { animation-name: zoom-in-95; }
    @keyframes zoom-in-95 { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>
