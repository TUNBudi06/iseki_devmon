<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import SmartphoneIcon from '@lucide/svelte/icons/smartphone';
    import ArrowLeftIcon from '@lucide/svelte/icons/arrow-left';
    import ShieldCheckIcon from '@lucide/svelte/icons/shield-check';
    import { Button } from '$shadcn/components/ui/button/index.js';
    import {
        FieldGroup,
        Field,
        FieldLabel,
        FieldDescription,
        FieldError,
    } from '$shadcn/components/ui/field/index.js';
    import { Input } from '$shadcn/components/ui/input/index.js';
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { home, deviceRegisterAdd } from '$routes';
    import { useHttp } from '@inertiajs/svelte';
    import {
        saveDeviceAuth,
        getDeviceAuth,
        clearDeviceAuth,
    } from '$lib/indexedDB.ts';
    import { onMount } from 'svelte';
    import { toast } from 'svelte-sonner';
    import LayoutTop from '$/components/LayoutTop.svelte';

    const form = useHttp({
        deviceName: null,
        deviceId: null,
    });

    let isLoading = $state(false);

    // Data device yang sudah terdaftar (jika ada)
    let registeredDevice = $state<{ device_id: string; jwt: string } | null>(
        null,
    );

    onMount(async () => {
        const auth = await getDeviceAuth();
        if (auth) {
            registeredDevice = auth;
            console.log('Device already registered:', auth);
        } else {
            // Hanya auto-generate jika belum terdaftar
            generateDeviceId();
        }
    });

    function handleBack() {
        router.visit(routeUrl(home()));
    }

    async function handleRegister(e: SubmitEvent) {
        e.preventDefault();
        isLoading = true;
        await form.post(routeUrl(deviceRegisterAdd()), {
            onFinish: () => {
                isLoading = false;
            },
            onError: (errors) => {
                console.error('Registration error:', errors);
                toast.error(
                    'Gagal mendaftarkan device. Pastikan informasi sudah benar dan coba lagi.',
                );
                isLoading = false;
            },
            onSuccess: async (params) => {
                await saveDeviceAuth(form.deviceId, params.jwt);
                registeredDevice = await getDeviceAuth();
                toast.success('Device berhasil didaftarkan!');
                router.visit(routeUrl(home()));
            },
        });
    }

    async function handleUnregister() {
        if (!confirm('Hapus registrasi device ini?')) return;
        await clearDeviceAuth();
        toast.success(
            'Registrasi device dihapus. Anda dapat mendaftarkan ulang jika ingin menggunakan perangkat ini.',
        );
        registeredDevice = null;
        generateDeviceId();
    }

    function generateDeviceId() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        form.deviceId = Array.from(
            { length: 8 },
            () => chars[Math.floor(Math.random() * chars.length)],
        ).join('');
    }
</script>

