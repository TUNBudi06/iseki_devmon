<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Monitor, Search, BatteryFull, BatteryMedium, BatteryLow, ChevronRight, ShieldCheck, ShieldX, SmartphoneNfc } from '@lucide/svelte';

    type Device = {
        id: string;
        device_id: string;
        name: string;
        battery_health: number;
        status: 'active' | 'inactive';
        approved: boolean;
        photos: string[];
        user?: { nama: string; nik: string };
    };

    const { devices = [] }: { devices: Device[] } = $props();

    // --- dummy data, nanti hapus kalau udah dari Laravel ---
    const dummyDevices: Device[] = [
        { id:'1', device_id:'THE56ZKN', name:'ThinkPad E14',   battery_health:92, status:'active',   approved:true,  photos:['https://placehold.co/400x300'], user:{nama:'Budi Santoso',nik:'3201234567890001'} },
        { id:'2', device_id:'AND12XKP', name:'Samsung A54',    battery_health:78, status:'active',   approved:true,  photos:['https://placehold.co/400x300'], user:{nama:'Siti Rahayu',nik:'3274567890123456'} },
        { id:'3', device_id:'IPH99ZQA', name:'iPhone 13',      battery_health:55, status:'inactive', approved:false, photos:[] },
        { id:'4', device_id:'LNV34MNB', name:'Lenovo Tab P11', battery_health:88, status:'active',   approved:true,  photos:['https://placehold.co/400x300'], user:{nama:'Ahmad Fauzi',nik:'3578901234567890'} },
        { id:'5', device_id:'HPX22KZT', name:'HP EliteBook',   battery_health:40, status:'inactive', approved:false, photos:[] },
        { id:'6', device_id:'SAM45PLK', name:'Galaxy Tab S8',  battery_health:95, status:'active',   approved:true,  photos:['https://placehold.co/400x300'], user:{nama:'Dewi Lestari',nik:'3201987654321001'} },
        { id:'7', device_id:'OPP77YTR', name:'OPPO Reno 8',    battery_health:62, status:'inactive', approved:false, photos:[] },
        { id:'8', device_id:'RLM23TQZ', name:'Realme GT Neo',  battery_health:85, status:'active',   approved:true,  photos:['https://placehold.co/400x300'] },
        { id:'9', device_id:'DEL88WQM', name:'Dell Latitude',  battery_health:30, status:'inactive', approved:false, photos:[] },
        { id:'10',device_id:'ASU11PKQ', name:'ASUS Vivobook',  battery_health:91, status:'active',   approved:true,  photos:['https://placehold.co/400x300'] },
        { id:'11',device_id:'XIA66NJK', name:'Xiaomi 12T',     battery_health:70, status:'active',   approved:true,  photos:['https://placehold.co/400x300'] },
        { id:'12',device_id:'NOK77TRQ', name:'Nokia G60',      battery_health:45, status:'inactive', approved:false, photos:[] },
    ];

    const allDevices = devices.length > 0 ? devices : dummyDevices;

    type Filter = 'all' | 'active' | 'inactive' | 'approved' | 'pending';

    let search = $state('');
    let activeFilter = $state<Filter>('all');

    const filters: { label: string; value: Filter }[] = [
        { label: 'Semua',              value: 'all' },
        { label: 'Aktif',             value: 'active' },
        { label: 'Nonaktif',          value: 'inactive' },
        { label: 'Terverifikasi',     value: 'approved' },
        { label: 'Belum diverifikasi',value: 'pending' },
    ];

    let filtered = $derived(
        allDevices.filter((d) => {
            const q = search.toLowerCase();
            const matchQ = d.name.toLowerCase().includes(q) || d.device_id.toLowerCase().includes(q);
            const matchF =
                activeFilter === 'all' ||
                (activeFilter === 'active'   && d.status === 'active') ||
                (activeFilter === 'inactive' && d.status === 'inactive') ||
                (activeFilter === 'approved' && d.approved) ||
                (activeFilter === 'pending'  && !d.approved);
            return matchQ && matchF;
        })
    );

    function handleClick(device: Device) {
        if (!device.approved) return;
        router.visit(`/devices/${device.id}`);
    }
</script>

