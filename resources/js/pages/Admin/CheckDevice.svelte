<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import {
        ShieldCheck,
        Search,
        ArrowLeft,
        CheckCircle2,
        XCircle,
    } from '@lucide/svelte';
    import * as InputGroup from '$shadcn/components/ui/input-group';
    import { dashboard } from '$routes/admin';

    let deviceId = $state('');
    let checking = $state(false);
    let result = $state<{ status: string; name: string } | null>(null);

    async function handleCheck() {
        if (!deviceId.trim()) return;

        checking = true;
        result = null;

        // Simulasi pengecekan
        await new Promise((r) => setTimeout(r, 1500));

        result = {
            status: Math.random() > 0.3 ? 'verified' : 'unverified',
            name: 'Device ' + deviceId,
        };

        checking = false;
    }

    function goBack() {
        router.visit(routeUrl(dashboard()));
    }
</script>

<div class="min-h-screen bg-background p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <Button variant="ghost" size="icon" onclick={goBack}>
            <ArrowLeft class="size-5" />
        </Button>
        <div>
            <div class="flex items-center gap-2">
                <ShieldCheck class="size-6 text-emerald-400" />
                <h1 class="text-2xl font-bold">Check Device</h1>
            </div>
            <p class="text-sm text-muted-foreground mt-1">
                Verifikasi dan periksa perangkat
            </p>
        </div>
    </div>

    <!-- Search Card -->
    <Card.Root class="max-w-xl border-border/60 bg-card/60 backdrop-blur-xl">
        <Card.Content class="p-6 space-y-4">
            <InputGroup.Root class="h-12 border-border/60 bg-background/40">
                <InputGroup.Addon>
                    <Search class="size-4 text-primary" />
                </InputGroup.Addon>
                <InputGroup.Input
                    bind:value={deviceId}
                    placeholder="Masukkan ID Perangkat..."
                    class="text-sm"
                />
            </InputGroup.Root>

            <Button
                class="w-full gap-2"
                disabled={checking || !deviceId.trim()}
                onclick={handleCheck}
            >
                {#if checking}
                    <div
                        class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                    />
                    Memeriksa...
                {:else}
                    <ShieldCheck class="size-4" />
                    Periksa Perangkat
                {/if}
            </Button>
        </Card.Content>
    </Card.Root>

    <!-- Result -->
    {#if result}
        <Card.Root
            class="max-w-xl border-border/60 bg-card/60 backdrop-blur-xl {result.status === 'verified' ? 'border-emerald-500/30' : 'border-red-500/30'}"
        >
            <Card.Content class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    {#if result.status === 'verified'}
                        <div
                            class="size-12 rounded-full bg-emerald-500/20 flex items-center justify-center"
                        >
                            <CheckCircle2 class="size-6 text-emerald-400" />
                        </div>
                    {:else}
                        <div
                            class="size-12 rounded-full bg-red-500/20 flex items-center justify-center"
                        >
                            <XCircle class="size-6 text-red-400" />
                        </div>
                    {/if}
                    <div>
                        <div class="font-semibold text-lg">{result.name}</div>
                        <div
                            class="text-sm {result.status === 'verified' ? 'text-emerald-400' : 'text-red-400'}"
                        >
                            {result.status === 'verified'
                                ? 'Perangkat Terverifikasi'
                                : 'Perangkat Tidak Terverifikasi'}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="space-y-1">
                        <span class="text-muted-foreground">ID</span>
                        <div class="font-mono">{deviceId}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-muted-foreground">Status</span>
                        <div class="font-mono">
                            {result.status === 'verified' ? 'Aktif' : 'Non-Aktif'}
                        </div>
                    </div>
                </div>
            </Card.Content>
        </Card.Root>
    {/if}
</div>
