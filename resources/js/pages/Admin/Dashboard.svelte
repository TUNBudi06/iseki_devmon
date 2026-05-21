<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Separator } from '$shadcn/components/ui/separator';
    import {
        ShieldCheck,
        Smartphone,
        Users,
        Wrench,
        LogOut,
        ArrowRight,
        Layers,
        CheckCircle2,
        UserCheck,
        AlertTriangle,
        Activity,
} from '@lucide/svelte';
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
            gradient: 'from-emerald-500 to-emerald-600',
            gradientLight: 'from-emerald-500/20 to-emerald-600/10',
            border: 'border-emerald-500/30',
            iconBg: 'bg-emerald-500/20 text-emerald-400',
            iconHover: 'group-hover:bg-emerald-500/30',
            glow: 'shadow-emerald-500/20',
            badge: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-300',
        },
        {
            icon: Smartphone,
            label: 'List Device',
            description: 'Daftar, tambah, dan hapus perangkat',
            route: listDevice,
            gradient: 'from-pink-500 to-pink-600',
            gradientLight: 'from-pink-500/20 to-pink-600/10',
            border: 'border-pink-500/30',
            iconBg: 'bg-pink-500/20 text-pink-400',
            iconHover: 'group-hover:bg-pink-500/30',
            glow: 'shadow-pink-500/20',
            badge: 'bg-pink-500/15 text-pink-600 dark:text-pink-300',
        },
        {
            icon: Users,
            label: 'Admin List',
            description: 'Kelola pengguna admin',
            route: adminList,
            gradient: 'from-violet-500 to-violet-600',
            gradientLight: 'from-violet-500/20 to-violet-600/10',
            border: 'border-violet-500/30',
            iconBg: 'bg-violet-500/20 text-violet-400',
            iconHover: 'group-hover:bg-violet-500/30',
            glow: 'shadow-violet-500/20',
            badge: 'bg-violet-500/15 text-violet-600 dark:text-violet-300',
        },
        {
            icon: Wrench,
            label: 'Maintenance',
            description: 'Perawatan perangkat (fisik, layar, kamera, body)',
            route: maintenance,
            gradient: 'from-amber-500 to-amber-600',
            gradientLight: 'from-amber-500/20 to-amber-600/10',
            border: 'border-amber-500/30',
            iconBg: 'bg-amber-500/20 text-amber-400',
            iconHover: 'group-hover:bg-amber-500/30',
            glow: 'shadow-amber-500/20',
            badge: 'bg-amber-500/15 text-amber-600 dark:text-amber-300',
        },
    ] as const;

    const overviewCards = [
        {
            label: 'Total Devices',
            value: stats.totalDevices,
            icon: Layers,
            gradient: 'from-pink-500/20 to-pink-600/5',
            iconBg: 'bg-pink-500/20 text-pink-400',
            border: 'border-pink-500/20',
            badge: 'Semua perangkat',
        },
        {
            label: 'Verified Devices',
            value: stats.verifiedDevices,
            icon: CheckCircle2,
            gradient: 'from-emerald-500/20 to-emerald-600/5',
            iconBg: 'bg-emerald-500/20 text-emerald-400',
            border: 'border-emerald-500/20',
            badge: 'Terverifikasi',
        },
        {
            label: 'Total Users',
            value: stats.totalUsers,
            icon: UserCheck,
            gradient: 'from-violet-500/20 to-violet-600/5',
            iconBg: 'bg-violet-500/20 text-violet-400',
            border: 'border-violet-500/20',
            badge: 'Pengguna terdaftar',
        },
        {
            label: 'In Maintenance',
            value: stats.maintenanceCount,
            icon: AlertTriangle,
            gradient: 'from-amber-500/20 to-amber-600/5',
            iconBg: 'bg-amber-500/20 text-amber-400',
            border: 'border-amber-500/20',
            badge: stats.maintenanceCount === 0 ? 'Tidak ada' : 'Butuh perhatian',
        },
    ] as const;

    function navigate(route: () => { url: string; method: string }) {
        router.visit(routeUrl(route()));
    }

    function handleLogout() {
        router.post(routeUrl(logout()));
    }
</script>