<LayoutTop>
    <AppHead title="Daftarkan Device - iseki Devmon" />
    <div
        class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-950 dark:to-pink-900
            flex min-h-svh flex-col items-center justify-center gap-4 sm:gap-6 p-4 sm:p-6 md:p-10"
    >
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
                        <div
                            class="flex size-10 sm:size-12 items-center justify-center rounded-full
                                bg-pink-500/10 border-2 border-pink-500/20"
                        >
                            {#if registeredDevice}
                                <ShieldCheckIcon
                                    class="size-5 sm:size-6 text-green-600 dark:text-green-400"
                                />
                            {:else}
                                <SmartphoneIcon
                                    class="size-5 sm:size-6 text-pink-600 dark:text-pink-400"
                                />
                            {/if}
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div class="text-center space-y-2 px-2">
                    <h1
                        class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100"
                    >
                        {registeredDevice
                            ? 'Device Terdaftar'
                            : 'Daftarkan Device'}
                    </h1>
                    <FieldDescription
                        class="text-sm sm:text-base text-pink-700 dark:text-pink-300 leading-relaxed"
                    >
                        {#if registeredDevice}
                            Perangkat ini sudah terdaftar di sistem iseki Devmon
                        {:else}
                            Masukkan informasi perangkat untuk mendaftarkannya
                            ke sistem iseki Devmon
                        {/if}
                    </FieldDescription>
                </div>

                {#if registeredDevice}
                    <!-- Already Registered State -->
                    <div
                        class="mx-2 rounded-2xl bg-white dark:bg-pink-950/60 border border-green-200
                            dark:border-green-800 shadow-sm overflow-hidden"
                    >
                        <div class="p-5 flex flex-col gap-4">
                            <!-- Status badge -->
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                         bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400
                                         text-xs font-medium"
                                >
                                    <span
                                        class="size-1.5 rounded-full bg-green-500 animate-pulse"
                                    ></span>
                                    Terdaftar
                                </span>
                            </div>

                            <!-- Device ID -->
                            <div class="space-y-1">
                                <p
                                    class="text-xs text-pink-500 dark:text-pink-400 uppercase tracking-widest font-medium"
                                >
                                    Device ID
                                </p>
                                <p
                                    class="font-mono text-base font-semibold text-pink-900 dark:text-pink-100 tracking-wider"
                                >
                                    {registeredDevice.device_id}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="px-2 flex flex-col gap-2">
                        <Button
                            onclick={() => router.visit(routeUrl(home()))}
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold
                               h-12 sm:h-14 text-base sm:text-lg"
                        >
                            Lanjut ke Aplikasi
                        </Button>
                        <Button
                            onclick={handleUnregister}
                            variant="ghost"
                            class="w-full text-pink-400 hover:text-red-600 dark:hover:text-red-400
                               text-sm h-9"
                        >
                            Hapus Registrasi Device Ini
                        </Button>
                    </div>
                {:else}
                    <!-- Registration Form -->
                    <form onsubmit={handleRegister} class="px-2">
                        <FieldGroup>
                            <Field>
                                <FieldLabel
                                    for="deviceName"
                                    class="text-sm font-medium"
                                    >Nama Device</FieldLabel
                                >
                                <Input
                                    id="deviceName"
                                    type="text"
                                    placeholder="Contoh: Smartphone Samsung A54"
                                    bind:value={form.deviceName}
                                    required
                                    disabled={isLoading}
                                    class="text-base h-11"
                                />
                                <FieldDescription class="text-xs">
                                    Masukkan nama yang mudah diingat untuk
                                    perangkat ini
                                </FieldDescription>
                            </Field>

                            <Field>
                                <FieldLabel
                                    for="deviceId"
                                    class="text-sm font-medium"
                                    >Device ID</FieldLabel
                                >
                                <div class="flex gap-2">
                                    <Input
                                        id="deviceId"
                                        type="text"
                                        placeholder="ABCDEFGH"
                                        bind:value={form.deviceId}
                                        disabled={isLoading}
                                        oninput={() => {
                                            form.deviceId = form.deviceId
                                                .toUpperCase()
                                                .replace(/\s/g, '');
                                        }}
                                        class="flex-1 text-base h-11"
                                        maxlength="20"
                                    />
                                    <Button
                                        type="button"
                                        onclick={generateDeviceId}
                                        variant="outline"
                                        disabled={isLoading}
                                        class="px-3 h-11 shrink-0"
                                        title="Generate new ID"
                                    >
                                        <span class="text-lg">🔄</span>
                                    </Button>
                                </div>
                                <FieldDescription class="text-xs">
                                    ID unik perangkat (otomatis dihasilkan atau
                                    input manual)
                                </FieldDescription>
                                <FieldError class="text-xs text-red-600 mt-1"
                                    >{form.errors.deviceId}</FieldError
                                >
                            </Field>

                            <Button
                                type="submit"
                                class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold
                                   py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all
                                   duration-200 h-12 sm:h-14 text-base sm:text-lg"
                                disabled={isLoading || !form.deviceName?.trim()}
                            >
                                {isLoading
                                    ? 'Mendaftarkan...'
                                    : 'Daftarkan Device'}
                            </Button>
                        </FieldGroup>
                    </form>
                {/if}

                <FieldDescription
                    class="text-center text-xs sm:text-sm text-pink-600 dark:text-pink-400 px-2"
                >
                    {#if registeredDevice}
                        Device ini aktif dan siap digunakan untuk presensi
                    {:else}
                        Pastikan informasi device sudah benar sebelum
                        mendaftarkan
                    {/if}
                </FieldDescription>
            </div>
        </div>
    </div>
</LayoutTop>
