<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import {
        Monitor,
        UserCircle,
        ShieldCheck,
        ArrowRight,
        Loader2,
    } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { listPhone } from '$routes/phone';
    import LayoutBG from '$/components/LayoutBG.svelte';
    import SpotlightCard from '$shadcn/components/svelte-bits/SpotlightCard.svelte';
    import { deviceNotRegister } from '$routes/user';
    import { loginAdmin } from '$routes/admin';

    let navigating = $state<string | null>(null);

    function visit(path: string, label: string) {
        navigating = label;
        router.visit(path, {
            onFinish: () => {
                navigating = null;
            },
        });
    }

    const year = new Date().getFullYear();

    type CardEntry = {
        icon: typeof Monitor;
        label: string;
        description: string;
        badge: string;
        route: string;
        routeLabel: string;
        accentClass: string;
    };

    const cards: CardEntry[] = [
        {
            icon: Monitor,
            label: 'Semua Perangkat',
            description: 'Lihat seluruh daftar perangkat',
            badge: 'Publik',
            route: routeUrl(listPhone()),
            routeLabel: 'listPhone',
            accentClass: 'from-card to-blue-500/20 hover:border-blue-500/40',
        },
        {
            icon: UserCircle,
            label: 'Daily Absence Checks',
            description: 'Cek absensi harian pengguna',
            badge: 'Pengguna',
            route: routeUrl(deviceNotRegister()),
            routeLabel: 'deviceNotRegister',
            accentClass:
                'from-card to-emerald-520/10 hover:border-emerald-500/40',
        },
        {
            icon: ShieldCheck,
            label: 'Device Control Sheet',
            description: 'Kelola dan kendalikan perangkat',
            badge: 'Admin',
            route: routeUrl(loginAdmin()),
            routeLabel: 'loginAdmin',
            accentClass: 'from-card to-amber-500210 hover:border-amber-500/40',
        },
    ];
</script>

<LayoutBG
    class="min-h-screen bg-background flex flex-col items-center justify-center p-8 gap-10 relative overflow-hidden"
>
    <Particles
        particleCount={350}
        particleColors={['#000000', '#ff00ae', '#ffffff']}
        particleBaseSize={130}
        speed={0.1}
        class="absolute inset-0 z-0"
    />

    <!-- Hero text -->
    <div class="text-center relative z-10 space-y-3">
        <div
            class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 text-primary text-xs font-medium px-4 py-1.5 rounded-full"
        >
            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
            Device Management System
        </div>
        <h1 class="text-4xl font-semibold tracking-tight">
            Selamat datang di
            <span class="text-gradient-pink">ISEKI-DevControl</span>
        </h1>
    </div>

    <!-- Cards -->
    <div
        class="grid grid-cols-1 sm:grid-cols-3 gap-6 w-full max-w-7xl relative z-10"
    >
        {#each cards as card}
            <!-- svelte-ignore a11y_click_events_have_key_events -->
            <SpotlightCard
                onclick={() => visit(card.route, card.routeLabel)}
                class="group bg-primary/20 backdrop-blur-sm border-2 border-border rounded-2xl p-8 flex flex-col items-center gap-4 text-center cursor-pointer hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/20 transition-all duration-300 {card.accentClass}"
            >
                <div
                    class="size-16 rounded-2xl bg-gradient-to-br {card.accentClass.split(
                        ' ',
                    )[0]} border border-primary/20 flex items-center justify-center text-primary group-hover:bg-primary/20 group-hover:scale-110 transition-all duration-300"
                >
                    <svelte:component this={card.icon} class="size-7" />
                </div>
                <div class="space-y-1">
                    <div class="font-semibold text-lg text-foreground">
                        {card.label}
                    </div>
                    <p class="text-xs text-muted-foreground/70 max-w-48">
                        {card.description}
                    </p>
                </div>
                <span
                    class="text-xs font-medium bg-primary/10 text-primary border border-primary/20 px-4 py-1 rounded-full"
                    >{card.badge}</span
                >
                <div class="mt-auto pt-4 w-full flex justify-center">
                    <div
                        class="p-2 rounded-full bg-primary/5 group-hover:bg-primary/10 transition-colors"
                    >
                        {#if navigating === card.routeLabel}
                            <Loader2 class="size-5 text-primary animate-spin" />
                        {:else}
                            <ArrowRight
                                class="size-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all"
                            />
                        {/if}
                    </div>
                </div>
            </SpotlightCard>
        {/each}
    </div>

    <p class="text-xs text-muted-foreground relative z-10">
        &copy; {year} DevControl &mdash; Sistem Manajemen Perangkat Lapangan
    </p>
</LayoutBG>