<div class="min-h-screen bg-background">

    <!-- Top bar -->
    <div class="sticky top-0 z-10 bg-background/95 backdrop-blur border-b border-border px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center gap-4 flex-wrap">
            <h1 class="text-lg font-semibold shrink-0">
                Dev<span class="text-primary">Control</span>
                <span class="text-muted-foreground font-normal text-sm ml-1">— Semua Perangkat</span>
            </h1>

            <!-- Search -->
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <input
                    bind:value={search}
                    type="text"
                    placeholder="Cari nama atau ID perangkat..."
                    class="w-full pl-9 pr-4 py-2 text-sm bg-muted border border-border rounded-full outline-none focus:border-primary transition-colors"
                />
            </div>

            <span class="text-sm text-muted-foreground ml-auto shrink-0">
                {filtered.length} perangkat
            </span>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6 space-y-6">

        <!-- Filter pills -->
        <div class="flex gap-2 flex-wrap">
            {#each filters as f}
                <button
                    onclick={() => activeFilter = f.value}
                    class="text-xs px-4 py-1.5 rounded-full border transition-colors
                        {activeFilter === f.value
                            ? 'bg-primary/10 border-primary/30 text-primary font-medium'
                            : 'bg-background border-border text-muted-foreground hover:border-primary/30 hover:text-foreground'}"
                >
                    {f.label}
                </button>
            {/each}
        </div>

        <!-- Grid -->
        {#if filtered.length === 0}
            <div class="flex flex-col items-center justify-center py-24 text-muted-foreground gap-3">
                <SmartphoneNfc class="size-12 opacity-30" />
                <p class="text-sm">Tidak ada perangkat yang cocok</p>
            </div>
        {:else}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                {#each filtered as device (device.id)}
                    <div
                        onclick={() => handleClick(device)}
                        role="button"
                        tabindex="0"
                        class="group bg-card border border-border rounded-xl overflow-hidden transition-all duration-200
                            {device.approved
                                ? 'cursor-pointer hover:border-primary/40 hover:-translate-y-1 hover:shadow-sm'
                                : 'cursor-not-allowed opacity-60'}"
                    >
                        <!-- Image / Icon area -->
                        <div class="aspect-square bg-muted relative flex items-center justify-center">
                            {#if device.photos.length > 0}
                                <img
                                    src={device.photos[0]}
                                    alt={device.name}
                                    class="w-full h-full object-cover"
                                />
                            {:else}
                                <Monitor class="size-10 text-muted-foreground/40" />
                            {/if}

                            <!-- Approved badge -->
                            <span class="absolute top-2 left-2 text-xs px-2 py-0.5 rounded-full font-medium
                                {device.approved
                                    ? 'bg-green-50 text-green-700 dark:bg-green-950/50 dark:text-green-400'
                                    : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400'}">
                                {device.approved ? 'Verified' : 'Pending'}
                            </span>

                            <!-- Status dot -->
                            <span class="absolute top-2 right-2 size-2 rounded-full
                                {device.status === 'active' ? 'bg-green-500' : 'bg-zinc-400'}">
                            </span>
                        </div>

                        <!-- Body -->
                        <div class="p-3">
                            <p class="text-sm font-medium truncate">{device.name}</p>
                            <p class="text-xs text-muted-foreground font-mono mt-0.5 mb-2">{device.device_id}</p>

                            <div class="flex items-center justify-between">
                                <!-- Battery -->
                                <div class="flex items-center gap-1 text-xs
                                    {device.battery_health >= 80
                                        ? 'text-green-600 dark:text-green-400'
                                        : device.battery_health >= 50
                                          ? 'text-amber-600 dark:text-amber-400'
                                          : 'text-destructive'}">
                                    {#if device.battery_health >= 80}
                                        <BatteryFull class="size-3.5" />
                                    {:else if device.battery_health >= 50}
                                        <BatteryMedium class="size-3.5" />
                                    {:else}
                                        <BatteryLow class="size-3.5" />
                                    {/if}
                                    {device.battery_health}%
                                </div>

                                <ChevronRight class="size-3.5 text-muted-foreground group-hover:text-primary transition-colors" />
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        {/if}
    </div>
</div>
