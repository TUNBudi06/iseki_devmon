<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import LockIcon from '@lucide/svelte/icons/lock';
    import ShieldOffIcon from '@lucide/svelte/icons/shield-off';
    import {
        FieldGroup,
        Field,
        FieldLabel,
        FieldDescription,
    } from '$shadcn/components/ui/field/index.js';
    import { Input } from '$shadcn/components/ui/input/index.js';
    import { Button } from '$shadcn/components/ui/button/index.js';
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { deviceVerify } from '$routes';
    import { login as adminLogin } from '$/routes/admin';
    import LayoutTop from '$/components/LayoutTop.svelte';

    let { deviceId, deviceName, status } = $props();

    let nik = $state('');
    let password = $state('');

    // Device harus terdaftar DAN approved
    const isApproved = $derived(!!deviceName && !!status);

    function handleNikInput(e: Event) {
        const input = e.target as HTMLInputElement;
        input.value = input.value.replace(/\D/g, '').slice(0, 6);
        nik = input.value;
    }

    function handleLogin(e: SubmitEvent) {
        e.preventDefault();
        if (!isApproved) return; // guard — tidak bisa login jika belum approved
        console.log('Login attempt:', { nik, password });
    }

    function handleVerifyDevice() {
        router.get(routeUrl(deviceVerify()));
    }

    function handleAdminLogin() {
        router.get(routeUrl(adminLogin()));
    }
</script>

<LayoutTop>
    <AppHead title="Login Page" />
    <div
        class="bg-linear-to-br from-pink-50 to-pink-100 dark:from-pink-950 dark:to-pink-900
            flex min-h-svh flex-col items-center justify-center gap-4 sm:gap-6 p-4 sm:p-6 md:p-10"
    >
        <div class="w-full max-w-sm mx-auto">
            <div class="flex flex-col gap-4 sm:gap-6">
                <!-- Header -->
                <div class="flex flex-col items-center gap-2 text-center px-2">
                    <div
                        class="flex size-10 sm:size-12 items-center justify-center rounded-lg
                            bg-pink-500/10 border border-pink-500/20"
                    >
                        <LockIcon
                            class="size-5 sm:size-6 text-pink-600 dark:text-pink-400"
                        />
                    </div>
                    <h1
                        class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100"
                    >
                        iseki Devmon
                    </h1>
                    <FieldDescription
                        class="text-sm text-pink-700 dark:text-pink-300"
                    >
                        iseki untuk memantau perangkat dan lainnya
                    </FieldDescription>

                    <!-- Device status badge -->
                    {#if deviceName}
                        <div
                            class="mt-2 w-full rounded-lg border px-3 py-2
                                {isApproved
                                ? 'bg-pink-100 dark:bg-pink-900/30 border-pink-300 dark:border-pink-700'
                                : 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-800'}"
                        >
                            <div
                                class="flex items-center justify-between gap-2"
                            >
                                <div class="text-left">
                                    <p
                                        class="text-xs font-semibold text-pink-900 dark:text-pink-100"
                                    >
                                        {deviceName}
                                        <span class="font-light text-pink-500"
                                            >({deviceId})</span
                                        >
                                    </p>
                                    <p
                                        class="text-xs mt-0.5
                                          {isApproved
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400'}"
                                    >
                                        {isApproved
                                            ? '✓ Approved'
                                            : '✗ Belum disetujui admin'}
                                    </p>
                                </div>
                                {#if !isApproved}
                                    <ShieldOffIcon
                                        class="size-5 shrink-0 text-red-400"
                                    />
                                {/if}
                            </div>
                        </div>
                    {/if}
                </div>

                <!-- Not approved warning -->
                {#if deviceName && !isApproved}
                    <div
                        class="mx-2 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200
                            dark:border-red-800 px-4 py-3 text-center"
                    >
                        <p
                            class="text-sm font-medium text-red-700 dark:text-red-400"
                        >
                            Device belum disetujui
                        </p>
                        <p class="mt-1 text-xs text-red-500 dark:text-red-500">
                            Hubungi admin untuk mengaktifkan device ini sebelum
                            dapat login
                        </p>
                    </div>
                {/if}

                <!-- Login Form -->
                <form onsubmit={handleLogin} class="px-2">
                    <FieldGroup>
                        <Field>
                            <FieldLabel for="nik" class="text-sm font-medium"
                                >NIK (6 Digit)</FieldLabel
                            >
                            <Input
                                id="nik"
                                type="text"
                                placeholder="000000"
                                maxlength="6"
                                inputmode="numeric"
                                oninput={handleNikInput}
                                disabled={!isApproved}
                                required
                                class="text-base"
                            />
                            <FieldDescription class="text-xs">
                                Masukkan 6 digit NIK Anda
                            </FieldDescription>
                        </Field>

                        <Field>
                            <FieldLabel
                                for="password"
                                class="text-sm font-medium">Password</FieldLabel
                            >
                            <Input
                                id="password"
                                type="password"
                                placeholder="••••••••"
                                bind:value={password}
                                disabled={!isApproved}
                                required
                                class="text-base"
                            />
                        </Field>

                        <Button
                            type="submit"
                            class="w-full h-11 text-base font-medium"
                            disabled={!isApproved || nik.length !== 6}
                        >
                            Masuk
                        </Button>
                    </FieldGroup>
                </form>

                <!-- Action buttons -->
                <div class="px-2 flex w-full justify-between gap-2">
                    <Button
                        onclick={handleVerifyDevice}
                        variant="outline"
                        class="border-pink-600 w-40 text-pink-600 hover:bg-pink-600 hover:text-white
                           font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg
                           transition-all duration-200 h-10 text-sm"
                    >
                        Verifikasi Device
                    </Button>

                    <Button
                        onclick={handleAdminLogin}
                        variant="outline"
                        class="border-pink-600 w-40 text-pink-600 hover:bg-pink-600 hover:text-white
                           font-semibold py-2 px-4 rounded-lg shadow-lg hover:shadow-xl
                           transition-all duration-200 h-10 text-sm"
                    >
                        Login Admin
                    </Button>
                </div>

                <!-- Footer -->
                <FieldDescription
                    class="text-center text-xs text-pink-600 dark:text-pink-400 px-2"
                >
                    Aplikasi monitoring perangkat milik <span
                        class="font-semibold">iseki</span
                    >
                </FieldDescription>
            </div>
        </div>
    </div>
</LayoutTop>
