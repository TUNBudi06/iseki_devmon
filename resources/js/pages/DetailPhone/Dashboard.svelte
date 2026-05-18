<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { Button } from "$shadcn/components/ui/button";
    import { ArrowRightCircle, SearchIcon, BatteryLow, BatteryMedium, BatteryFull, User } from "@lucide/svelte";
    import * as InputGroup from "$shadcn/components/ui/input-group";
    import * as Card from "$shadcn/components/ui/card";

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
    }

    const devices: Device[] = [
        { id: 'DEV-001', name: 'Samsung Galaxy A54', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 85, user: 'Budi Santoso', ram: '8GB', storage: '256GB', status: 'active', registered: true },
        { id: 'DEV-002', name: 'Xiaomi Redmi Note 12', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 23, user: 'Siti Rahayu', ram: '6GB', storage: '128GB', status: 'active', registered: true },
        { id: 'DEV-003', name: 'OPPO A78', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 60, user: null, ram: '8GB', storage: '128GB', status: 'inactive', registered: true },
        { id: 'DEV-004', name: 'Realme C55', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 12, user: null, ram: '4GB', storage: '64GB', status: 'inactive', registered: false },
        { id: 'DEV-005', name: 'iPhone 13', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 95, user: 'Ahmad Fauzi', ram: '4GB', storage: '128GB', status: 'active', registered: true },
        { id: 'DEV-006', name: 'Vivo Y35', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 47, user: null, ram: '8GB', storage: '128GB', status: 'inactive', registered: false },
        { id: 'DEV-007', name: 'Samsung Galaxy S23', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 71, user: 'Dewi Kartika', ram: '8GB', storage: '256GB', status: 'active', registered: true },
        { id: 'DEV-008', name: 'Poco X5 Pro', photo: 'https://placehold.co/120x200/1e293b/94a3b8?text=📱', battery: 33, user: null, ram: '8GB', storage: '256GB', status: 'inactive', registered: true },
    ];

    type Filter = 'all' | 'active' | 'inactive' | 'registered' | 'unregistered';
    let activeFilter = $state<Filter>('all');
    let search = $state('');

    const filtered = $derived(devices.filter(d => {
        const matchSearch =
            d.name.toLowerCase().includes(search.toLowerCase()) ||
            d.id.toLowerCase().includes(search.toLowerCase());
        const matchFilter =
            activeFilter === 'all'          ? true :
                activeFilter === 'active'       ? d.status === 'active' :
                    activeFilter === 'inactive'     ? d.status === 'inactive' :
                        activeFilter === 'registered'   ? d.registered :
                            !d.registered;
        return matchSearch && matchFilter;
    }));

    function batteryColor(b: number) {
        if (b <= 20) return 'text-red-500';
        if (b <= 50) return 'text-yellow-500';
        return 'text-emerald-500';
    }

    function batteryBg(b: number) {
        if (b <= 20) return 'bg-red-500/10 border-red-500/20';
        if (b <= 50) return 'bg-yellow-500/10 border-yellow-500/20';
        return 'bg-emerald-500/10 border-emerald-500/20';
    }

    function batteryIcon(b: number) {
        if (b <= 20) return BatteryLow;
        if (b <= 50) return BatteryMedium;
        return BatteryFull;
    }

    const filters: { label: string; value: Filter }[] = [
        { label: 'Semua', value: 'all' },
        { label: 'Aktif', value: 'active' },
        { label: 'Non-Aktif', value: 'inactive' },
        { label: 'Terdaftar', value: 'registered' },
        { label: 'Belum Terdaftar', value: 'unregistered' },
    ];
</script>

