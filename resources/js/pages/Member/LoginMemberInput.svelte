<script lang="ts">
    import { router, useHttp } from '@inertiajs/svelte';
    import { Fingerprint, Smartphone, ArrowRight, ArrowLeft, AlertCircle, IdCard, Lock, CheckCircle2 } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';
    import { toast } from 'svelte-sonner';

    import LayoutBG from '$/components/LayoutBG.svelte';
    import { Button } from '$shadcn/components/ui/button';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import * as Card from '$shadcn/components/ui/card';
    import * as Alert from '$shadcn/components/ui/alert';
    import { Badge } from '$shadcn/components/ui/badge';
    import { home } from '$routes';
    import loginMember from '$routes/user/loginMember';
    import { dashboard } from '$routes/user';

    type DeviceInfo = {
        model_id: string;
        model_name: string;
        imei: string | null;
        mac_address: string | null;
        approved: boolean;
        registered: boolean;
    };

    let { device, error: serverError }: { device: DeviceInfo | null; error: string | null } = $props();

    const http = useHttp({ nik: '', password: '' });

    let localError = $state('');

    let blocked = $derived(!device || !device.approved || !device.registered || !!serverError);
    let deviceId = $derived(device?.model_id ?? '');

    function handleNIKInput(e: Event) {
        const input = e.target as HTMLInputElement;
        http.nik = input.value.replace(/\D/g, '').slice(0, 6);
    }

    function handleSubmit(e:Event) {
        e.preventDefault()
        if (http.nik.length !== 6 || !http.password) {
            localError = 'Harap isi NIK (6 digit) dan password';
            return;
        }

        localError = '';

        http.post(loginMember.store(deviceId).url, {
            onSuccess: () => {
                toast.success('Absensi berhasil! Terima kasih telah melakukan absensi hari ini.');
                setTimeout(() => {
                    router.visit(dashboard(deviceId).url);
                }, 1000);
            },
            onError: (errors) => {
                localError = errors.error || errors.message || 'Terjadi kesalahan';
            },
        });
    }

    let today = $derived(new Date().toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    }));
</script>

<LayoutBG class="min-h-screen bg-background flex items-center justify-center p-4">
    <Particles particleCount={200} particleColors={['#000000', '#ff00ae', '#ffffff']} particleBaseSize={100} speed={0.05} class="absolute inset-0 z-0 opacity-30" />

    <div class="w-full max-w-md relative z-10">
        <button onclick={() => router.visit(dashboard(deviceId).url)} class="mb-6 flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors">
            <ArrowLeft class="size-4" /> Kembali ke Dashboard
        </button>

        <Card.Root class="border-border/60 bg-card/80 backdrop-blur-xl">
            <Card.Header class="text-center">
                <div class="mx-auto size-14 rounded-2xl bg-primary/20 flex items-center justify-center mb-4">
                    <Fingerprint class="size-7 text-primary" />
                </div>
                <Card.Title class="text-2xl font-bold">Absensi Tambahan</Card.Title>
                <Card.Description class="text-sm">Input absen untuk pengguna lain — {today}</Card.Description>
            </Card.Header>

            <Card.Content class="space-y-4">
                {#if device}
                    <div class="rounded-xl bg-muted/30 p-3 space-y-1.5">
                        <div class="flex items-center gap-2 justify-center">
                            <Smartphone class="size-4 text-primary shrink-0" />
                            <span class="font-semibold text-sm">{device.model_name}</span>
                        </div>
                        {#if device.imei}
                            <div class="text-center">
                                <span class="text-[10px] text-primary/70 uppercase tracking-wider">IMEI</span>
                                <code class="text-xs font-mono font-bold text-foreground">{device.imei}</code>
                            </div>
                        {:else if device.mac_address}
                            <div class="text-center">
                                <span class="text-[10px] text-primary/70 uppercase tracking-wider">MAC</span>
                                <code class="text-xs font-mono font-bold text-foreground">{device.mac_address}</code>
                            </div>
                        {:else}
                            <div class="text-center">
                                <code class="text-xs text-muted-foreground">{device.model_id}</code>
                            </div>
                        {/if}
                    </div>
                {/if}

                {#if serverError}
                    <Alert.Root variant="destructive">
                        <AlertCircle class="size-4" />
                        <Alert.Description>{serverError}</Alert.Description>
                    </Alert.Root>
                {/if}

                {#if localError}
                    <Alert.Root variant="destructive">
                        <AlertCircle class="size-4" />
                        <Alert.Description>{localError}</Alert.Description>
                    </Alert.Root>
                {/if}

                <form onsubmit={handleSubmit} class="space-y-4">
                    <div class="space-y-2">
                        <Label for="nik">NIK</Label>
                        <div class="relative">
                            <IdCard class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                            <Input
                                id="nik"
                                type="text"
                                inputmode="numeric"
                                value={http.nik}
                                oninput={handleNIKInput}
                                placeholder="6 digit NIK"
                                maxlength={6}
                                class="pl-10 font-mono tracking-widest text-center"
                                disabled={blocked || http.processing}
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <div class="relative">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                            <Input id="password" type="password" bind:value={http.password} placeholder="Password" class="pl-10" disabled={blocked || http.processing} />
                        </div>
                    </div>

                    <Button type="submit" class="w-full gap-2" disabled={http.processing || blocked}>
                        {#if http.processing}
                            <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...
                        {:else}
                            <CheckCircle2 class="size-4" />
                            Absen Sekarang
                            <ArrowRight class="size-4" />
                        {/if}
                    </Button>
                </form>
            </Card.Content>
        </Card.Root>
    </div>
</LayoutBG>
