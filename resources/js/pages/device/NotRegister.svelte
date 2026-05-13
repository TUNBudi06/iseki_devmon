<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import SmartphoneIcon from '@lucide/svelte/icons/smartphone';
    import { Button } from '$shadcn/components/ui/button/index.js';
    import { FieldDescription } from '$shadcn/components/ui/field/index.js';
    import {router} from "@inertiajs/svelte";
    import {routeUrl} from "@tunbudi06/inertia-route-helper";
    import {deviceLogin, deviceRegister} from "$routes";
    import {onMount} from "svelte";
    import {getDeviceAuth} from "$lib/indexedDB.ts";

    onMount(async () => {
        // Cek apakah perangkat sudah terdaftar
        // Jika sudah, redirect ke halaman utama atau dashboard
        // Contoh: router.visit(routeUrl(dashboard()));

        const auth = await getDeviceAuth()
        // console.log('Device auth found:', auth);
        if (auth) {
            router.get(routeUrl(deviceLogin({deviceId:auth.device_id})))
        }
    });

    function handleRegister() {
        router.get(routeUrl(deviceRegister()))
    }
</script>

<AppHead title="Device Belum Terdaftar - iseki Devmon" />

<div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-950 dark:to-pink-900 flex min-h-svh flex-col items-center justify-center gap-6 sm:gap-8 p-4 sm:p-6 md:p-10">
    <div class="w-full max-w-md mx-auto">
        <div class="flex flex-col items-center gap-4 sm:gap-6 text-center px-2">
            <!-- Icon -->
            <div class="flex size-14 sm:size-16 items-center justify-center rounded-full bg-pink-500/10 border-2 border-pink-500/20">
                <SmartphoneIcon class="size-7 sm:size-8 text-pink-600 dark:text-pink-400" />
            </div>

            <!-- Title -->
            <div class="space-y-2">
                <h1 class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100">
                    Device Belum Terdaftar
                </h1>
                <FieldDescription class="text-sm sm:text-base text-pink-700 dark:text-pink-300 leading-relaxed">
                    Perangkat ini belum terdaftar dalam sistem iseki Devmon.
                    Silakan daftarkan perangkat Anda terlebih dahulu untuk dapat menggunakan aplikasi.
                </FieldDescription>
            </div>

            <!-- Register Button -->
            <div class="w-full pt-2 sm:pt-4">
                <Button
                    onclick={handleRegister}
                    class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 h-12 sm:h-14 text-base sm:text-lg"
                    size="lg"
                >
                    Daftarkan Device
                </Button>
            </div>

            <!-- Register Button -->
            <div class="w-full pt-2 sm:pt-4">
                <Button
                    onclick={handleRegister}
                    class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 h-12 sm:h-14 text-base sm:text-lg"
                    size="lg"
                >
                    Admin Login
                </Button>
            </div>

            <!-- Additional Info -->
            <div class="mt-2 sm:mt-4">
                <FieldDescription class="text-xs sm:text-sm text-pink-600 dark:text-pink-400">
                    Proses pendaftaran hanya perlu dilakukan sekali per perangkat
                </FieldDescription>
            </div>
        </div>
    </div>
</div>
