<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import {
        Monitor,
        QrCode,
        ArrowRightCircle,
        ArrowLeft,
    } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { listPhone } from '$routes/phone';
    import LayoutBG from '$/components/LayoutBG.svelte';
    import SpotlightCard from '$shadcn/components/svelte-bits/SpotlightCard.svelte';
    import { deviceNotRegister, deviceRegisterQR } from '$routes/user';
    import { home } from '$routes';
</script>

<LayoutBG
    class="min-h-screen bg-background flex flex-col items-center justify-center p-6 md:p-8 gap-8 md:gap-12 relative overflow-hidden"
>
    <Particles
        particleCount={350}
        particleColors={['#000000', '#ff00ae', '#ffffff']}
        particleBaseSize={130}
        speed={0.1}
        class="absolute inset-0 z-0 opacity-50"
    />
    <!-- Back button -->
    <div class="w-full max-w-4xl pb-3 relative z-10">
        <button
            onclick={() => router.visit(routeUrl(home()))}
            class="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors group"
        >
            <ArrowLeft
                class="size-4 group-hover:-translate-x-1 transition-transform"
            />
            Kembali ke Beranda
        </button>
    </div>

    <!-- Hero section -->
    <div
        class="text-center relative z-10 space-y-4 md:space-y-6 max-w-4xl mx-auto"
        id="hero"
    >
        <div
            class="inline-flex items-center gap-2 bg-primary/10 backdrop-blur-sm border border-primary/20 text-primary text-xs font-medium px-4 py-1.5 rounded-full animate-in fade-in slide-in-from-top-5 duration-500"
        >
            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
            Device Management System
        </div>

        <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold tracking-tight">
            <span class="text-foreground">Device Register</span>
        </h1>
    </div>

    <!-- Cards -->
    <div
        class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 w-full max-w-6xl relative z-10 px-4"
    >
        <!-- Manual Register -->
        <SpotlightCard
            class="group relative bg-gradient-to-br from-primary/50 to-card/30 backdrop-blur-sm border border-border rounded-2xl p-6 md:p-8 flex flex-col items-center gap-4 text-center cursor-pointer hover:-translate-y-2 hover:border-primary/40 hover:shadow-xl hover:shadow-primary/10 transition-all duration-500"
        >
            <!-- Decorative gradient orb -->
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-primary/10 to-transparent rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            />

            <div
                class="size-16 md:size-20 rounded-2xl bg-gradient-to-br from-card/80 to-primary/5 border border-primary/30 flex items-center justify-center text-primary group-hover:scale-110 group-hover:rotate-3 transition-all duration-500"
            >
                <Monitor class="size-7 md:size-8" />
            </div>

            <div class="space-y-2">
                <div class="font-bold text-xl md:text-2xl text-foreground">
                    Daftarkan Perangkat Manual
                </div>
                <p class="text-sm text-muted-foreground max-w-xs mx-auto">
                    Input data perangkat secara langsung ke sistem dengan form
                    pendaftaran manual jika perangkat belum ada di list
                </p>
            </div>

            <div class="mt-4 w-full flex justify-center">
                <div
                    class="p-2 rounded-full bg-primary/5 group-hover:bg-primary/10 group-hover:scale-110 transition-all duration-300"
                >
                    <ArrowRightCircle
                        class="size-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all"
                    />
                </div>
            </div>
        </SpotlightCard>

        <!-- QR Register -->
        <SpotlightCard
            onclick={() => router.visit(routeUrl(deviceRegisterQR()))}
            class="group relative bg-gradient-to-br from-primary/50 to-card/30 backdrop-blur-sm border border-border rounded-2xl p-6 md:p-8 flex flex-col items-center gap-4 text-center cursor-pointer hover:-translate-y-2 hover:border-primary/40 hover:shadow-xl hover:shadow-primary/10 transition-all duration-500"
        >
            <!-- Decorative gradient orb -->
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-primary/10 to-transparent rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"
            />

            <div
                class="size-16 md:size-20 rounded-2xl bg-gradient-to-br from-card/60 to-primary/5 border border-primary/30 flex items-center justify-center text-primary group-hover:scale-110 group-hover:rotate-3 transition-all duration-500"
            >
                <QrCode class="size-7 md:size-8" />
            </div>

            <div class="space-y-2">
                <div class="font-bold text-xl md:text-2xl text-foreground">
                    Daftarkan dengan QR Code
                </div>
                <p class="text-sm text-muted-foreground max-w-xs mx-auto">
                    Scan QR code untuk pendaftaran cepat dan mudah langsung dari
                    perangkat mobile yang tersedia di web
                </p>
            </div>

            <div class="mt-4 w-full flex justify-center">
                <div
                    class="p-2 rounded-full bg-primary/5 group-hover:bg-primary/10 group-hover:scale-110 transition-all duration-300"
                >
                    <ArrowRightCircle
                        class="size-5 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all"
                    />
                </div>
            </div>
        </SpotlightCard>
    </div>

    <!-- Footer -->
    <div class="relative z-10 pt-4">
        <p class="text-xs text-muted-foreground/60">
            &copy; 2025 DevControl — Sistem Manajemen Perangkat Lapangan
        </p>
    </div>
</LayoutBG>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slide-in-from-top-5 {
        from {
            transform: translateY(-5px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-in {
        animation-duration: 0.5s;
        animation-fill-mode: both;
    }

    .fade-in {
        animation-name: fade-in;
    }

    .slide-in-from-top-5 {
        animation-name: slide-in-from-top-5;
    }

    .duration-500 {
        animation-duration: 500ms;
    }
</style>
