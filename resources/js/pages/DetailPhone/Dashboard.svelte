<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { router } from '@inertiajs/svelte';
    import { Button } from '$shadcn/components/ui/button';
    import * as InputGroup from '$shadcn/components/ui/input-group';
    import * as Card from '$shadcn/components/ui/card';
    import {
        ArrowRightCircle,
        SearchIcon,
        BatteryLow,
        BatteryMedium,
        BatteryFull,
    } from '@lucide/svelte';
    import {routeUrl} from "@tunbudi06/inertia-route-helper";
    import {home} from "$routes";

    type Device = {
        id: string;
        name: string;
        photo: string;
        battery: number;
        user: string | null;
        ram: string;
        storage: string;
        status: 'active' | 'inactive';
        registered: boolean;
    };

    const devices: Device[] = [
        {
            id: 'DEV-001',
            name: 'Samsung Galaxy A54',
            photo: 'https://th.bing.com/th/id/OIP.uCDsEosK52ltHkDVDNch3wHaE8?o=7rm=3&rs=1&pid=ImgDetMain&o=7&rm=3',
            battery: 85,
            user: 'Budi Santoso',
            ram: '8GB',
            storage: '256GB',
            status: 'active',
            registered: true,
        },
        {
            id: 'DEV-002',
            name: 'Xiaomi Redmi Note 12',
            photo: 'https://tse1.mm.bing.net/th/id/OIP.Ba58RDy2zxZhX-B06l8begHaHa?rs=1&pid=ImgDetMain&o=7&rm=3',
            battery: 23,
            user: 'Siti Rahayu',
            ram: '6GB',
            storage: '128GB',
            status: 'active',
            registered: true,
        },
        {
            id: 'DEV-003',
            name: 'OPPO A78',
            photo: 'https://m.media-amazon.com/images/I/41WG5p9a9bL.jpg',
            battery: 60,
            user: null,
            ram: '8GB',
            storage: '128GB',
            status: 'inactive',
            registered: true,
        },
        {
            id: 'DEV-004',
            name: 'Realme C55',
            photo: 'https://www.bing.com/th/id/OIP.CeOotHMOVLAx9_ZMUVZQAQHaHa?w=193&h=193&c=8&rs=1&qlt=90&o=6&dpr=1.1&pid=3.1&rm=2',
            battery: 12,
            user: null,
            ram: '4GB',
            storage: '64GB',
            status: 'inactive',
            registered: false,
        },
        {
            id: 'DEV-005',
            name: 'iPhone 13',
            photo: 'https://th.bing.com/th/id/OIP.V1_EKiKp6DBulYdKIdHOewAAAA?w=254&h=203&c=7&r=0&o=7&dpr=1.1&pid=1.7&rm=3',
            battery: 95,
            user: 'Ahmad Fauzi',
            ram: '4GB',
            storage: '128GB',
            status: 'active',
            registered: true,
        },
        {
            id: 'DEV-006',
            name: 'Vivo Y35',
            photo: 'https://th.bing.com/th/id/OIP.YB_Cdkv7DMdXBrnoBjJTCgAAAA?w=198&h=141&c=7&r=0&o=7&dpr=1.1&pid=1.7&rm=3',
            battery: 47,
            user: null,
            ram: '8GB',
            storage: '128GB',
            status: 'inactive',
            registered: false,
        },
        {
            id: 'DEV-007',
            name: 'Samsung Galaxy S23',
            photo: 'https://tse4.mm.bing.net/th/id/OIP.5jT6INY2KTIjIuZbR2M_EwHaNY?rs=1&pid=ImgDetMain&o=7&rm=3',
            battery: 71,
            user: 'Dewi Kartika',
            ram: '8GB',
            storage: '256GB',
            status: 'active',
            registered: true,
        },
        {
            id: 'DEV-008',
            name: 'Poco X5 Pro',
            photo: 'https://i02.appmifile.com/mi-com-product/fly-birds/poco-x5-pro-5g/pc/img10-1.jpg',
            battery: 33,
            user: null,
            ram: '8GB',
            storage: '256GB',
            status: 'inactive',
            registered: true,
        },
    ];

    type Filter = 'all' | 'active' | 'inactive' | 'registered' | 'unregistered';
    let activeFilter = $state<Filter>('all');
    let search = $state('');
    let loading = $state(false);

    const filtered = $derived(
        devices.filter((d) => {
            const matchSearch =
                d.name.toLowerCase().includes(search.toLowerCase()) ||
                d.id.toLowerCase().includes(search.toLowerCase());
            const matchFilter =
                activeFilter === 'all'
                    ? true
                    : activeFilter === 'active'
                      ? d.status === 'active'
                      : activeFilter === 'inactive'
                        ? d.status === 'inactive'
                        : activeFilter === 'registered'
                          ? d.registered
                          : !d.registered;
            return matchSearch && matchFilter;
        }),
    );

    function countFilter(filter: Filter) {
        return devices.filter((d) =>
            filter === 'all'
                ? true
                : filter === 'active'
                  ? d.status === 'active'
                  : filter === 'inactive'
                    ? d.status === 'inactive'
                    : filter === 'registered'
                      ? d.registered
                      : !d.registered,
        ).length;
    }

    function batteryStyle(b: number) {
        if (b <= 20)
            return 'bg-destructive text-destructive-foreground shadow-[0_0_20px_oklch(0.55_0.22_25_/_0.35)]';
        if (b <= 50)
            return 'bg-primary/85 text-primary-foreground shadow-[0_0_24px_oklch(0.72_0.22_350_/_0.28)]';
        return 'bg-primary text-primary-foreground shadow-[0_0_30px_oklch(0.72_0.22_350_/_0.38)]';
    }

    function batteryIcon(b: number) {
        if (b <= 20) return BatteryLow;
        if (b <= 50) return BatteryMedium;
        return BatteryFull;
    }

    function cardGlow(status: Device['status']) {
        return status === 'active'
            ? 'hover:shadow-[0_0_45px_oklch(0.72_0.22_350_/_0.22)]'
            : 'hover:shadow-[0_0_25px_oklch(0.30_0.05_350_/_0.15)]';
    }

    function handleCardClick(device: Device) {
        if (!device.registered) return;
        router.visit(`/device/${device.id}`);
    }

    const filters: { label: string; value: Filter }[] = [
        { label: 'Semua', value: 'all' },
        { label: 'Aktif', value: 'active' },
        { label: 'Non-Aktif', value: 'inactive' },
        { label: 'Terdaftar', value: 'registered' },
        { label: 'Belum Terdaftar', value: 'unregistered' },
    ];
