<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import {
        ShieldCheck,
        Smartphone,
        Users,
        Wrench,
        LogOut,
        ArrowRight,
    } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';
    import { dashboard, checkDevice, listDevice, adminList, maintenance, logout } from '$routes/admin';

    type Stat = {
        totalDevices: number;
        verifiedDevices: number;
        totalUsers: number;
        maintenanceCount: number;
    };

    let { stats }: { stats: Stat } = $props();

    const menuCards = [
        {
            icon: ShieldCheck,
            label: 'Check Device',
            description: 'Verifikasi dan periksa perangkat',
            route: checkDevice,
            color: 'from-emerald-500/20 to-emerald-600/10 border-emerald-500/30',
            iconBg: 'bg-emerald-500/20 text-emerald-400',
        },
        {
            icon: Smartphone,
            label: 'List Device',
            description: 'Daftar, tambah, dan hapus perangkat',
            route: listDevice,
            color: 'from-blue-500/20 to-blue-600/10 border-blue-500/30',
            iconBg: 'bg-blue-500/20 text-blue-400',
        },
        {
            icon: Users,
            label: 'Admin List',
            description: 'Kelola pengguna admin',
            route: adminList,
            color: 'from-violet-500/20 to-violet-600/10 border-violet-500/30',
            iconBg: 'bg-violet-500/20 text-violet-400',
        },
        {
            icon: Wrench,
            label: 'Maintenance',
            description: 'Perawatan perangkat (fisik, layar, kamera, body)',
            route: maintenance,
            color: 'from-amber-500/20 to-amber-600/10 border-amber-500/30',
            iconBg: 'bg-amber-500/20 text-amber-400',
        },
    ];

    function navigate(route: () => { url: string; method: string }) {
        router.visit(routeUrl(route()));
    }

    function handleLogout() {
        router.post(routeUrl(logout()));
    }
</script>

<div class="min-h-screen bg-background relative overflow-hidden">
    <Particles
        particleCount={200}
        particleColors={['#ff00ae', '#6366f1', '#ffffff']}
        particleBaseSize={80}
        speed={0.05}
        class="absolute inset-0 z-0 opacity-20"
    />

    <!-- Navbar -->
    <nav
        class="relative z-10 border-b border-border/60 bg-card/80 backdrop-blur-xl"
    >
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="size-10 rounded-xl bg-primary/20 flex items-center justify-center"
                >
                    <ShieldCheck class="size-5 text-primary" />
                </div>
                <div>
                    <div class="text-lg font-semibold tracking-tight">
                        Admin Panel
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Iseki DevMon
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button
                    variant="ghost"
                    size="sm"
                    onclick={handleLogout}
                    class="gap-2 text-muted-foreground hover:text-destructive"
                >
                    <LogOut class="size-4" />
                    Logout
                </Button>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="relative z-10 max-w-7xl mx-auto px-6 py-8 space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
            <p class="text-muted-foreground mt-1">
                Panel kontrol manajemen perangkat
            </p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <Card.Root
                class="border-border/60 bg-card/60 backdrop-blur-xl"
            >
                <Card.Header class="flex flex-row items-center justify-between pb-2">
                    <Card.Title class="text-sm font-medium text-muted-foreground"
                        >Total Devices</Card.Title
                    >
                    <Smartphone class="size-4 text-primary" />
                </Card.Header>
                <Card.Content>
                    <div class="text-3xl font-bold">
                        {stats.totalDevices}
                    </div>
                </Card.Content>
            </Card.Root>

            <Card.Root
                class="border-border/60 bg-card/60 backdrop-blur-xl"
            >
                <Card.Header class="flex flex-row items-center justify-between pb-2">
                    <Card.Title class="text-sm font-medium text-muted-foreground"
                        >Verified Devices</Card.Title
                    >
                    <ShieldCheck class="size-4 text-emerald-400" />
                </Card.Header>
                <Card.Content>
                    <div class="text-3xl font-bold">
                        {stats.verifiedDevices}
                    </div>
                </Card.Content>
            </Card.Root>

            <Card.Root
                class="border-border/60 bg-card/60 backdrop-blur-xl"
            >
                <Card.Header class="flex flex-row items-center justify-between pb-2">
                    <Card.Title class="text-sm font-medium text-muted-foreground"
                        >Total Users</Card.Title
                    >
                    <Users class="size-4 text-violet-400" />
                </Card.Header>
                <Card.Content>
                    <div class="text-3xl font-bold">
                        {stats.totalUsers}
                    </div>
                </Card.Content>
            </Card.Root>

            <Card.Root
                class="border-border/60 bg-card/60 backdrop-blur-xl"
            >
                <Card.Header class="flex flex-row items-center justify-between pb-2">
                    <Card.Title class="text-sm font-medium text-muted-foreground"
                        >In Maintenance</Card.Title
                    >
                    <Wrench class="size-4 text-amber-400" />
                </Card.Header>
                <Card.Content>
                    <div class="text-3xl font-bold">
                        {stats.maintenanceCount}
                    </div>
                </Card.Content>
            </Card.Root>
        </div>

        <!-- Menu Cards -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Menu</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {#each menuCards as card}
                    <!-- svelte-ignore a11y_click_events_have_key_events -->
                    <div
                        role="button"
                        tabindex="0"
                        onclick={() => navigate(card.route)}
                        onkeydown={(e) => e.key === 'Enter' && navigate(card.route)}
                        class="group cursor-pointer rounded-2xl border-2 bg-card/50 backdrop-blur-xl p-8 flex flex-col items-center gap-4 text-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl {card.color}"
                    >
                        <div
                            class="size-16 rounded-2xl flex items-center justify-center {card.iconBg} group-hover:scale-110 transition-transform duration-300"
                        >
                            <svelte:component this={card.icon} class="size-7" />
                        </div>
                        <div>
                            <div class="font-semibold text-lg">
                                {card.label}
                            </div>
                            <p class="text-xs text-muted-foreground/70 mt-1">
                                {card.description}
                            </p>
                        </div>
                        <div class="mt-auto pt-2">
                            <div
                                class="p-2 rounded-full bg-primary/5 group-hover:bg-primary/10 transition-colors"
                            >
                                <ArrowRight
                                    class="size-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all"
                                />
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </main>
</div>
