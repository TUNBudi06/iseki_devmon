<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Monitor, UserCircle, ShieldCheck, ArrowRight } from '@lucide/svelte';
    import Particles from "$shadcn/components/Particles.svelte";
    import {routeUrl} from "@tunbudi06/inertia-route-helper";
    import {listPhone} from "$routes/phone";
</script>

<div class="min-h-screen bg-background flex flex-col items-center justify-center p-8 gap-10 relative overflow-hidden">
    <Particles particleCount={350} particleBaseSize={130} speed={0.1} class="absolute inset-0 z-0" />
    <!-- Blob dekorasi -->
    <div class="absolute -top-52 -left-40 w-150 h-150 rounded-full bg-gradient-pink opacity-10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-52 -right-32 w-125 h-125 rounded-full bg-gradient-pink opacity-8 blur-3xl pointer-events-none"></div>

    <!-- Hero text -->
    <div class="text-center relative z-10 space-y-3">
        <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 text-primary text-xs font-medium px-4 py-1.5 rounded-full">
            <span class="size-1.5 rounded-full bg-primary animate-pulse"></span>
            Device Management System
        </div>
        <h1 class="text-4xl font-semibold tracking-tight">
            Selamat datang di <span class="bg-linear-to-br bg-clip-text text-transparent from-blue-700 to-violet-900">ISEKI-</span><span class="text-gradient-pink">DevControl</span>
        </h1>
        <p class="text-muted-foreground text-sm max-w-md mx-auto">
            Platform terpadu untuk memantau, mengelola, dan mengaktivasi perangkat lapangan secara realtime.
        </p>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-3xl relative z-10">

        <!-- Device List -->
        <button
            onclick={() => router.visit(routeUrl(listPhone()))}
            class="group bg-card border border-border rounded-xl p-6 flex flex-col items-center gap-3 text-center cursor-pointer hover:-translate-y-1 hover:border-primary/40 hover:shadow-sm transition-all duration-200"
        >
            <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition-colors">
                <Monitor class="size-5" />
            </div>
            <div class="space-y-1">
                <div class="font-medium text-sm">Semua Perangkat</div>
                <div class="text-xs text-muted-foreground leading-relaxed">
                    Lihat detail, status, dan riwayat seluruh device yang terdaftar
                </div>
            </div>
            <span class="text-xs bg-primary/10 text-primary px-3 py-1 rounded-full">Publik</span>
            <ArrowRight class="size-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all mt-auto" />
        </button>

        <!-- User Login -->
        <button
            onclick={() => router.visit('/login')}
            class="group bg-card border border-border rounded-xl p-6 flex flex-col items-center gap-3 text-center cursor-pointer hover:-translate-y-1 hover:border-primary/40 hover:shadow-sm transition-all duration-200"
        >
            <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition-colors">
                <UserCircle class="size-5" />
            </div>
            <div class="space-y-1">
                <div class="font-medium text-sm">Login Pengguna</div>
                <div class="text-xs text-muted-foreground leading-relaxed">
                    Masuk sebagai pengguna untuk menggunakan perangkat terdaftar
                </div>
            </div>
            <span class="text-xs bg-primary/10 text-primary px-3 py-1 rounded-full">Pengguna</span>
            <ArrowRight class="size-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all mt-auto" />
        </button>

        <!-- Admin Login -->
        <button
            onclick={() => router.visit('/admin/login')}
            class="group bg-card border border-border rounded-xl p-6 flex flex-col items-center gap-3 text-center cursor-pointer hover:-translate-y-1 hover:border-primary/40 hover:shadow-sm transition-all duration-200"
        >
            <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary/15 transition-colors">
                <ShieldCheck class="size-5" />
            </div>
            <div class="space-y-1">
                <div class="font-medium text-sm">Login Admin</div>
                <div class="text-xs text-muted-foreground leading-relaxed">
                    Akses panel administrasi untuk verifikasi dan kelola perangkat
                </div>
            </div>
            <span class="text-xs bg-primary/10 text-primary px-3 py-1 rounded-full">Admin</span>
            <ArrowRight class="size-4 text-muted-foreground group-hover:text-primary group-hover:translate-x-1 transition-all mt-auto" />
        </button>

    </div>

    <!-- Footer -->
    <p class="text-xs text-muted-foreground relative z-10">
        &copy; 2025 DevControl &mdash; Sistem Manajemen Perangkat Lapangan
    </p>

</div>