</script>

<LayoutBG class="bg-mesh-pink min-h-screen">
    <Navbar
        class="sticky top-0 z-50 border-b border-white/10 bg-background/70 backdrop-blur-2xl justify-between px-6"
    >
        <div>
            <div
                class="text-lg font-semibold tracking-tight text-gradient-pink"
            >
                DevControl
            </div>
            <div class="text-xs text-muted-foreground">
                Device Management System
            </div>
        </div>
        <Button
            size="sm"
            variant="ghost"
            class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
            onclick={() => router.visit(routeUrl(home()))}
        >
            Kembali
            <ArrowRightCircle class="size-4" />
        </Button>
    </Navbar>

    <div class="p-6 space-y-6">
        <!-- Search + Filter -->
        <Card.Root
            class="overflow-hidden border border-border/60 bg-card/60 backdrop-blur-2xl shadow-2xl"
        >
            <Card.Content class="p-5 space-y-4">
                <InputGroup.Root class="h-12 border-border/60 bg-background/40">
                    <InputGroup.Addon>
                        <SearchIcon class="size-4 text-primary" />
                    </InputGroup.Addon>
                    <InputGroup.Input
                        bind:value={search}
                        placeholder="Cari nama perangkat atau ID..."
                        class="text-sm"
                    />
                </InputGroup.Root>
                <div class="flex flex-wrap gap-2">
                    {#each filters as f}
                        <Button
                            size="sm"
                            variant={activeFilter === f.value
                                ? 'default'
                                : 'outline'}
                            onclick={() => (activeFilter = f.value)}
                            class="rounded-full transition-all duration-300"
                        >
                            {f.label} ({countFilter(f.value)})
                        </Button>
                    {/each}
                </div>
            </Card.Content>
        </Card.Root>

        <!-- Header -->
        <div class="flex items-center justify-between px-1">
            <div>
                <div
                    class="text-2xl font-semibold tracking-tight text-card-foreground"
                >
                    Device Inventory
                </div>
                <div class="text-sm text-muted-foreground">
                    Menampilkan {filtered.length} perangkat
                </div>
            </div>
        </div>

        <!-- Loading skeleton -->
        {#if loading}
            <div
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5"
            >
                {#each Array(8) as _, i}
                    <div
                        class="overflow-hidden rounded-3xl border border-border bg-card/50 animate-pulse"
                    >
                        <div class="aspect-[3/4] shimmer-pink" />
                        <div class="space-y-3 p-4">
                            <div class="h-4 w-3/4 rounded-full shimmer-pink" />
                            <div class="h-3 w-1/2 rounded-full shimmer-pink" />
                            <div class="h-12 rounded-2xl shimmer-pink" />
                        </div>
                    </div>
                {/each}
            </div>
        {:else if filtered.length === 0}
            <Card.Root class="border-border/60 bg-card/50 backdrop-blur-xl">
                <Card.Content class="p-16 text-center text-muted-foreground">
                    Tidak ada perangkat yang cocok
                </Card.Content>
            </Card.Root>
        {:else}
            <div
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-5"
            >
                {#each filtered as device (device.id)}
                    {@const isRegistered = device.registered}
                    {@const BattIcon = batteryIcon(device.battery)}

                    <div
                        role="button"
                        tabindex={isRegistered ? 0 : -1}
                        aria-disabled={!isRegistered}
                        onclick={() => handleCardClick(device)}
                        onkeydown={(e) =>
                            e.key === 'Enter' && handleCardClick(device)}
                        class="
                            group overflow-hidden rounded-xl border border-border/60
                            bg-card/70 backdrop-blur-xl transition-all duration-500
                            {isRegistered
                            ? 'cursor-pointer hover:-translate-y-1.5 hover:border-primary/40 ' +
                              cardGlow(device.status)
                            : 'cursor-not-allowed opacity-50 grayscale-[50%] saturate-50'}
                        "
                    >
                        <!-- Photo -->
                        <div
                            class="relative aspect-[3/4] overflow-hidden bg-muted"
                        >
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-background/40 z-10"
                            />

                            <img
                                src={device.photo}
                                alt={device.name}
                                class="h-full w-full object-cover transition-transform duration-700
                                    {isRegistered
                                    ? 'group-hover:scale-105'
                                    : ''}"
                            />

                            <!-- Bottom gradient overlay -->
                            <div
                                class="absolute inset-x-0 bottom-0 h-15 bg-gradient-to-t from-background via-background/60 to-transparent z-20"
                            />

                            <!-- Battery badge -->
                            <div
                                class="absolute top-3 right-3 z-30 flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold backdrop-blur-md border border-white/10 {batteryStyle(
                                    device.battery,
                                )}"
                            >
                                <BattIcon class="size-3" />
                                <span>{device.battery}%</span>
                            </div>

                            <!-- Status / registered badge -->
                            {#if !isRegistered}
                                <div
                                    class="absolute bottom-3 left-3 z-30 flex items-center gap-2 rounded-full border border-orange-500/30 bg-orange-500/15 px-3 py-1.5 text-xs font-medium backdrop-blur-lg text-orange-400"
                                >
                                    <span
                                        class="size-2 rounded-full bg-current"
                                    />
                                    Belum Terdaftar
                                </div>
                            {:else}
                                <div
                                    class="absolute bottom-3 left-3 z-30 flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium backdrop-blur-lg
                                    {device.status === 'active'
                                        ? 'bg-primary/90 text-primary-foreground border-primary/30'
                                        : 'bg-muted/80 text-muted-foreground border-border'}"
                                >
                                    <span
                                        class="relative flex size-2 rounded-full bg-current"
                                    >
                                        {#if device.status === 'active'}
                                            <span
                                                class="animate-ping absolute inline-flex size-full rounded-full bg-current opacity-75"
                                            ></span>
                                        {/if}
                                    </span>
                                    {device.status === 'active'
                                        ? 'Aktif'
                                        : 'Non-Aktif'}
                                </div>
                            {/if}
                        </div>

                        <!-- Info -->
                        <div class="space-y-4 p-4">
                            <div class="space-y-2">
                                <div
                                    class="line-clamp-2 text-[15px] font-semibold tracking-tight text-card-foreground"
                                >
                                    {device.name}
                                </div>
                                <div
                                    class="inline-flex items-center rounded-full border border-border bg-muted/60 px-2.5 py-1 font-mono text-[11px] text-muted-foreground"
                                >
                                    {device.ram} / {device.storage}
                                </div>
                            </div>

                            <!-- User -->
                            <div
                                class="flex items-center gap-3 rounded-2xl border border-border/60 bg-muted/30 p-2.5"
                            >
                                <div class="min-w-0">
                                    <div
                                        class="text-[10px] uppercase tracking-[0.18em] text-muted-foreground"
                                    >
                                        User
                                    </div>
                                    <div
                                        class="truncate text-sm text-card-foreground {!device.user
                                            ? 'italic text-muted-foreground'
                                            : ''}"
                                    >
                                        {device.user ?? 'Tidak ada pengguna'}
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex items-center justify-between">
                                <span
                                    class="rounded-full border px-2.5 py-1 text-[11px] font-medium
                                    {isRegistered
                                        ? 'border-primary/20 bg-primary/10 text-primary'
                                        : 'border-orange-500/20 bg-orange-500/10 text-orange-400'}"
                                >
                                    {isRegistered ? 'Terdaftar' : 'Belum'}
                                </span>
                                <span
                                    class="font-mono text-[11px] text-muted-foreground"
                                    >{device.id}</span
                                >
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {/if}
    </div>
</LayoutBG>
