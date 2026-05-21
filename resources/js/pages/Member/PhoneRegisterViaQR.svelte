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
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import LayoutBG from '$/components/LayoutBG.svelte';
    import { Button } from '$shadcn/components/ui/button';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import * as Card from '$shadcn/components/ui/card';
    import * as Alert from '$shadcn/components/ui/alert';
    import QrScanner from 'qr-scanner';
    import { home } from '$routes';
    import { deviceNotRegister } from '$routes/user';

    let videoElement: HTMLVideoElement | null = null;
    let qrScanner: QrScanner | null = null;
    let scanning = $state(false);
    let manualCode = $state('');
    let isLoading = $state(false);
    let error = $state('');
    let success = $state(false);
    let scannedData = $state('');
    let hasFlash = $state(false);
    let isFlashOn = $state(false);

    let deviceInfo = $state<null | {
        id: string;
        name: string;
        type: string;
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
                    // QR Code berhasil discan
                    if (result?.data && !isLoading && !success) {
                        processQRCode(result.data);
                    }
                },
                {
                    onDecodeError: (err) => {
                        console.log('Decode error:', err);
                        // Tidak menampilkan error ke user karena akan terus muncul
                    },
                    preferredCamera: 'environment', // Kamera belakang
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                    maxScansPerSecond: 10,
                    returnDetailedScanResult: true,
                },
            );

            await qrScanner.start();

            // Cek ketersediaan flash
            hasFlash = await qrScanner.hasFlash();
        } catch (err) {
            console.error('Camera error:', err);
            error =
                'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan dan gunakan HTTPS.';
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
                const currentIndex = cameras.findIndex(
                    (cam) => cam.id === currentCamera,
                );
                const nextCamera = cameras[(currentIndex + 1) % cameras.length];
                await qrScanner.setCamera(nextCamera.id);
            } catch (err) {
                console.error('Switch camera error:', err);
            }
        }
    }

    async function processQRCode(code: string) {
        await stopScan();
        isLoading = true;
        error = '';

        try {
            // Simulasi API call untuk verifikasi QR code
            await new Promise((resolve) => setTimeout(resolve, 1500));

            if (code && code.length > 0) {
                deviceInfo = {
                    id: code,
                    name: 'Device from QR',
                    type: 'Smartphone',
                };
                success = true;
                scannedData = code;
            } else {
                error = 'QR Code tidak valid atau perangkat tidak ditemukan';
                success = false;
            }
        } catch (err) {
            error = 'Terjadi kesalahan, silakan coba lagi';
        } finally {
            isLoading = false;
        }
    }

    async function handleManualSubmit() {
        if (!manualCode) {
            error = 'Harap masukkan kode QR';
            return;
        }
        await stopScan();
        await processQRCode(manualCode);
    }

    function handleRegister() {
        router.visit(routeUrl('register-device', { deviceId: deviceInfo?.id }));
    }

    function resetScan() {
        scanning = false;
        manualCode = '';
        deviceInfo = null;
        success = false;
        error = '';
        scannedData = '';
        isFlashOn = false;
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
            onclick={() => router.visit(routeUrl(deviceNotRegister()))}
            class="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors group"
        >
            <ArrowLeft
                class="size-4 group-hover:-translate-x-1 transition-transform"
            />
            Kembali ke Beranda
        </button>
    </div>

    <!-- Hero section -->
    <div
        class="text-center relative z-10 space-y-4 md:space-y-6 max-w-4xl mx-auto"
    >
        <div
            class="inline-flex items-center gap-2 bg-primary/10 backdrop-blur-sm border border-primary/20 text-primary text-xs font-medium px-4 py-1.5 rounded-full animate-in fade-in slide-in-from-top-5 duration-500"
        >
            <QrCode class="size-3" />
            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
            Pendaftaran Cepat
        </div>

        <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold tracking-tight">
            <span class="text-foreground">Scan QR Code</span>
        </h1>

        <p class="text-sm md:text-base text-muted-foreground max-w-2xl mx-auto">
            Scan QR code pada perangkat atau masukkan kode manual untuk memulai
            pendaftaran
        </p>
    </div>

    <!-- Main Content -->
    <div class="w-full max-w-2xl relative z-10 px-4">
        {#if !success}
            <Card.Root class="border-border/60 bg-card/80 backdrop-blur-sm">
                <Card.Header>
                    <Card.Title class="text-xl font-bold text-center">
                        {scanning
                            ? 'Arahkan Kamera ke QR Code'
                            : 'Pilih Metode'}
                    </Card.Title>
                    <Card.Description class="text-center">
                        {scanning
                            ? 'Tempatkan QR code di dalam area scanner'
                            : 'Scan menggunakan kamera atau masukkan kode manual'}
                    </Card.Description>
                </Card.Header>

                <Card.Content class="space-y-6">
                    {#if error}
                        <Alert.Root variant="destructive">
                            <AlertCircle class="size-4" />
                            <Alert.Title>Error</Alert.Title>
                            <Alert.Description>{error}</Alert.Description>
                        </Alert.Root>
                    {/if}

                    {#if scanning}
                        <!-- QR Scanner -->
                        <div
                            class="relative aspect-square w-full max-w-sm mx-auto rounded-xl overflow-hidden border-2 border-primary/30 bg-black"
                        >
                            <video
                                bind:this={videoElement}
                                class="w-full h-full object-cover"
                            ></video>

                            <!-- Scanner overlay line -->
                            <div class="absolute inset-0 pointer-events-none">
                                <div
                                    class="absolute inset-0 border-2 border-primary/50 rounded-xl"
                                ></div>
                                <div
                                    class="absolute top-1/2 left-0 right-0 h-0.5 bg-primary animate-scan"
                                ></div>
                                <div
                                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 border-2 border-primary rounded-xl"
                                ></div>
                            </div>
                        </div>

                        <!-- Controls -->
                        <div class="flex justify-center gap-3">
                            {#if hasFlash}
                                <Button
                                    onclick={toggleFlash}
                                    variant="outline"
                                    class="gap-2"
                                >
                                    <Zap
                                        class="size-4 {isFlashOn
                                            ? 'text-yellow-500 fill-yellow-500'
                                            : ''}"
                                    />
                                    {isFlashOn ? 'Flash On' : 'Flash Off'}
                                </Button>
                            {/if}

                            <Button
                                onclick={switchCamera}
                                variant="outline"
                                class="gap-2"
                            >
                                <FlipHorizontal class="size-4" />
                                Ganti Kamera
                            </Button>

                            <Button
                                onclick={stopScan}
                                variant="outline"
                                class="gap-2"
                            >
                                <X class="size-4" />
                                Batal
                            </Button>
                        </div>

                        <div
                            class="text-center text-xs text-muted-foreground space-y-1"
                        >
                            <p>
                                Pastikan QR code terlihat jelas dan dalam
                                pencahayaan yang cukup
                            </p>
                        </div>
                    {:else}
                        <!-- Method selection -->
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                onclick={startScan}
                                class="group flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-border hover:border-primary/50 bg-card/50 hover:bg-primary/5 transition-all duration-300"
                            >
                                <div
                                    class="size-12 rounded-full bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform"
                                >
                                    <Camera class="size-6 text-primary" />
                                </div>
                                <div>
                                    <div class="font-semibold text-sm">
                                        Scan Kamera
                                    </div>
                                    <p
                                        class="text-xs text-muted-foreground mt-1"
                                    >
                                        Gunakan kamera perangkat
                                    </p>
                                </div>
                            </button>

                            <button
                                onclick={() =>
                                    document
                                        .getElementById('manual-input')
                                        ?.scrollIntoView({
                                            behavior: 'smooth',
                                        })}
                                class="group flex flex-col items-center gap-3 p-6 rounded-xl border-2 border-border hover:border-primary/50 bg-card/50 hover:bg-primary/5 transition-all duration-300"
                            >
                                <div
                                    class="size-12 rounded-full bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform"
                                >
                                    <Smartphone class="size-6 text-primary" />
                                </div>
                                <div>
                                    <div class="font-semibold text-sm">
                                        Input Manual
                                    </div>
                                    <p
                                        class="text-xs text-muted-foreground mt-1"
                                    >
                                        Masukkan kode QR manual
                                    </p>
                                </div>
                            </button>
                        </div>

                        <!-- Manual input -->
                        <div
                            id="manual-input"
                            class="space-y-4 pt-4 border-t border-border"
                        >
                            <div class="space-y-2">
                                <Label
                                    for="qr-code"
                                    class="text-sm font-medium"
                                >
                                    Kode QR / Device ID
                                </Label>
                                <div class="relative">
                                    <QrCode
                                        class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                                    />
                                    <Input
                                        id="qr-code"
                                        type="text"
                                        bind:value={manualCode}
                                        placeholder="Contoh: DEV-2024-001"
                                        class="pl-10 bg-background/50 border-border/60 focus:border-primary"
                                    />
                                </div>
                            </div>
                            <Button
                                onclick={handleManualSubmit}
                                disabled={isLoading || !manualCode}
                                class="w-full gap-2 bg-gradient-to-r from-primary to-primary/80 hover:from-primary/90 hover:to-primary transition-all duration-300"
                            >
                                {#if isLoading}
                                    <Loader2 class="size-4 animate-spin" />
                                    Memverifikasi...
                                {:else}
                                    Verifikasi Kode
                                    <CheckCircle class="size-4" />
                                {/if}
                            </Button>
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>
        {:else}
            <!-- Success state -->
            <Card.Root
                class="border-border/60 bg-card/80 backdrop-blur-sm animate-in fade-in slide-in-from-bottom-5 duration-500"
            >
                <Card.Header class="text-center">
                    <div
                        class="mx-auto size-16 rounded-full bg-emerald-500/10 flex items-center justify-center mb-4"
                    >
                        <CheckCircle class="size-8 text-emerald-500" />
                    </div>
                    <Card.Title class="text-xl font-bold">
                        Perangkat Ditemukan!
                    </Card.Title>
                    <Card.Description>
                        Verifikasi berhasil, silakan lanjutkan pendaftaran
                    </Card.Description>
                </Card.Header>

                <Card.Content class="space-y-4">
                    <div class="rounded-xl bg-muted/30 p-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground"
                                >Device ID</span
                            >
                            <code class="text-sm font-mono text-primary"
                                >{deviceInfo?.id}</code
                            >
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground"
                                >Nama Perangkat</span
                            >
                            <span class="text-sm font-medium"
                                >{deviceInfo?.name}</span
                            >
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground"
                                >Tipe</span
                            >
                            <span class="text-sm">{deviceInfo?.type}</span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <Button
                            onclick={resetScan}
                            variant="outline"
                            class="flex-1 gap-2"
                        >
                            <X class="size-4" />
                            Scan Ulang
                        </Button>
                        <Button
                            onclick={handleRegister}
                            class="flex-1 gap-2 bg-gradient-to-r from-primary to-primary/80 hover:from-primary/90 hover:to-primary"
                        >
                            Lanjutkan
                            <ArrowLeft class="size-4 rotate-180" />
                        </Button>
                    </div>
                </Card.Content>
            </Card.Root>
        {/if}
    </div>

    <!-- Footer -->
    <div class="relative z-10 pt-4">
        <p class="text-xs text-muted-foreground/60">
            &copy; 2025 DevControl — Sistem Manajemen Perangkat Lapangan
        </p>
    </div>
</LayoutBG>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slide-in-from-top-5 {
        from {
            transform: translateY(-5px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slide-in-from-bottom-5 {
        from {
            transform: translateY(5px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes scan {
        0% {
            top: 0%;
        }
        100% {
            top: 100%;
        }
    }

    .animate-in {
        animation-duration: 0.5s;
        animation-fill-mode: both;
    }

    .fade-in {
        animation-name: fade-in;
    }

    .slide-in-from-top-5 {
        animation-name: slide-in-from-top-5;
    }

    .slide-in-from-bottom-5 {
        animation-name: slide-in-from-bottom-5;
    }

    .animate-scan {
        animation: scan 2s linear infinite;
    }
</style>
