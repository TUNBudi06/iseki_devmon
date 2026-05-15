<script lang="ts">
    import SidebarProvider from '$/components/SidebarProvider.svelte';
    import { router } from '@inertiajs/svelte';
    import * as Card from '$shadcn/components/ui/card/index.js';
    import * as Table from '$shadcn/components/ui/table/index.js';
    import * as Badge from '$shadcn/components/ui/badge/index.js';
    import { Button } from '$shadcn/components/ui/button';
    import { Input } from '$shadcn/components/ui/input';
    import { useInterval } from 'runed';
    import {
        Monitor,
        CheckCircle2,
        XCircle,
        RefreshCw,
        Search,
        Activity,
    } from '@lucide/svelte';

    // --- Dummy Data ---
    type DeviceLog = {
        id: string;
        deviceId: string;
        deviceName: string;
        context: string;
        time: string;
        status: 'active' | 'inactive';
    };

    const allLogs: DeviceLog[] = [
        {
            id: '1',
            deviceId: 'THE56ZKN',
            deviceName: 'ThinkPad E14',
            context: 'Login berhasil',
            time: '07:40',
            status: 'active',
        },
        {
            id: '2',
            deviceId: 'AND12XKP',
            deviceName: 'Samsung A54',
            context: 'Sinkronisasi data',
            time: '07:42',
            status: 'active',
        },
        {
            id: '3',
            deviceId: 'IPH99ZQA',
            deviceName: 'iPhone 13',
            context: 'Logout dari sistem',
            time: '07:45',
            status: 'inactive',
        },
        {
            id: '4',
            deviceId: 'LNV34MNB',
            deviceName: 'Lenovo Tab P11',
            context: 'Update firmware',
            time: '07:50',
            status: 'active',
        },
        {
            id: '5',
            deviceId: 'HPX22KZT',
            deviceName: 'HP EliteBook',
            context: 'Login berhasil',
            time: '08:00',
            status: 'active',
        },
        {
            id: '6',
            deviceId: 'OPP77YTR',
            deviceName: 'OPPO Reno 8',
            context: 'Koneksi terputus',
            time: '08:05',
            status: 'inactive',
        },
        {
            id: '7',
            deviceId: 'SAM45PLK',
            deviceName: 'Galaxy Tab S8',
            context: 'Login berhasil',
            time: '08:10',
            status: 'active',
        },
        {
            id: '8',
            deviceId: 'DEL88WQM',
            deviceName: 'Dell Latitude',
            context: 'Download laporan',
            time: '08:15',
            status: 'active',
        },
        {
            id: '9',
            deviceId: 'XIA66NJK',
            deviceName: 'Xiaomi 12T',
            context: 'Sinkronisasi data',
            time: '08:20',
            status: 'inactive',
        },
        {
            id: '10',
            deviceId: 'ASU11PKQ',
            deviceName: 'ASUS Vivobook',
            context: 'Login berhasil',
            time: '08:25',
            status: 'active',
        },
        {
            id: '11',
            deviceId: 'RLM23TQZ',
            deviceName: 'Realme GT Neo',
            context: 'Update profil pengguna',
            time: '08:30',
            status: 'active',
        },
        {
            id: '12',
            deviceId: 'TCL55WBN',
            deviceName: 'TCL Tab 10',
            context: 'Logout dari sistem',
            time: '08:35',
            status: 'inactive',
        },
        {
            id: '13',
            deviceId: 'HWI99KLP',
            deviceName: 'Huawei P50',
            context: 'Login berhasil',
            time: '08:40',
            status: 'active',
        },
        {
            id: '14',
            deviceId: 'VIV44ZNM',
            deviceName: 'Vivo Y35',
            context: 'Koneksi terputus',
            time: '08:45',
            status: 'inactive',
        },
        {
            id: '15',
            deviceId: 'NOK77TRQ',
            deviceName: 'Nokia G60',
            context: 'Login berhasil',
            time: '08:50',
            status: 'active',
        },
    ];

    const stats = {
        total: 20,
        active: 14,
        inactive: 6,
    };

    // --- Search & Filter ---
    let search = $state('');

    let filteredLogs = $derived(
        search.trim() === ''
            ? allLogs
            : allLogs.filter(
                  (log) =>
                      log.deviceId
                          .toLowerCase()
                          .includes(search.toLowerCase()) ||
                      log.deviceName
                          .toLowerCase()
                          .includes(search.toLowerCase()) ||
                      log.context.toLowerCase().includes(search.toLowerCase()),
              ),
    );

    // --- Auto Refresh ---
    let isRefreshing = $state(false);

    async function handleRefresh() {
        isRefreshing = true;
        // router.reload({ only: ['devices'] });
        await new Promise((r) => setTimeout(r, 800)); // simulate
        isRefreshing = false;
    }

    // Auto-refresh every 10s
    useInterval(10_000, { callback: handleRefresh });
