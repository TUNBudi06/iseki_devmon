<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import SmartphoneIcon from '@lucide/svelte/icons/smartphone';
    import ArrowLeftIcon from '@lucide/svelte/icons/arrow-left';
    import { Button } from '$shadcn/components/ui/button/index.js';
    import { FieldGroup, Field, FieldLabel, FieldDescription } from '$shadcn/components/ui/field/index.js';
    import { Input } from '$shadcn/components/ui/input/index.js';
    import {router} from "@inertiajs/svelte";
    import {routeUrl} from "@tunbudi06/inertia-route-helper";
    import {deviceNotRegister, deviceRegisterAdd} from "$routes";
    import {useForm} from "@inertiajs/svelte";

    const form = useForm({
        deviceName: null,
        deviceId: null,
    })
    let isLoading = $state(false);

    function handleBack() {
        router.visit(routeUrl(deviceNotRegister()));
    }

    function handleRegister(e: SubmitEvent) {
        e.preventDefault();
        isLoading = true;
        console.log('Device registration:', { deviceName: form.deviceName, deviceId: form.deviceId });
        form.post(routeUrl(deviceRegisterAdd()), {
            onFinish: () => {
                isLoading = false;
            },
            onSuccess: (params) => {
                console.log(params);
            }
        });
    }

    function generateDeviceId() {
        // Generate random 8-character device ID
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 8; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        form.deviceId = result;
    }

    // Auto-generate device ID on mount
    $effect(() => {
        if (!form.deviceId) {
            generateDeviceId();
        }
    });
</script>

<AppHead title="Daftarkan Device - iseki Devmon" />

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
                        <SmartphoneIcon class="size-5 sm:size-6 text-pink-600 dark:text-pink-400" />
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center space-y-2 px-2">
                <h1 class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100">
                    Daftarkan Device
                </h1>
                <FieldDescription class="text-sm sm:text-base text-pink-700 dark:text-pink-300 leading-relaxed">
                    Masukkan informasi perangkat untuk mendaftarkannya ke sistem iseki Devmon
                </FieldDescription>
            </div>

            <!-- Registration Form -->
            <form onsubmit={handleRegister} class="px-2">
                <FieldGroup>
                    <Field>
                        <FieldLabel for="deviceName" class="text-sm font-medium">Nama Device</FieldLabel>
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
                            Masukkan nama yang mudah diingat untuk perangkat ini
                        </FieldDescription>
                    </Field>

                    <Field>
                        <FieldLabel for="deviceId" class="text-sm font-medium">Device ID</FieldLabel>
                        <div class="flex gap-2">
                            <Input
                                id="deviceId"
                                type="text"
                                placeholder="ABCDEFGH"
                                bind:value={form.deviceId}
                                disabled={isLoading}
                                oninput={()=>{
                                    // always uppercase dan hapus spasi
                                    form.deviceId = form.deviceId.toUpperCase().replace(/\s/g, '');
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
                            ID unik perangkat (otomatis dihasilkan atau input manual)
                        </FieldDescription>
                    </Field>

                    <Button
                        type="submit"
                        class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 h-12 sm:h-14 text-base sm:text-lg"
                        disabled={isLoading || !form.deviceName?.trim()}
                    >
                        {isLoading ? 'Mendaftarkan...' : 'Daftarkan Device'}
                    </Button>
                </FieldGroup>
            </form>

            <!-- Info -->
            <FieldDescription class="text-center text-xs sm:text-sm text-pink-600 dark:text-pink-400 px-2">
                Pastikan informasi device sudah benar sebelum mendaftarkan
            </FieldDescription>
        </div>
    </div>
</div>