<LayoutBG>
    <Navbar class="sticky top-0 z-50 border-b-2 border-black/10 bg-pink-400 justify-between px-6">
        <span class="text-2xl font-semibold tracking-tight text-white">DevControl</span>
        <Button size="sm" variant="ghost" class="text-white hover:bg-white/20 gap-2 font-medium">
            Kembali <ArrowRightCircle class="size-4" />
        </Button>
    </Navbar>

    <div class="p-6 space-y-4">
        <!-- Search + Filter -->
        <Card.Root class="bg-card border-2 border-border shadow-sm">
            <Card.Content class="p-4 space-y-3">
                <InputGroup.Root>
                    <InputGroup.Addon>
                        <SearchIcon class="size-4 text-muted-foreground" />
                    </InputGroup.Addon>
                    <InputGroup.Input
                        bind:value={search}
                        placeholder="Cari nama perangkat atau ID..."
                    />
                </InputGroup.Root>
                <div class="flex flex-wrap gap-2">
                    {#each filters as f}
                        <Button
                            size="sm"
                            variant={activeFilter === f.value ? 'default' : 'outline'}
                            onclick={() => activeFilter = f.value}
                        >
                            {f.label}
                        </Button>
                    {/each}
                </div>
            </Card.Content>
        </Card.Root>

        <div class="text-sm text-muted-foreground px-1">
            Menampilkan <span class="font-semibold text-foreground">{filtered.length}</span> perangkat
        </div>

        <!-- Device Grid -->
        {#if filtered.length === 0}
            <Card.Root class="bg-card border-2 border-border">
                <Card.Content class="p-12 text-center text-muted-foreground text-sm">
                    Tidak ada perangkat yang cocok
                </Card.Content>
            </Card.Root>
        {:else}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-7 gap-4">
                {#each filtered as device (device.id)}
                    <Card.Root class="bg-card border-2 border-border hover:border-pink-400/60 hover:-translate-y-1 transition-all duration-200 cursor-pointer shadow-sm group overflow-hidden">
                        <Card.Content class="p-0">
                            <!-- Photo area with battery badge -->
                            <div class="relative w-full aspect-[3/4] bg-muted overflow-hidden">
                                <img
                                    src={device.photo}
                                    alt={device.name}
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />

                                <!-- Battery badge top-right -->
                                <div class="absolute top-2 right-2 flex items-center gap-1 px-1.5 py-0.5 rounded-full border text-xs font-semibold backdrop-blur-sm {batteryBg(device.battery)} {batteryColor(device.battery)}">
                                    <svelte:component this={batteryIcon(device.battery)} class="size-3" />
                                    <span>{device.battery}%</span>
                                </div>

                                <!-- Status dot top-left -->
                                <div class="absolute top-2 left-2">
                                    <span class="flex size-2.5 rounded-full
                                        {device.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'}">
                                        {#if device.status === 'active'}
                                            <span class="animate-ping absolute inline-flex size-full rounded-full bg-emerald-400 opacity-75"></span>
                                        {/if}
                                    </span>
                                </div>
                            </div>

                            <!-- Info area -->
                            <div class="p-3 space-y-1.5">
                                <!-- Device name + specs -->
                                <div>
                                    <div class="font-semibold text-sm text-foreground leading-tight truncate">
                                        {device.name}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {device.ram} / {device.storage}
                                    </div>
                                </div>

                                <div class="h-px bg-border"></div>

                                <!-- User -->
                                <div class="flex items-center gap-1.5 text-xs">
                                    <User class="size-3 shrink-0 text-muted-foreground" />
                                    <span class="truncate {device.user ? 'text-foreground' : 'text-muted-foreground italic'}">
                                        {device.user ?? 'Tidak ada pengguna'}
                                    </span>
                                </div>

                                <!-- Registered status -->
                                <div class="flex justify-between items-center">
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium border
                                        {device.registered
                                            ? 'bg-blue-500/10 text-blue-600 border-blue-500/20'
                                            : 'bg-orange-500/10 text-orange-500 border-orange-500/20'}">
                                        {device.registered ? 'Terdaftar' : 'Belum'}
                                    </span>
                                    <span class="text-xs text-muted-foreground">{device.id}</span>
                                </div>
                            </div>
                        </Card.Content>
                    </Card.Root>
                {/each}
            </div>
        {/if}
    </div>
</LayoutBG>
