<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import {
        Monitor,
        UserCircle,
        ShieldCheck,
        ArrowRight,
    } from '@lucide/svelte';
    import * as Card from '$shadcn/components/ui/card';
    import Particles from '$shadcn/components/Particles.svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { listPhone } from '$routes/phone';
    import { MagicCard } from '$shadcn/components/magic/magic-card';
    import LayoutBG from '$/components/LayoutBG.svelte';
</script>

<LayoutBG class="flex flex-col items-center justify-center p-8 gap-10">
    <Particles particleCount={350} particleBaseSize={200} speed={0.1} class="absolute inset-0 z-0" />
    <div class="text-center relative z-10 space-y-3">
        <div
            class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 text-primary text-xs font-medium px-4 py-1.5 rounded-full"
        >
            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
            Device Management System
        </div>
        <h1 class="text-4xl font-semibold tracking-tight">
            Selamat datang di <span
                class="bg-linear-to-br bg-clip-text text-transparent from-blue-700 to-violet-900"
                >ISEKI-</span
            ><span class="text-gradient-pink">DevControl</span>
        </h1>
    </div>

    <div
        class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full max-w-7xl relative z-10"
    >
        <!-- Card 1: Semua Perangkat -->
        <MagicCard
            class="bg-card borderborder-border rounded-xl p-6 flex flex-col items-center justify-center gap-3 text-center cursor-pointer group"
        >
            <Card.Content
                onclick={() => router.visit(routeUrl(listPhone()))}
                class="flex flex-col items-center justify-center gap-3 p-0"
            >
                <div
                    class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition-colors"
                >
                    <Monitor class="size-10" />
                </div>
                <div class="font-medium text-md">Semua Perangkat</div>
                <span
                    class="text-xs bg-primary/10 text-primary px-3 py-1 rounded-full"
                    >Publik</span
                >
                <ArrowRight
                    class="size-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all mt-auto"
                />
            </Card.Content>
        </MagicCard>

        <!-- Card 2: Device List -->
        <MagicCard
            class="bg-linear-to-br from-pink-500/10 to-rose-500/10 border-pink-300/20 backdrop-blur-xl border rounded-xl p-6 flex flex-col items-center justify-center gap-3 text-center cursor-pointer group"
        >
            <Card.Content
                class="flex flex-col items-center justify-center gap-3 p-0"
            >
                <div
                    class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition-colors"
                >
                    <Monitor class="size-10" />
                </div>
                <div class="font-medium text-md">Device List</div>
                <span
                    class="text-xs bg-primary/10 text-primary px-3 py-1 rounded-full"
                    >Lihat Semua</span
                >
                <ArrowRight
                    class="size-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all mt-auto"
                />
            </Card.Content>
        </MagicCard>

        <!-- Card 3: Login Pengguna -->
        <MagicCard
            onclick={() => router.visit('/login')}
            class="bg-card border border-border rounded-xl p-6 flex flex-col items-center justify-center gap-3 text-center cursor-pointer group"
        >
            <Card.Content
                class="flex flex-col items-center justify-center gap-3 p-0"
            >
                <div
                    class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition-colors"
                >
                    <UserCircle class="size-10" />
                </div>
                <div class="font-medium text-md">Login Pengguna</div>
                <span
                    class="text-xs bg-primary/10 text-primary px-3 py-1 rounded-full"
                    >Pengguna</span
                >
                <ArrowRight
                    class="size-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all mt-auto"
                />
            </Card.Content>
        </MagicCard>
    </div>

    <p class="text-xs text-muted-foreground relative z-10">
        &copy; 2026 DevControl &mdash; Sistem Manajemen Perangkat Lapangan
    </p>
</LayoutBG>
