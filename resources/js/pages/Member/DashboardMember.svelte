<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import {
        Smartphone,
        CheckCircle2,
        Clock,
        User,
        IdCard,
        CalendarDays,
        ArrowLeft,
        ArrowRight,
    } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';

    import LayoutBG from '$/components/LayoutBG.svelte';
    import * as Card from '$shadcn/components/ui/card';
    import * as Table from '$shadcn/components/ui/table';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Button } from '$shadcn/components/ui/button';
    import { home } from '$routes';
    import { loginMember } from '$routes/user';

    type DeviceItem = {
        model_id: string;
        model_name: string;
        brand_name: string | null;
    };

    type UserAbsence = {
        nik: string;
        name: string;
        time_absence: string;
        date_absence: string;
    };

    let { deviceLatest, currentDevice, hasAbsence, noAbsence }:
        { deviceLatest: UserAbsence | null; currentDevice: { model_id: string; model_name: string }; hasAbsence: DeviceItem[]; noAbsence: DeviceItem[] } = $props();

    let today = $derived(new Date().toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    }));

    let deviceId = $derived(currentDevice.model_id);
</script>

<LayoutBG class="min-h-screen bg-background p-4">
    <Particles particleCount={200} particleColors={['#000000', '#ff00ae', '#ffffff']} particleBaseSize={100} speed={0.05} class="absolute inset-0 z-0 opacity-20" />

    <div class="w-full max-w-3xl mx-auto relative z-10 space-y-4">
        <!-- Back -->
        <button onclick={() => router.visit(home().url)} class="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors">
            <ArrowLeft class="size-4" /> Kembali
        </button>

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight">
                    <span class="text-sm font-normal text-muted-foreground">{currentDevice.model_name}</span>
                    <br />Dashboard Perangkat
                </h1>
                <p class="text-sm text-muted-foreground">{today}</p>
            </div>
            <div class="text-right">
                <Badge variant="secondary" class="text-xs">{hasAbsence.length + noAbsence.length} total device</Badge>
                <div class="text-xs text-muted-foreground font-mono mt-1">{currentDevice.model_id}</div>
            </div>
        </div>

        <!-- User Info Card — who last logged in on THIS device -->
        {#if deviceLatest}
            <Card.Root class="border-emerald-500/30 bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 backdrop-blur-xl overflow-hidden">
                <div class="absolute top-0 right-0 size-32 bg-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>

                <Card.Content class="p-4 relative">
                    <div class="flex items-center gap-2 mb-3">
                        <CheckCircle2 class="size-5 text-emerald-500" />
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400 text-sm">Terakhir Absen</span>
                    </div>
                    <div class="grid grid-cols-3 gap-x-4 gap-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <User class="size-4 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground">Nama:</span>
                            <span class="font-medium">{deviceLatest.name}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <IdCard class="size-4 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground">NIK:</span>
                            <span class="font-medium font-mono">{deviceLatest.nik}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Clock class="size-4 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground">Jam:</span>
                            <span class="font-medium font-mono">{deviceLatest.time_absence}</span>
                        </div>
                    </div>
                </Card.Content>
            </Card.Root>
        {:else}
            <Card.Root class="border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-amber-500/5 backdrop-blur-xl overflow-hidden">
                <Card.Content class="p-4 relative">
                    <div class="flex items-center gap-2">
                        <Clock class="size-5 text-amber-500" />
                        <span class="font-semibold text-amber-600 dark:text-amber-400 text-sm">Belum Ada Absen Hari Ini</span>
                    </div>
                    <p class="text-sm text-muted-foreground mt-1">Gunakan perangkat ini untuk melakukan absensi</p>
                </Card.Content>
            </Card.Root>
        {/if}

        <!-- Button to input another absence -->
        <div class="flex justify-center py-2">
            <Button onclick={() => router.visit(loginMember(deviceId).url)} class="gap-2" size="lg">
                <ArrowRight class="size-4" />
                Input Absence Lagi
            </Button>
        </div>

        <!-- Table: Sudah Absen (all devices, read-only) -->
        <Card.Root class="border-border/60 bg-card/80 backdrop-blur-xl">
            <Card.Header class="pb-3">
                <Card.Title class="text-base flex items-center gap-2">
                    <CheckCircle2 class="size-4 text-emerald-500" />
                    Sudah Absen
                    <Badge variant="secondary" class="ml-auto text-xs">{hasAbsence.length} device</Badge>
                </Card.Title>
            </Card.Header>

            <Card.Content>
                {#if hasAbsence.length === 0}
                    <div class="text-center py-6 text-muted-foreground text-sm">
                        Belum ada perangkat yang digunakan hari ini
                    </div>
                {:else}
                    <Table.Root>
                        <Table.Header>
                            <Table.Row class="text-xs">
                                <Table.Head>Perangkat</Table.Head>
                                <Table.Head>Brand</Table.Head>
                                <Table.Head class="w-24 text-right">Status</Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each hasAbsence as d (d.model_id)}
                                <Table.Row class="text-sm">
                                    <Table.Cell>
                                        <span class="font-medium">{d.model_name}</span>
                                        <div class="text-xs text-muted-foreground font-mono">{d.model_id}</div>
                                    </Table.Cell>
                                    <Table.Cell class="text-muted-foreground">{d.brand_name ?? '-'}</Table.Cell>
                                    <Table.Cell class="text-right">
                                        <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 text-xs">
                                            <CheckCircle2 class="size-3 mr-1" /> Sudah
                                        </Badge>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                {/if}
            </Card.Content>
        </Card.Root>

        <!-- Table: Belum Absen (all devices, read-only) -->
        <Card.Root class="border-border/60 bg-card/80 backdrop-blur-xl">
            <Card.Header class="pb-3">
                <Card.Title class="text-base flex items-center gap-2">
                    <Clock class="size-4 text-muted-foreground" />
                    Belum Absen
                    <Badge variant="secondary" class="ml-auto text-xs">{noAbsence.length} device</Badge>
                </Card.Title>
            </Card.Header>

            <Card.Content>
                {#if noAbsence.length === 0}
                    <div class="text-center py-6 text-muted-foreground text-sm">
                        Semua perangkat sudah digunakan hari ini
                    </div>
                {:else}
                    <Table.Root>
                        <Table.Header>
                            <Table.Row class="text-xs">
                                <Table.Head>Perangkat</Table.Head>
                                <Table.Head>Brand</Table.Head>
                                <Table.Head class="w-24 text-right">Status</Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each noAbsence as d (d.model_id)}
                                <Table.Row class="text-sm">
                                    <Table.Cell>
                                        <span class="font-medium">{d.model_name}</span>
                                        <div class="text-xs text-muted-foreground font-mono">{d.model_id}</div>
                                    </Table.Cell>
                                    <Table.Cell class="text-muted-foreground">{d.brand_name ?? '-'}</Table.Cell>
                                    <Table.Cell class="text-right">
                                        <Badge variant="outline" class="text-xs text-muted-foreground border-dashed">
                                            <Clock class="size-3 mr-1" /> Belum
                                        </Badge>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>
</LayoutBG>
