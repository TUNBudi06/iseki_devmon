<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import LockIcon from '@lucide/svelte/icons/lock';
    import { FieldGroup, Field, FieldLabel, FieldDescription } from '$shadcn/components/ui/field/index.js';
    import { Input } from '$shadcn/components/ui/input/index.js';
    import { Button } from '$shadcn/components/ui/button/index.js';
    import {router} from "@inertiajs/svelte";
    import {routeUrl} from "@tunbudi06/inertia-route-helper";
    import {deviceVerify} from "$routes";
    let {deviceId,deviceName} = $props();

    let nik = $state('');
    let password = $state('');

    function handleNikInput(e: Event) {
        const input = e.target as HTMLInputElement;
        // Hanya izinkan maksimal 6 digit
        input.value = input.value.replace(/\D/g, '').slice(0, 6);
        nik = input.value;
    }

    function handleLogin(e: SubmitEvent) {
        e.preventDefault();
        console.log('Login attempt:', { nik, password });
        // Frontend only - bisa tambahkan validasi atau logic sesuai kebutuhan
    }

    function handleVerifyDevice() {
        router.get(routeUrl(deviceVerify()));
    }
</script>

<AppHead title="Login Page" />

<div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-950 dark:to-pink-900 flex min-h-svh flex-col items-center justify-center gap-4 sm:gap-6 p-4 sm:p-6 md:p-10">
    <div class="w-full max-w-sm mx-auto">
        <div class="flex flex-col gap-4 sm:gap-6">
             <!-- Header -->
             <div class="flex flex-col items-center gap-2 text-center px-2">
                 <div class="flex size-10 sm:size-12 items-center justify-center rounded-lg bg-pink-500/10 border border-pink-500/20">
                     <LockIcon class="size-5 sm:size-6 text-pink-600 dark:text-pink-400" />
                 </div>
                 <h1 class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100">iseki Devmon</h1>
                 <FieldDescription class="text-sm text-pink-700 dark:text-pink-300">
                     iseki untuk memantau perangkat dan lainnya
                 </FieldDescription>
                 {#if deviceName}
                     <div class="mt-2 p-2 sm:p-3 bg-pink-100 dark:bg-pink-900/30 rounded-lg border border-pink-300 dark:border-pink-700 w-full">
                         <FieldDescription class="text-xs sm:text-sm font-semibold text-pink-900 dark:text-pink-100">
                             Device: <span class="font-bold">{deviceName}</span> <span class="font-light">({deviceId})</span>
                         </FieldDescription>
                     </div>
                 {/if}
             </div>

            <!-- Login Form -->
            <form onsubmit={handleLogin} class="px-2">
                <FieldGroup>
                    <Field>
                        <FieldLabel for="nik" class="text-sm font-medium">NIK (6 Digit)</FieldLabel>
                        <Input
                            id="nik"
                            type="text"
                            placeholder="000000"
                            maxlength="6"
                            inputmode="numeric"
                            oninput={handleNikInput}
                            required
                            class="text-base"
                        />
                        <FieldDescription class="text-xs">
                            Masukkan 6 digit NIK Anda
                        </FieldDescription>
                    </Field>

                    <Field>
                        <FieldLabel for="password" class="text-sm font-medium">Password</FieldLabel>
                        <Input
                            id="password"
                            type="password"
                            placeholder="••••••••"
                            bind:value={password}
                            required
                            class="text-base"
                        />
                    </Field>

                    <Button type="submit" class="w-full h-11 text-base font-medium" disabled={nik.length !== 6}>
                        Masuk
                    </Button>
                </FieldGroup>
            </form>

            <!-- Verify Device Button -->
            <div class="px-2">
                <div class="flex w-full justify-between gap-2">

                    <!-- Verify Device -->
                    <Button
                        onclick={handleVerifyDevice}
                        variant="outline"
                        class="border-pink-600 w-40 text-pink-600 hover:bg-pink-600 hover:text-white font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 h-10 text-sm"
                    >
                        Verifikasi Device
                    </Button>

                    <!-- Login Admin -->
                    <Button
                        class="border-pink-600 w-40 text-pink-600 hover:bg-pink-600 hover:text-white font-semibold py-2 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 h-10 text-sm"
                        variant="outline"
                    >
                        Login Admin
                    </Button>

                </div>
            </div>

            <!-- Footer -->
            <FieldDescription class="text-center text-xs text-pink-600 dark:text-pink-400 px-2">
                Aplikasi monitoring perangkat milik <span class="font-semibold">iseki</span>
            </FieldDescription>
        </div>
    </div>
</div>
