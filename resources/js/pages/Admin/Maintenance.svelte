<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import * as Progress from '$shadcn/components/ui/progress';
    import {
        Wrench,
        ArrowLeft,
        Smartphone,
        Monitor,
        Camera,
        Maximize,
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { dashboard } from '$routes/admin';

    let deviceId = $state('');
    let showForm = $state(false);

    let checks = $state({
        fisik: { value: 50, label: 'Fisik', icon: Smartphone },
        screen: { value: 50, label: 'Layar', icon: Monitor },
        camera: { value: 50, label: 'Kamera', icon: Camera },
        body: { value: 50, label: 'Body', icon: Maximize },
    });

    let submitting = $state(false);

    function goBack() {
        router.visit(dashboard().url);
    }

    function startCheck() {
        if (!deviceId.trim()) {
            toast.error('Masukkan ID perangkat terlebih dahulu');
            return;
        }
        showForm = true;
    }

    function updateCheck(key: keyof typeof checks, val: number) {
        checks[key].value = val;
    }

    function getColor(v: number) {
        if (v <= 30) return 'bg-red-500';
        if (v <= 60) return 'bg-yellow-500';
        return 'bg-emerald-500';
    }

    function handleSubmit() {
        submitting = true;
        setTimeout(() => {
            submitting = false;
            toast.success('Data maintenance berhasil disimpan');
            showForm = false;
            deviceId = '';
            checks = {
                fisik: { value: 50, label: 'Fisik', icon: Smartphone },
                screen: { value: 50, label: 'Layar', icon: Monitor },
                camera: { value: 50, label: 'Kamera', icon: Camera },
                body: { value: 50, label: 'Body', icon: Maximize },
            };
        }, 1000);
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
                <Wrench class="size-6 text-amber-400" />
                <h1 class="text-2xl font-bold">Maintenance</h1>
            </div>
            <p class="text-sm text-muted-foreground mt-1">
                Perawatan perangkat (fisik, layar, kamera, body)
            </p>
        </div>
    </div>

    <!-- Search Device -->
    {#if !showForm}
        <Card.Root class="max-w-xl border-border/60 bg-card/60 backdrop-blur-xl">
            <Card.Content class="p-6 space-y-4">
                <div class="space-y-2">
                    <Label for="device-id">Device ID</Label>
                    <Input
                        id="device-id"
                        bind:value={deviceId}
                        placeholder="Masukkan ID Perangkat"
                    />
                </div>
                <Button class="w-full gap-2" onclick={startCheck}>
                    <Wrench class="size-4" />
                    Mulai Maintenance
                </Button>
            </Card.Content>
        </Card.Root>
    {/if}

    <!-- Maintenance Form -->
    {#if showForm}
        <Card.Root class="max-w-2xl border-border/60 bg-card/60 backdrop-blur-xl">
            <Card.Header>
                <Card.Title>Maintenance: {deviceId}</Card.Title>
                <Card.Description
                    >Atur kondisi perangkat berdasarkan bagian</Card.Description
                >
            </Card.Header>
            <Card.Content class="space-y-6">
                {#each Object.entries(checks) as [key, check]}
                    {@const Icon = check.icon}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Icon class="size-4 text-amber-400" />
                                <span class="font-medium">{check.label}</span>
                            </div>
                            <span class="text-sm font-mono">{check.value}%</span>
                        </div>
                        <input
                            type="range"
                            min="0"
                            max="100"
                            value={check.value}
                            oninput={(e) =>
                                updateCheck(
                                    key as keyof typeof checks,
                                    parseInt(e.currentTarget.value),
                                )}
                            class="w-full h-2 rounded-full appearance-none cursor-pointer bg-muted"
                            style="accent-color: currentColor"
                        />
                        <Progress.Root
                            value={check.value}
                            class="h-2 {getColor(check.value)}"
                        />
                    </div>
                {/each}
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3">
                <Button
                    variant="outline"
                    onclick={() => {
                        showForm = false;
                        deviceId = '';
                    }}
                >
                    Batal
                </Button>
                <Button onclick={handleSubmit} disabled={submitting} class="gap-2">
                    {#if submitting}
                        <div
                            class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                        />
                        Menyimpan...
                    {:else}
                        Simpan Maintenance
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    {/if}
</div>
