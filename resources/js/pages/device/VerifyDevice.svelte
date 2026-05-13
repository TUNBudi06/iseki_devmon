<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import ShieldCheckIcon from '@lucide/svelte/icons/shield-check';
    import ArrowLeftIcon from '@lucide/svelte/icons/arrow-left';
    import { Button } from '$shadcn/components/ui/button/index.js';
    import { FieldGroup, Field, FieldLabel, FieldDescription } from '$shadcn/components/ui/field/index.js';
    import { Input } from '$shadcn/components/ui/input/index.js';
    import {router} from "@inertiajs/svelte";
    import {routeUrl} from "@tunbudi06/inertia-route-helper";

    let deviceId = $state('');
    let isLoading = $state(false);
    let isVerified = $state(false);

    function handleBack() {
        router.visit(routeUrl(deviceNotRegister()));
    }

    function handleVerify(e: SubmitEvent) {
        e.preventDefault();
        isLoading = true;

        // Simulate verification process
        setTimeout(() => {
            isLoading = false;
            if (deviceId.trim()) {
                isVerified = true;
                console.log('Device verified:', deviceId);
                // Simulate success and redirect to dashboard or main app
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            }
        }, 2000);
    }

    function resetVerification() {
        isVerified = false;
        deviceId = '';
    }
</script>

<AppHead title="Verifikasi Device - iseki Devmon" />

<div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-950 dark:to-pink-900 flex min-h-svh flex-col items-center justify-center gap-4 sm:gap-6 p-4 sm:p-6 md:p-10">
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
                    <div class="flex size-10 sm:size-12 items-center justify-center rounded-full bg-pink-500/10 border-2 border-pink-500/20">
                        <ShieldCheckIcon class="size-5 sm:size-6 text-pink-600 dark:text-pink-400" />
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center space-y-2 px-2">
                <h1 class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100">
                    Verifikasi Device
                </h1>
                <FieldDescription class="text-sm sm:text-base text-pink-700 dark:text-pink-300 leading-relaxed">
                    Masukkan Device ID untuk memverifikasi perangkat Anda
                </FieldDescription>
            </div>

            {#if !isVerified}
                <!-- Verification Form -->
                <form onsubmit={handleVerify} class="px-2">
                    <FieldGroup>
                        <Field>
                            <FieldLabel for="deviceId" class="text-sm font-medium">Device ID</FieldLabel>
                            <Input
                                id="deviceId"
                                type="text"
                                placeholder="Masukkan Device ID (contoh: ABC12345)"
                                bind:value={deviceId}
                                required
                                disabled={isLoading}
                                maxlength="20"
                                class="text-base h-11 uppercase"
                            />
                            <FieldDescription class="text-xs">
                                Masukkan Device ID yang sudah terdaftar di sistem
                            </FieldDescription>
                        </Field>

                        <Button
                            type="submit"
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 h-12 sm:h-14 text-base sm:text-lg"
                            disabled={isLoading || !deviceId.trim()}
                        >
                            {isLoading ? 'Memverifikasi...' : 'Verifikasi Device'}
                        </Button>
                    </FieldGroup>
                </form>
            {:else}
                <!-- Success State -->
                <div class="text-center space-y-4 px-2">
                    <div class="flex justify-center">
                        <div class="flex size-16 items-center justify-center rounded-full bg-green-500/10 border-2 border-green-500/20">
                            <ShieldCheckIcon class="size-8 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-lg sm:text-xl font-semibold text-green-900 dark:text-green-100">
                            Device Terverifikasi!
                        </h2>
                        <p class="text-sm sm:text-base text-green-700 dark:text-green-300">
                            Device ID: <strong class="font-mono">{deviceId.toUpperCase()}</strong>
                        </p>
                        <p class="text-xs sm:text-sm text-pink-600 dark:text-pink-400">
                            Mengalihkan ke dashboard...
                        </p>
                    </div>
                </div>
            {/if}

            <!-- Info -->
            <FieldDescription class="text-center text-xs sm:text-sm text-pink-600 dark:text-pink-400 px-2">
                Pastikan Device ID yang dimasukkan sudah terdaftar di sistem iseki Devmon
            </FieldDescription>
        </div>
    </div>
</div>
