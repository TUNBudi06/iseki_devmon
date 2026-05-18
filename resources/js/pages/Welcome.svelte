<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Monitor, UserCircle, ShieldCheck, ArrowRight } from '@lucide/svelte';
    import Particles from "$shadcn/components/Particles.svelte";
    import {routeUrl} from "@tunbudi06/inertia-route-helper";
    import {listPhone} from "$routes/phone";
    import LayoutBG from "$/components/LayoutBG.svelte";
    import SpotlightCard from "$shadcn/components/svelte-bits/SpotlightCard.svelte";
</script>

<LayoutBG class="min-h-screen bg-background flex flex-col items-center justify-center p-8 gap-10 relative overflow-hidden">
    <Particles particleCount={350} particleColors={['#000000','#ff00ae','#ffffff']} particleBaseSize={130} speed={0.1} class="absolute inset-0 z-0" />

    <!-- Hero text -->
    <div class="text-center relative z-10 space-y-3">
        <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 text-primary text-xs font-medium px-4 py-1.5 rounded-full">
            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
            Device Management System
        </div>
        <h1 class="text-4xl font-semibold tracking-tight">
            Selamat datang di
            <span class="bg-linear-to-br bg-clip-text text-transparent from-blue-700 to-violet-900">ISEKI-</span><span class="text-gradient-pink">DevControl</span>
        </h1>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 w-full max-w-7xl relative z-10">

        <!-- Semua Perangkat -->
        <SpotlightCard
            onclick={() => router.visit(routeUrl(listPhone()))}
            class="group bg-pink-300/30 backdrop-blur-sm border-2 border-border rounded-2xl p-8 flex flex-col items-center gap-4 text-center cursor-pointer hover:-translate-y-2 hover:border-primary/50 hover:shadow-2xl hover:shadow-primary/20 transition-all duration-300"
        >
            <div class="size-16 rounded-2xl bg-primary/10  border border-primary/20 flex items-center justify-center text-primary group-hover:bg-primary/20 group-hover:scale-110 transition-all duration-300">
                <Monitor class="size-7" />
            </div>
            <div class="space-y-1">
                <div class="font-semibold text-lg text-foreground">Semua Perangkat</div>
            </div>
            <span class="text-xs font-medium bg-primary/10 text-primary border border-primary/20 px-4 py-1 rounded-full">Publik</span>
            <div class="mt-auto pt-4 w-full flex justify-center">
                <div class="p-2 rounded-full bg-primary/5 group-hover:bg-primary/10 transition-colors">
                    <ArrowRight class="size-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all" />
                </div>
            </div>
        </SpotlightCard>

        <!-- User Login -->
        <SpotlightCard
            onclick={() => router.visit('/login')}
            class="group bg-pink-300/30 backdrop-blur-sm border-2 border-border rounded-2xl p-8 flex flex-col items-center gap-4 text-center cursor-pointer hover:-translate-y-2 hover:border-primary/50 hover:shadow-2xl hover:shadow-primary/20 transition-all duration-300"
        >
            <div class="size-16 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary group-hover:bg-primary/20 group-hover:scale-110 transition-all duration-300">
                <UserCircle class="size-7" />
            </div>
            <div class="space-y-1">
                <div class="font-semibold text-lg text-foreground">Login Pengguna</div>
            </div>
            <span class="text-xs font-medium bg-primary/10 text-primary border border-primary/20 px-4 py-1 rounded-full">Pengguna</span>
            <div class="mt-auto pt-4 w-full flex justify-center">
                <div class="p-2 rounded-full bg-primary/5 group-hover:bg-primary/10 transition-colors">
                    <ArrowRight class="size-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all" />
                </div>
            </div>
        </SpotlightCard>

        <!-- Admin Login -->
        <SpotlightCard
            onclick={() => router.visit('/admin/login')}
            class="group bg-pink-300/30 backdrop-blur-sm border-2 border-border rounded-2xl p-8 flex flex-col items-center gap-4 text-center cursor-pointer hover:-translate-y-2 hover:border-primary/50 hover:shadow-2xl hover:shadow-primary/20 transition-all duration-300"
        >
            <div class="size-16 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary group-hover:bg-pink-500/40 group-hover:scale-110 transition-all duration-300">
                <ShieldCheck class="size-7" />
            </div>
            <div class="space-y-1">
                <div class="font-semibold text-lg text-foreground">Login Admin</div>
            </div>
            <span class="text-xs font-medium bg-primary/10 text-primary border border-primary/20 px-4 py-1 rounded-full">Admin</span>
            <div class="mt-auto pt-4 w-full flex justify-center">
                <div class="p-2 rounded-full bg-primary/5 group-hover:bg-primary/10 transition-colors">
                    <ArrowRight class="size-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all" />
                </div>
            </div>
        </SpotlightCard>

    </div>

    <p class="text-xs text-muted-foreground relative z-10">
        &copy; 2025 DevControl &mdash; Sistem Manajemen Perangkat Lapangan
    </p>
</LayoutBG>
