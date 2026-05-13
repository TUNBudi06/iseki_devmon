<!-- resources/js/pages/Device/Verify.svelte -->

<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import ShieldCheckIcon from '@lucide/svelte/icons/shield-check';
    import ArrowLeftIcon from '@lucide/svelte/icons/arrow-left';
    import RefreshCwIcon from '@lucide/svelte/icons/refresh-cw';
    import { Button } from '$shadcn/components/ui/button/index.js';
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { onMount } from 'svelte';
    import QRCode from 'qrcode';
    import {deviceVerify, home} from "$routes";
    import {getDeviceAuth} from "$lib/indexedDB.ts";

    // Ambil data dari persisted store / getDeviceAuth()
    let deviceId = $state('');
    let token = $state('');
    let qrDataUrl = $state('');
    let isLoading = $state(true);
    let error = $state('');

    async function generateQr(id: string, tok: string) {
        const payload = JSON.stringify({ deviceId: id, token: tok });
        qrDataUrl = await QRCode.toDataURL(payload, {
            width: 240,
            margin: 2,
            color: {dark: '#9d174d', light: '#fff0f6'},
            errorCorrectionLevel: 'M',
        });
    }

    async function load() {
        isLoading = true;
        error = '';
        const auth = await getDeviceAuth();

        if (!auth || !auth.device_id || !auth.jwt) {
            router.visit(routeUrl(deviceVerify()));
            return;
        }

        deviceId = String(auth.device_id);
        token = auth.jwt;

        await generateQr(deviceId, token);
        isLoading = false;
    }

    async function refresh() {
        await load();
    }

    function handleBack() {
        router.visit(routeUrl(home()));
    }

    onMount(load);
</script>

<AppHead title="QR Device - iseki Devmon" />

<div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-950 dark:to-pink-900
            flex min-h-svh flex-col items-center justify-center gap-4 sm:gap-6 p-4 sm:p-6 md:p-10">
    <div class="w-full max-w-md mx-auto">
        <div class="flex flex-col gap-4 sm:gap-6">

            <!-- Header -->
            <div class="flex items-center gap-3 sm:gap-4 px-2">
                <Button
                    onclick={handleBack}
                    variant="ghost"
                    size="sm"
                    class="text-pink-700 hover:text-pink-900 dark:text-pink-300 dark:hover:text-pink-100 p-2 h-auto"
                >
                    <ArrowLeftIcon class="size-4 sm:size-5" />
                </Button>
                <div class="flex flex-col items-center flex-1">
                    <div class="flex size-10 sm:size-12 items-center justify-center rounded-full
                                bg-pink-500/10 border-2 border-pink-500/20">
                        <ShieldCheckIcon class="size-5 sm:size-6 text-pink-600 dark:text-pink-400" />
                    </div>
                </div>
                <!-- Refresh button kanan -->
                <Button
                    onclick={refresh}
                    variant="ghost"
                    size="sm"
                    class="text-pink-700 hover:text-pink-900 dark:text-pink-300 dark:hover:text-pink-100 p-2 h-auto"
                    disabled={isLoading}
                >
                    <RefreshCwIcon class="size-4 sm:size-5 {isLoading ? 'animate-spin' : ''}" />
                </Button>
            </div>

            <!-- Title -->
            <div class="text-center space-y-1 px-2">
                <h1 class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100">
                    QR Device
                </h1>
                <p class="text-sm text-pink-600 dark:text-pink-400">
                    Tunjukkan QR ini ke scanner presensi
                </p>
            </div>

            <!-- QR Card -->
            <div class="mx-2 rounded-2xl bg-white dark:bg-pink-950/60 border border-pink-200
                        dark:border-pink-800 shadow-sm overflow-hidden">

                {#if isLoading}
                    <!-- Skeleton -->
                    <div class="flex flex-col items-center justify-center gap-4 p-8">
                        <div class="size-[240px] rounded-xl bg-pink-100 dark:bg-pink-900/40 animate-pulse"></div>
                        <div class="h-4 w-32 rounded bg-pink-100 dark:bg-pink-900/40 animate-pulse"></div>
                        <div class="h-3 w-24 rounded bg-pink-100 dark:bg-pink-900/40 animate-pulse"></div>
                    </div>

                {:else if error}
                    <div class="flex flex-col items-center justify-center gap-3 p-8 text-center">
                        <p class="text-sm text-red-500">{error}</p>
                        <Button onclick={refresh} variant="outline" size="sm">Coba Lagi</Button>
                    </div>

                {:else}
                    <!-- QR Image -->
                    <div class="flex justify-center pt-8 pb-4 px-8">
                        <div class="rounded-xl overflow-hidden ring-4 ring-pink-100 dark:ring-pink-900">
                            <img
                                src={qrDataUrl}
                                alt="QR Code Device {deviceId}"
                                width="240"
                                height="240"
                                class="block"
                            />
                        </div>
                    </div>

                    <!-- Device Info -->
                    <div class="px-6 pb-6 pt-2 text-center space-y-1">
                        <p class="text-xs font-mono text-pink-500 dark:text-pink-400 uppercase tracking-widest">
                            ID: {deviceId}
                        </p>
                    </div>
                {/if}
            </div>

            <!-- Footer note -->
            <p class="text-center text-xs text-pink-500 dark:text-pink-500 px-4">
                QR ini berisi identitas device. Jangan bagikan ke pihak yang tidak berwenang.
            </p>

        </div>
    </div>
</div>
