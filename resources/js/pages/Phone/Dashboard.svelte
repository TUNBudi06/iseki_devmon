<script lang="ts">
    import { onMount, onDestroy } from 'svelte';
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { router } from '@inertiajs/svelte';

    let pollInterval: ReturnType<typeof setInterval> | null = null;

    onMount(() => {
        pollInterval = setInterval(() => {
            router.reload({ only: ['latestAbsences', 'todayActiveDeviceIds'] });
        }, 10000);
    });

    onDestroy(() => {
        if (pollInterval) clearInterval(pollInterval);
    });
    import { Button } from '$shadcn/components/ui/button';
    import * as InputGroup from '$shadcn/components/ui/input-group';
    import * as Card from '$shadcn/components/ui/card';
    import * as Carousel from '$shadcn/components/ui/carousel';
    import storage from '$routes/storage';
    import {
        ArrowRightCircle,
        SearchIcon,
        BatteryLow,
        BatteryMedium,
        BatteryFull,
        ImageOff,
        SortAsc,
        SortDesc
    } from '@lucide/svelte';

    import { home } from '$routes';
    import { detailPhone } from '$routes/phone';
    import Autoplay from 'embla-carousel-autoplay';
    import * as DropdownMenu from '$shadcn/components/ui/dropdown-menu';
    import Sifter from 'sifter';

    // ─── Types ──────────────────────────────────────────────────
    type DeviceType = 'phone' | 'tablet';

    type Device = {
        id: string;
        name: string;
        photo?: string;
        photos?: string[];
        battery: number;
        user: string | null;
        ram: string;
        storage: string;
        status: 'active' | 'inactive';
        registered: boolean;
        type: DeviceType;
        brand: { name: string } | null;
    };

    // ─── Props ──────────────────────────────────────────────────
    let { devices: rawDevices, latestAbsences, todayActiveDeviceIds }: { devices: PhoneListItem[]; latestAbsences: Record<string, { latest_user: string; latest_user_nik: string; latest_time: string }>; todayActiveDeviceIds: string[] } = $props();

    type PhoneListItem = {
        id: number;
        brand_id: string;
        model_id: string;
        model_name: string;
        model_type: string;
        buy_date: string;
        price: string;
        ram: string;
        storage: string;
        thumbnail: string | null;
        list_photos: string[] | null;
        registered: boolean;
        hash_token: string | null;
        created_at: string;
        updated_at: string;
        deleted_at: string | null;
        brand: { id: string; name: string } | null;
    };

    // ─── Asset URL helper ───────────────────────────────────────
    function assetUrl(path: string | null): string | null {
        if (!path) return null;
        // Strip 'storage/' prefix if present (backward compat with old stored paths)
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }

    // ─── Map PhoneList data to Device ───────────────────────────
    const todayActiveSet = $derived(new Set(todayActiveDeviceIds));

    const devices: Device[] = $derived(
        rawDevices.map((p): Device => {
            const abs = latestAbsences?.[p.model_id];
            return {
                id: p.model_id,
                name: p.model_name,
                photo: assetUrl(p.thumbnail) ?? (p.list_photos?.[0] ? assetUrl(p.list_photos[0]) : undefined) ?? undefined,
                photos: p.list_photos?.map((photo) => assetUrl(photo)).filter(Boolean) as string[] | undefined,
                battery: 0,
                user: abs?.latest_user ?? null,
                ram: p.ram,
                storage: p.storage,
                status: p.deleted_at || !todayActiveSet.has(p.model_id) ? 'inactive' : 'active',
                registered: p.registered,
                type: p.model_type.toLowerCase() as DeviceType,
                brand: p.brand ? { name: p.brand.name } : null,
            };
        }),
    );

    // ─── State ──────────────────────────────────────────────────
    type Filter = 'all' | 'active' | 'inactive' | 'registered' | 'unregistered' | 'tablet' | 'phone';
    type SortOption = 'name-asc' | 'name-desc' | 'battery-asc' | 'battery-desc';

    let search = $state('');
    let activeFilter = $state<Filter>('all');
    let sortOption = $state<SortOption>('name-asc');
    let loading = $state(false);
    let failedImages = $state<Set<string>>(new Set());
    let hoveredDeviceId = $state<string | null>(null);

    // State untuk carousel API per device
    let currentSlideIndices = $state<Record<string, number>>({});
    let carouselApis: Record<string, any> = {};

    // ─── Inisialisasi Sifter (dilakukan sekali via $derived ref) ─
    const sifter = new Sifter(devices);

    // ─── SEMUA FILTER PAKAI SIFTER ─────────────────────────────
    const filteredDevices = $derived.by(() => {
        let items = [...devices];

        // 1. Search filter (if search is not empty)
        if (search.trim()) {
            const results = sifter.search(search, {
                fields: ['name', 'id'],
                limit: 100,
                filter: true
            });
            items = results.items.map(item => devices[item.id as number]);
        }

        // 2. Category filter
        if (activeFilter !== 'all') {
            items = items.filter(device => {
                if (activeFilter === 'active') return device.status === 'active';
                if (activeFilter === 'inactive') return device.status === 'inactive';
                if (activeFilter === 'registered') return device.registered;
                if (activeFilter === 'unregistered') return !device.registered;
                if (activeFilter === 'tablet') return device.type === 'tablet';
                if (activeFilter === 'phone') return device.type === 'phone';
                return true;
            });
        }

        // 3. Sort — active devices first, then non-active
        items.sort((a, b) => {
            // Primary: active before inactive
            const aActive = a.status === 'active' ? 0 : 1;
            const bActive = b.status === 'active' ? 0 : 1;
            if (aActive !== bActive) return aActive - bActive;

            // Secondary: user-selected sort
            const [field, direction] = sortOption.split('-');
            const isAsc = direction === 'asc';

            if (field === 'name') {
                return isAsc ? a.name.localeCompare(b.name) : b.name.localeCompare(a.name);
            }
            if (field === 'battery') {
                return isAsc ? a.battery - b.battery : b.battery - a.battery;
            }
            return 0;
        });

        return items;
    });

    function countFilter(filter: Filter): number {
        if (filter === 'all') return devices.length;

        return devices.filter(device => {
            if (filter === 'active') return device.status === 'active';
            if (filter === 'inactive') return device.status === 'inactive';
            if (filter === 'registered') return device.registered;
            if (filter === 'unregistered') return !device.registered;
            if (filter === 'tablet') return device.type === 'tablet';
            if (filter === 'phone') return device.type === 'phone';
            return true;
        }).length;
    }

    // ─── Helpers ────────────────────────────────────────────────
    function batteryStyle(b: number) {
        if (b <= 20) return 'bg-destructive text-destructive-foreground';
        if (b <= 50) return 'bg-primary/85 text-primary-foreground';
        return 'bg-primary text-primary-foreground';
    }

    function batteryIcon(b: number) {
        if (b <= 20) return BatteryLow;
        if (b <= 50) return BatteryMedium;
        return BatteryFull;
    }

    function cardGlowStyle(status: Device['status']) {
        return status === 'active' ? 'box-shadow: 0 0 45px oklch(0.72 0.22 350 / 0.22);' : '';
    }

    function handleCardClick(device: Device) {
        router.visit(detailPhone({ id: device.id }).url);
    }

    function handleImageError(deviceId: string) {
        failedImages = new Set([...failedImages, deviceId]);
    }

    // ─── Carousel Functions dengan API ─────────────────────────
    function initCarousel(deviceId: string, api: any) {
        if (!api) return;
        carouselApis[deviceId] = api;
        currentSlideIndices[deviceId] = 0;

        api.on('select', () => {
            currentSlideIndices[deviceId] = api.selectedScrollSnap();
        });
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

    const sortOptions: { label: string; value: SortOption; icon: any }[] = [
        { label: 'Nama A-Z', value: 'name-asc', icon: SortAsc },
        { label: 'Nama Z-A', value: 'name-desc', icon: SortDesc },
        { label: 'Battery Tertinggi', value: 'battery-desc', icon: SortDesc },
        { label: 'Battery Terendah', value: 'battery-asc', icon: SortAsc },
    ];
</script>

<LayoutBG class="bg-mesh-pink min-h-screen">
    <Navbar>
        <div>
            <div class="text-lg font-semibold tracking-tight text-gradient-pink">DevControl</div>
            <div class="text-xs text-muted-foreground">Device Management System</div>
        </div>
        <Button
            size="sm"
            variant="ghost"
            class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
            onclick={() => router.visit(home().url)}
        >
            Kembali
            <ArrowRightCircle class="size-4" />
        </Button>
    </Navbar>

    <div class="p-6 space-y-6">
        <!-- Search + Filter -->
        <Card.Root class="overflow-hidden border border-border/60 bg-card/60 backdrop-blur-2xl shadow-2xl">
            <Card.Content class="p-5 space-y-4">
                <!-- Search Input -->
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

                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-2">
                    {#each filters as f}
                        <Button
                            size="sm"
                            variant={activeFilter === f.value ? 'default' : 'outline'}
                            onclick={() => activeFilter = f.value}
                            class="rounded-full transition-all duration-300"
                        >
                            {f.label} ({countFilter(f.value)})
                        </Button>
                    {/each}
                </div>

                <!-- Sort Dropdown -->
                <div class="flex justify-end">
                    <DropdownMenu.Root>
                        <DropdownMenu.Trigger asChild>
                            <Button variant="outline" size="sm" class="gap-2">
                                {#each sortOptions as opt}
                                    {#if opt.value === sortOption}
                                        {@const Icon = opt.icon}
                                        <Icon class="size-4" />
                                        {opt.label}
                                    {/if}
                                {/each}
                            </Button>
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Content>
                            {#each sortOptions as opt}
                                {@const Icon = opt.icon}
                                <DropdownMenu.Item onclick={() => sortOption = opt.value}>
                                    <Icon class="size-4 mr-2" />
                                    {opt.label}
                                </DropdownMenu.Item>
                            {/each}
                        </DropdownMenu.Content>
                    </DropdownMenu.Root>
                </div>
            </Card.Content>
        </Card.Root>

        <!-- Header -->
        <div class="flex items-center justify-between px-1">
            <div>
                <div class="text-2xl font-semibold tracking-tight text-card-foreground">Device Inventory</div>
                <div class="text-sm text-muted-foreground">Menampilkan {filteredDevices.length} perangkat</div>
            </div>
        </div>

        <!-- Device Grid -->
        {#if loading}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 lg:gap-8">
                {#each Array(8) as _, i}
                    <div class="overflow-hidden rounded-xl border border-border bg-card/50 animate-pulse">
                        <div class="aspect-[3/4] shimmer-pink" />
                        <div class="space-y-3 p-4">
                            <div class="h-4 w-3/4 rounded-full shimmer-pink" />
                            <div class="h-3 w-1/2 rounded-full shimmer-pink" />
                            <div class="h-12 rounded-2xl shimmer-pink" />
                        </div>
                    </div>
                {/each}
            </div>
        {:else if filteredDevices.length === 0}
            <Card.Root class="border-border/60 bg-card/50 backdrop-blur-xl">
                <Card.Content class="p-16 text-center text-muted-foreground">
                    Tidak ada perangkat yang cocok
                </Card.Content>
            </Card.Root>
        {:else}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 lg:gap-8">
                {#each filteredDevices as device (device.id)}
                    {@const isRegistered = device.registered}
                    {@const BattIcon = batteryIcon(device.battery)}
                    {@const hasCarousel = device.photos && device.photos.length > 1}
                    {@const isHovered = hoveredDeviceId === device.id}

                    <div
                        role="button"
                        tabindex="0"
                        onclick={() => handleCardClick(device)}
                        onkeydown={(e) => e.key === 'Enter' && handleCardClick(device)}
                        onmouseenter={() => hoveredDeviceId = device.id}
                        onmouseleave={() => hoveredDeviceId = null}
                        class="group overflow-hidden rounded-xl border border-border/60 bg-card/70 backdrop-blur-xl transition-all duration-500 cursor-pointer hover:-translate-y-1.5 hover:border-primary/40"
                        style={cardGlowStyle(device.status)}
                    >
                        <!-- Photo Area -->
                        <div class="relative aspect-[3/4] overflow-hidden bg-muted [&_[data-slot='carousel-content']]:h-full">
                            {#if !device.photo || failedImages.has(device.id)}
                                <div class="h-full w-full flex items-center justify-center bg-muted/50">
                                    <ImageOff class="size-8 text-muted-foreground/70" />
                                </div>
                            {:else if hasCarousel && isHovered}
                                <Carousel.Root
                                    plugins={[Autoplay({ delay: 2000, stopOnInteraction: false })]}
                                    class="w-full h-full"
                                    onapi={(e) => initCarousel(device.id, e.detail)}
                                >
                                    <Carousel.Content class="h-full">
                                        {#each device.photos! as photo, idx (idx)}
                                            <Carousel.Item class="h-full">
                                                <img src={photo} alt={device.name} class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                            </Carousel.Item>
                                        {/each}
                                    </Carousel.Content>
                                    <Carousel.Previous class="absolute left-2 top-1/2 -translate-y-1/2 size-7 bg-black/50 hover:bg-black/70 text-white border-none opacity-0 group-hover:opacity-100 transition-opacity" />
                                    <Carousel.Next class="absolute right-2 top-1/2 -translate-y-1/2 size-7 bg-black/50 hover:bg-black/70 text-white border-none opacity-0 group-hover:opacity-100 transition-opacity" />
                                </Carousel.Root>
                            {:else}
                                <img src={device.photo} alt={device.name} onerror={() => handleImageError(device.id)} class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                            {/if}


                            <!-- Battery Badge -->
                            {#if device.battery > 0}
                                <div class="absolute top-3 right-3 z-30 flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold backdrop-blur-md border border-white/10 {batteryStyle(device.battery)}">
                                    <BattIcon class="size-3" />
                                    <span>{device.battery}%</span>
                                </div>
                            {/if}

                            <!-- Status Badge -->
                        {#if !isRegistered}
                            <div class="absolute bottom-3 left-3 z-30 flex items-center gap-2 rounded-full border border-orange-500/30 bg-orange-500/15 px-3 py-1.5 text-xs font-medium backdrop-blur-lg text-orange-400">
                                <span class="size-2 rounded-full bg-current" />
                                Belum Terdaftar
                            </div>
                        {:else}
                            <div class="absolute bottom-3 left-3 z-30 flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium backdrop-blur-lg {device.status === 'active' ? 'bg-primary/90 text-primary-foreground border-primary/30' : 'bg-muted/80 text-muted-foreground border-border'}">
                                <span class="relative flex size-2 rounded-full bg-current">
                                    {#if device.status === 'active'}
                                        <span class="animate-ping absolute inline-flex size-full rounded-full bg-current opacity-75" />
                                    {/if}
                                </span>
                                {device.status === 'active' ? 'Aktif' : 'Non-Aktif'}
                            </div>
                        {/if}
                    </div>

                        <!-- Info -->
                        <div class="space-y-4 p-4">
                            <div class="space-y-2">
                                {#if device.brand}
                                    <div class="text-[11px] font-medium text-pink-500/80 uppercase tracking-wider">{device.brand.name}</div>
                                {/if}
                                <div class="line-clamp-2 text-[15px] font-semibold tracking-tight text-card-foreground">{device.name}</div>
                                <div class="inline-flex items-center rounded-full border border-border bg-muted/60 px-2.5 py-1 font-mono text-[11px] text-muted-foreground">
                                    {device.ram} / {device.storage}
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-2xl border border-border/60 bg-muted/30 p-2.5">
                                <div class="min-w-0">
                                    <div class="text-[10px] uppercase tracking-[0.18em] text-muted-foreground">User</div>
                                    <div class="truncate text-sm text-card-foreground {!device.user ? 'italic text-muted-foreground' : ''}">
                                        {device.user ?? 'Tidak ada pengguna'}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="rounded-full border px-2.5 py-1 text-[11px] font-medium {isRegistered ? 'border-primary/20 bg-primary/10 text-primary' : 'border-orange-500/20 bg-orange-500/10 text-orange-400'}">
                                    {isRegistered ? 'Terdaftar' : 'Belum'}
                                </span>
                                <span class="font-mono text-[11px] text-muted-foreground">{device.id}</span>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {/if}
    </div>
</LayoutBG>

<style>
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }

    .shimmer-pink {
        background: linear-gradient(90deg, rgba(236, 72, 153, 0.05) 0%, rgba(236, 72, 153, 0.15) 50%, rgba(236, 72, 153, 0.05) 100%);
        background-size: 1000px 100%;
        animation: shimmer 2s infinite;
    }
</style>
