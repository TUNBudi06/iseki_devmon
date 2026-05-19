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
        ImageOff,
    } from '@lucide/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { home } from '$routes';
    import { detailPhone } from '$routes/phone';

    // ─── Types ──────────────────────────────────────────────────
    type DeviceType = 'phone' | 'tablet';

    type Device = {
        id: string;
        name: string;
        photo?: string;
        battery: number;
        user: string | null;
        ram: string;
        storage: string;
        status: 'active' | 'inactive';
        registered: boolean;
        type: DeviceType; // ← Added for filter support
    };
    // ─── Data ───────────────────────────────────────────────────
    const devices: Device[] = [
        {
            id: 'DEV-001',
            name: 'Samsung Galaxy A54',
            photo: 'https://placehold.co/400x600/1a1a2e/eee?text=Galaxy+A54&font=roboto',
            battery: 85,
            user: 'Budi Santoso',
            ram: '8GB',
            storage: '256GB',
            status: 'active',
            registered: true,
            type: 'phone',
        },
        {
            id: 'DEV-002',
            name: 'Xiaomi Redmi Note 12',
            photo: 'https://placehold.co/400x600/16213e/eee?text=Redmi+Note+12&font=roboto',
            battery: 23,
            user: 'Siti Rahayu',
            ram: '6GB',
            storage: '128GB',
            status: 'active',
            registered: true,
            type: 'phone',
        },
        {
            id: 'DEV-003',
            name: 'OPPO A78',
            photo: 'https://placehold.co/400x600/0f3460/eee?text=OPPO+A78&font=roboto',
            battery: 60,
            user: null,
            ram: '8GB',
            storage: '128GB',
            status: 'inactive',
            registered: true,
            type: 'phone',
        },
        {
            id: 'DEV-004',
            name: 'Realme C55',
            photo: 'https://placehold.co/400x600/1a1a2e/eee?text=Realme+C55&font=roboto',
            battery: 12,
            user: null,
            ram: '6GB',
            storage: '128GB',
            status: 'inactive',
            registered: false,
            type: 'phone',
        },
        {
            id: 'DEV-005',
            name: 'iPhone 13',
            photo: 'https://placehold.co/400x600/16213e/eee?text=iPhone+13&font=roboto',
            battery: 95,
            user: 'Ahmad Fauzi',
            ram: '4GB',
            storage: '128GB',
            status: 'active',
            registered: true,
            type: 'phone',
        },
        {
            id: 'DEV-006',
            name: 'Vivo Y35',
            photo: 'https://placehold.co/400x600/0f3460/eee?text=Vivo+Y35&font=roboto',
            battery: 47,
            user: null,
            ram: '8GB',
            storage: '128GB',
            status: 'inactive',
            registered: false,
            type: 'phone',
        },
        {
            id: 'DEV-007',
            name: 'Samsung Galaxy S23',
            photo: 'https://placehold.co/400x600/1a1a2e/eee?text=Galaxy+S23&font=roboto',
            battery: 71,
            user: 'Dewi Kartika',
            ram: '8GB',
            storage: '256GB',
            status: 'active',
            registered: true,
            type: 'phone',
        },
        {
            id: 'DEV-008',
            name: 'Poco X5 Pro',
            photo: 'https://placehold.co/400x600/16213e/eee?text=Poco+X5+Pro&font=roboto',
            battery: 33,
            user: null,
            ram: '8GB',
            storage: '256GB',
            status: 'inactive',
            registered: true,
            type: 'phone',
        },
        // ─── Tablet Devices (for filter testing) ───────────────
        {
            id: 'DEV-009',
            name: 'iPad Air 5th Gen',
            photo: 'https://placehold.co/600x400/0f3460/eee?text=iPad+Air&font=roboto',
            battery: 8,
            user: 'Rina Wijaya',
            ram: '8GB',
            storage: '256GB',
            status: 'active',
            registered: true,
            type: 'tablet',
        },
        {
            id: 'DEV-010',
            name: 'Samsung Galaxy Tab S9',
            photo: 'https://placehold.co/600x400/1a1a2e/eee?text=Tab+S9&font=roboto',
            battery: 100,
            user: null,
            ram: '12GB',
            storage: '512GB',
            status: 'inactive',
            registered: false,
            type: 'tablet',
        },
    ];

    // ─── State ──────────────────────────────────────────────────
    type Filter =
        | 'all'
        | 'active'
        | 'inactive'
        | 'registered'
        | 'unregistered'
        | 'tablet'
        | 'phone';
    let activeFilter = $state<Filter>('all');
    let search = $state('');
    let loading = $state(false);
    let failedImages = $state<Set<string>>(new Set());

    // ─── Filtering Logic (Updated for type filter) ─────────────
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
                          : activeFilter === 'unregistered'
                            ? !d.registered
                            : activeFilter === 'tablet'
                              ? d.type === 'tablet'
                              : activeFilter === 'phone'
                                ? d.type === 'phone'
                                : true;

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
                      : filter === 'unregistered'
                        ? !d.registered
                        : filter === 'tablet'
                          ? d.type === 'tablet'
                          : filter === 'phone'
                            ? d.type === 'phone'
                            : true,
        ).length;
    }

    // ─── Helpers ────────────────────────────────────────────────
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
        router.visit(routeUrl(detailPhone({ id: device.id })));
    }

    function handleImageError(deviceId: string) {
        failedImages.add(deviceId);
        failedImages = failedImages; // Trigger reactivity in Svelte 5
    }

    const filters: { label: string; value: Filter }[] = [
        { label: 'Semua', value: 'all' },
        { label: 'Aktif', value: 'active' },
        { label: 'Non-Aktif', value: 'inactive' },
        { label: 'Terdaftar', value: 'registered' },
        { label: 'Belum Terdaftar', value: 'unregistered' },
        { label: 'Tablet', value: 'tablet' },
        { label: 'Phone', value: 'phone' },
    ];
</script>

<LayoutBG class="bg-mesh-pink min-h-screen">
    <Navbar>
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
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 lg:gap-8"
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

                            {#if !device.photo || failedImages.has(device.id)}
                                <div
                                    class="h-full w-full flex items-center justify-center bg-muted/50 backdrop-blur-sm"
                                >
                                    <div class="text-center space-y-2">
                                        <ImageOff
                                            class="size-8 mx-auto text-muted-foreground/70"
                                        />
                                        <div
                                            class="text-xs text-muted-foreground"
                                        >
                                            No image
                                        </div>
                                    </div>
                                </div>
                            {:else}
                                <img
                                    src={device.photo}
                                    alt={device.name}
                                    onerror={() => handleImageError(device.id)}
                                    class="h-full w-full object-cover transition-transform duration-700
                {isRegistered ? 'group-hover:scale-105' : ''}"
                                />
                            {/if}

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
                                {#await batteryIcon(device.battery) then Icon}
                                    <Icon class="size-3" />
                                {/await}
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
                                    class="absolute bottom-3 left-3 z-30 flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium backdrop-blur-lg {device.status ===
                                    'active'
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