<div class="min-h-screen bg-mesh-pink">
    <!-- ──────── Decorative floating orbs ──────── -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-pink-500/10 blur-3xl animate-float" />
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-violet-500/8 blur-3xl animate-float delay-300" />
        <div class="absolute top-1/3 left-1/4 w-64 h-64 rounded-full bg-amber-500/5 blur-3xl animate-float delay-150" />
    </div>

    <!-- ──────── Navbar ──────── -->
    <nav class="relative z-10 border-b border-border/60 bg-card/70 backdrop-blur-xl">
        <div class="px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                    <ShieldCheck class="size-5 text-pink-400" />
                </div>
                <div>
                    <div class="text-lg font-semibold tracking-tight">Admin Panel</div>
                    <div class="text-xs text-muted-foreground">Iseki DevMon</div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <Badge variant="outline" class="hidden sm:inline-flex gap-1.5 bg-pink-500/8 border-pink-300/30 text-pink-600 dark:text-pink-300 text-xs">
                    <Activity class="size-3" />
                    Online
                </Badge>
                <Button
                    variant="ghost"
                    size="sm"
                    onclick={handleLogout}
                    class="gap-2 text-muted-foreground hover:text-red-400 hover:bg-red-500/10 transition-colors"
                >
                    <LogOut class="size-4" />
                    Logout
                </Button>
            </div>
        </div>
    </nav>

    <!-- ──────── Content ──────── -->
    <main class="relative z-10 px-6 py-8 space-y-8 animate-fade-up">
        <!-- Header Section -->
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                        <Activity class="size-5 text-pink-400" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                        <p class="text-muted-foreground text-sm mt-0.5">
                            Panel kontrol manajemen perangkat
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-muted-foreground bg-card/60 border border-border/50 rounded-lg px-3 py-2">
                <div class="size-2 rounded-full bg-emerald-400 pulse-pink" />
                Sistem aktif
            </div>
        </div>

        <!-- ──────── Stat Overview Cards ──────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {#each overviewCards as card, i}
                <Card.Root
                    class="relative overflow-hidden border {card.border} bg-card/70 backdrop-blur-xl shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5 {card.gradient} animate-fade-up"
                    style="animation-delay: {i * 75}ms"
                >
                    <!-- Decorative gradient bar on top -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {card.gradient}" />

                    <Card.Header class="flex flex-row items-center justify-between pb-2">
                        <Card.Title class="text-sm font-medium text-muted-foreground tracking-wide uppercase">
                            {card.label}
                        </Card.Title>
                        <div class="size-9 rounded-lg {card.iconBg} flex items-center justify-center">
                            <card.icon class="size-4" />
                        </div>
                    </Card.Header>
                    <Card.Content>
                        <div class="text-4xl font-bold tracking-tight">{card.value}</div>
                        <p class="text-xs text-muted-foreground/70 mt-1.5 flex items-center gap-1">
                            <span class="size-1.5 rounded-full bg-current opacity-40" />
                            {card.badge}
                        </p>
                    </Card.Content>
                </Card.Root>
            {/each}
        </div>

        <!-- Divider -->
        <div class="flex items-center gap-4">
            <Separator class="flex-1 opacity-30" />
            <span class="text-xs font-medium text-muted-foreground/50 uppercase tracking-widest">Navigasi Cepat</span>
            <Separator class="flex-1 opacity-30" />
        </div>

        <!-- ──────── Menu Cards ──────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {#each menuCards as card, i}
                <!-- svelte-ignore a11y_click_events_have_key_events -->
                <div
                    role="button"
                    tabindex="0"
                    onclick={() => navigate(card.route)}
                    onkeydown={(e) => e.key === 'Enter' && navigate(card.route)}
                    class="group relative cursor-pointer rounded-2xl border-2 {card.border} bg-card/70 backdrop-blur-xl p-7 flex flex-col items-center gap-4 text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl {card.glow} animate-fade-up overflow-hidden"
                    style="animation-delay: {i * 100}ms"
                >
                    <!-- Hover gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-b {card.gradientLight} opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                    <!-- Corner accent -->
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl {card.gradientLight} rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                    <div class="relative z-10 flex flex-col items-center gap-4">
                        <!-- Icon -->
                        <div class="size-16 rounded-2xl flex items-center justify-center {card.iconBg} {card.iconHover} group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                            <card.icon class="size-7" />
                        </div>

                        <!-- Label & Description -->
                        <div>
                            <div class="font-semibold text-lg">{card.label}</div>
                            <p class="text-xs text-muted-foreground/70 mt-1 leading-relaxed">
                                {card.description}
                            </p>
                        </div>

                        <!-- Action indicator -->
                        <div class="mt-auto pt-2 relative z-10">
                            <div class="p-2.5 rounded-full bg-muted/30 group-hover:bg-background/40 transition-colors duration-300 ring-1 ring-border/30 group-hover:ring-0">
                                <ArrowRight class="size-5 text-muted-foreground group-hover:text-foreground group-hover:translate-x-1.5 group-hover:-translate-y-0.5 transition-all duration-300" />
                            </div>
                        </div>
                    </div>
                </div>
            {/each}
        </div>

        <!-- ──────── Quick Stats Footer ──────── -->
        <div class="rounded-2xl border border-border/50 bg-card/50 backdrop-blur-xl p-6 animate-fade-up delay-375">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-pink-400">{stats.totalDevices}</div>
                    <p class="text-xs text-muted-foreground mt-1">Brand Terdaftar</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-400">{stats.verifiedDevices}</div>
                    <p class="text-xs text-muted-foreground mt-1">Perangkat Aktif</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-violet-400">{stats.totalUsers}</div>
                    <p class="text-xs text-muted-foreground mt-1">Pengguna</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-amber-400">{stats.maintenanceCount}</div>
                    <p class="text-xs text-muted-foreground mt-1">Dalam Perbaikan</p>
                </div>
            </div>
        </div>
    </main>
</div>