</script>

<SidebarProvider>
    <div class="flex flex-col gap-6 p-6 w-full">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Dashboard Device
                </h1>
                <p class="text-muted-foreground text-sm mt-0.5">
                    Monitor dan pantau semua aktivitas device secara realtime
                </p>
            </div>
            <Button
                variant="outline"
                size="sm"
                class="gap-2"
                onclick={handleRefresh}
                disabled={isRefreshing}
            >
                <RefreshCw
                    class="size-4 {isRefreshing ? 'animate-spin' : ''}"
                />
                Refresh
            </Button>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <Card.Root>
                <Card.Content class="flex items-center gap-4 pt-6">
                    <div class="p-2.5 rounded-lg bg-primary/10">
                        <Monitor class="size-5 text-primary" />
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Total Device
                        </p>
                        <p class="text-2xl font-bold">{stats.total}</p>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Semua device terdaftar
                        </p>
                    </div>
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Content class="flex items-center gap-4 pt-6">
                    <div class="p-2.5 rounded-lg bg-green-500/10">
                        <CheckCircle2
                            class="size-5 text-green-600 dark:text-green-400"
                        />
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Device Aktif
                        </p>
                        <p
                            class="text-2xl font-bold text-green-600 dark:text-green-400"
                        >
                            {stats.active}
                        </p>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Sedang digunakan
                        </p>
                    </div>
                </Card.Content>
            </Card.Root>

            <Card.Root>
                <Card.Content class="flex items-center gap-4 pt-6">
                    <div class="p-2.5 rounded-lg bg-destructive/10">
                        <XCircle class="size-5 text-destructive" />
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Device Nonaktif
                        </p>
                        <p class="text-2xl font-bold text-destructive">
                            {stats.inactive}
                        </p>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Tidak aktif digunakan
                        </p>
                    </div>
                </Card.Content>
            </Card.Root>
        </div>

        <!-- Log Table -->
        <Card.Root class="flex-1">
            <Card.Header>
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <Card.Title class="flex items-center gap-2">
                            <Activity class="size-4" />
                            Log Aktivitas Device
                        </Card.Title>
                        <Card.Description class="mt-1">
                            {filteredLogs.length} entri ditemukan
                        </Card.Description>
                    </div>
                    <!-- Search -->
                    <div class="relative w-full sm:w-64">
                        <Search
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            bind:value={search}
                            placeholder="Cari device atau aktivitas..."
                            class="pl-9"
                        />
                    </div>
                </div>
            </Card.Header>
            <Card.Content class="p-0">
                <div class="overflow-y-auto max-h-[520px]">
                    <Table.Root>
                        <Table.Header class="sticky top-0 bg-background z-10">
                            <Table.Row>
                                <Table.Head class="w-32 pl-6"
                                    >Device ID</Table.Head
                                >
                                <Table.Head>Nama Device</Table.Head>
                                <Table.Head>Aktivitas</Table.Head>
                                <Table.Head class="w-24 text-center"
                                    >Status</Table.Head
                                >
                                <Table.Head class="w-20 text-right pr-6"
                                    >Waktu</Table.Head
                                >
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#if filteredLogs.length === 0}
                                <Table.Row>
                                    <Table.Cell
                                        colspan={5}
                                        class="text-center py-16 text-muted-foreground"
                                    >
                                        Tidak ada log yang cocok dengan
                                        pencarian
                                    </Table.Cell>
                                </Table.Row>
                            {:else}
                                {#each filteredLogs as log (log.id)}
                                    <Table.Row class="hover:bg-muted/50">
                                        <Table.Cell
                                            class="font-mono text-sm pl-6"
                                            >{log.deviceId}</Table.Cell
                                        >
                                        <Table.Cell class="font-medium"
                                            >{log.deviceName}</Table.Cell
                                        >
                                        <Table.Cell
                                            class="text-muted-foreground text-sm"
                                            >{log.context}</Table.Cell
                                        >
                                        <Table.Cell class="text-center">
                                            {#if log.status === 'active'}
                                                <Badge.Root
                                                    variant="outline"
                                                    class="text-green-600 border-green-300 bg-green-50 dark:bg-green-950/30 dark:border-green-800 dark:text-green-400"
                                                >
                                                    Aktif
                                                </Badge.Root>
                                            {:else}
                                                <Badge.Root
                                                    variant="outline"
                                                    class="text-muted-foreground"
                                                >
                                                    Nonaktif
                                                </Badge.Root>
                                            {/if}
                                        </Table.Cell>
                                        <Table.Cell
                                            class="text-right text-sm text-muted-foreground pr-6"
                                            >{log.time}</Table.Cell
                                        >
                                    </Table.Row>
                                {/each}
                            {/if}
                        </Table.Body>
                    </Table.Root>
                </div>
            </Card.Content>
        </Card.Root>
    </div>
</SidebarProvider>
