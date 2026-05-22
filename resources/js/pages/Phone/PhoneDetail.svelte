<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { router } from '@inertiajs/svelte';
    import { home } from '$routes';
    import { Button } from '$shadcn/components/ui/button';
    import storage from '$routes/storage';
    import {
        ArrowRightCircle,
        LucideHome,
        Cpu,
        HardDrive,
        Tag,
        CalendarDays,
        ClipboardList,
        CheckCircle2,
        XCircle,
        User,
        CreditCard,
        LogIn,
        LogOut,
        FileText,
    } from '@lucide/svelte';
    import { listPhone } from '$routes/phone';
    import * as Carousel from '$shadcn/components/ui/carousel';
    import { onMount, onDestroy } from 'svelte';
    import gsap from 'gsap';
    import Lenis from 'lenis';
    import ScrollTrigger from 'gsap/ScrollTrigger';
    import Autoplay from 'embla-carousel-autoplay';

    // ─── Helpers ────────────────────────────────────────────────
    function assetUrl(path: string | null): string | null {
        if (!path) return null;
        // Strip 'storage/' prefix if present (backward compat with old stored paths)
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }

    function formatPrice(val: string): string {
        const num = parseInt(val.replace(/\D/g, ''), 10);
        if (isNaN(num)) return val;
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
    }

    // ─── Props ──────────────────────────────────────────────────
    let { phone: raw }: { phone: PhoneType } = $props();

    type PhoneType = {
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

    // ─── Map to display data ────────────────────────────────────
    const phone = {
        id: raw.model_id,
        name: raw.model_name,
        price: raw.price,
        ram: raw.ram,
        storage: raw.storage,
        color: '',
        photos: raw.list_photos?.map((p) => assetUrl(p)).filter(Boolean) as string[] ?? [],
        buy_date: raw.buy_date,
        checks: [] as { date: string; user: string; nik: string; status: string; note: string }[],
        usages: [] as { name: string; nik: string; login: string; note: string }[],
    };

    // ── STATE with runes ──
    let lenis: Lenis | null = $state(null);
    let stickyRef = $state<HTMLDivElement | null>(null);
    let isMobile = $state(false);

    // Desktop: sticky progress
    let stickyProgress = $state(0);

    // Mobile: threshold-based visibility
    let mobileNavVisible = $state(true);

    // ScrollTrigger instances untuk cleanup
    let stMobile: ScrollTrigger | null = $state(null);
    let stDesktop: ScrollTrigger | null = $state(null);

    // Computed values with $derived
    const desktopNavHidden = $derived(
        !isMobile && stickyProgress > 0.05 && stickyProgress < 0.95,
    );

    const navTransform = $derived(() => {
        if (isMobile) {
            return mobileNavVisible ? 'translateY(-100%)' : 'translateY(0)';
        }
        return desktopNavHidden ? 'translateY(-100%)' : 'translateY(0)';
    });

    gsap.registerPlugin(ScrollTrigger);

    // ── HELPERS ──
    function setupMobileTrigger() {
        if (stMobile) stMobile.kill();

        stMobile = ScrollTrigger.create({
            trigger: document.body,
            start: 'top top',
            end: 'bottom bottom',
            onUpdate: (self) => {
                // Hide navbar after scrolling past 150px
                mobileNavVisible = self.scroll() < 300;
            },
        });
    }

    function setupDesktopTrigger() {
        if (stDesktop) stDesktop.kill();
        if (!stickyRef) return;

        stDesktop = ScrollTrigger.create({
            trigger: stickyRef,
            start: 'top top',
            end: 'bottom bottom',
            scrub: 0.5,
            onUpdate: (self) => {
                stickyProgress = self.progress;
            },
        });
    }

    function killTriggers() {
        if (stMobile) {
            stMobile.kill();
            stMobile = null;
        }
        if (stDesktop) {
            stDesktop.kill();
            stDesktop = null;
        }
    }

    function handleResize() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth < 768;

        // Re-init triggers jika breakpoint berubah
        if (wasMobile !== isMobile) {
            killTriggers();
            // Reset state
            stickyProgress = 0;
            mobileNavVisible = true;
            // Setup trigger sesuai mode baru
            if (isMobile) {
                setupMobileTrigger();
            } else {
                setTimeout(() => setupDesktopTrigger(), 100);
            }
        }
    }

    onMount(() => {
        // Init mobile state
        isMobile = window.innerWidth < 768;
        window.addEventListener('resize', handleResize);

        // ── Lenis Smooth Scroll ──
        lenis = new Lenis({
            duration: 0.8,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -4 * t)),
            smoothWheel: true,
        });

        function raf(time: number) {
            lenis?.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // ── ScrollTrigger Setup ──
        if (isMobile) {
            setupMobileTrigger();
        } else {
            setTimeout(() => setupDesktopTrigger(), 100);
        }
    });

    onDestroy(() => {
        if (lenis) {
            lenis.destroy();
            lenis = null;
        }
        killTriggers();
        window.removeEventListener('resize', handleResize);
        ScrollTrigger.getAll().forEach((trigger) => trigger.kill());
    });
</script>

<LayoutBG>
    <!-- ══ NAVBAR ══ -->
    <Navbar
        class="border-b rounded-b-2xl border-border/60 bg-background/80 backdrop-blur-2xl fixed top-0 z-50 w-full transition-transform duration-300 ease-out"
        style="transform: {navTransform()}"
    >
        <div>
            <div
                class="text-lg font-semibold tracking-tight text-gradient-pink"
            >
                DevControl
            </div>
            <div class="text-xs text-muted-foreground hidden sm:block">
                Device Management System
            </div>
        </div>
        <div class="flex gap-2">
            <Button
                size="sm"
                variant="ghost"
                class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
                onclick={() => router.visit(home().url)}
            >
                <LucideHome class="size-4" />
                <span class="hidden sm:inline">Home</span>
            </Button>
            <Button
                size="sm"
                variant="ghost"
                class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
                onclick={() => router.visit(listPhone().url)}
            >
                <span class="hidden sm:inline">Kembali</span>
                <ArrowRightCircle class="size-4" />
            </Button>
        </div>
    </Navbar>

    <!-- ══ MOBILE LAYOUT ══ -->
    {#if isMobile}
        <div class="pb-10 space-y-0">
            <!-- Foto carousel mobile -->
            <div class="w-full aspect-3/4 h-full relative overflow-hidden">
                <Carousel.Root
                    class="w-full h-full mb-0 pb-0 pt-30"
                    plugins={[
                        Autoplay({ delay: 3500, stopOnInteraction: false }),
                    ]}
                >
                    <Carousel.Content class="h-full">
                        {#each phone.photos as photo, i (i)}
                            <Carousel.Item class="h-full">
                                <img
                                    src={photo}
                                    alt="foto {i + 1}"
                                    class="w-full h-full object-contain"
                                />
                            </Carousel.Item>
                        {/each}
                    </Carousel.Content>
                </Carousel.Root>
                <div
                    class="absolute inset-x-0 bottom-0 h-24 bg-linear-to-t from-background to-transparent z-10"
                />
            </div>

            <!-- Info cards stacked -->
            <div class="px-4 space-y-4 -mt-6 relative z-10">
                <!-- Nama + Harga -->
                <div
                    class="rounded-2xl border border-border/60 bg-card/80 backdrop-blur-xl p-5 space-y-2"
                >
                    <div
                        class="text-[11px] uppercase tracking-[0.22em] text-primary font-bold"
                    >
                        {raw.model_type}
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight leading-snug">
                        {phone.name}
                    </h1>
                    <div class="text-lg font-light text-gray-500">
                        ({phone.id})
                    </div>
                    <div class="flex items-center gap-2">
                        <Tag class="size-5 text-primary shrink-0" />
                        <span class="text-xl font-bold text-primary">{formatPrice(phone.price)}</span>
                    </div>
                </div>

                <!-- Spec -->
                <div
                    class="rounded-2xl border border-border/60 bg-card/80 backdrop-blur-xl p-5 space-y-3"
                >
                    <div
                        class="text-[11px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5"
                    >
                        <Cpu class="size-4" /> Spesifikasi
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div
                            class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3"
                        >
                            <Cpu class="size-5 text-primary shrink-0" />
                            <div>
                                <div
                                    class="text-[11px] text-muted-foreground uppercase font-bold"
                                >
                                    RAM
                                </div>
                                <div class="font-bold text-base">
                                    {phone.ram}
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3"
                        >
                            <HardDrive class="size-5 text-primary shrink-0" />
                            <div>
                                <div
                                    class="text-[11px] text-muted-foreground uppercase font-bold"
                                >
                                    Storage
                                </div>
                                <div class="font-bold text-base">
                                    {phone.storage}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ DESKTOP STICKY LAYOUT ══ -->
    {:else}
        <div class="relative" style="height: 200vh" bind:this={stickyRef}>
            <div class="sticky top-0 h-screen flex overflow-hidden">
                <!-- Foto 70% -->
                <div class="w-[70%] relative overflow-hidden bg-muted/20">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-background/50 z-10 pointer-events-none"
                    />
                    <Carousel.Root
                        class="w-full h-full"
                        plugins={[
                            Autoplay({ delay: 3500, stopOnInteraction: false }),
                        ]}
                    >
                        {#if phone.photos.length > 0}
                            <Carousel.Content class="h-full">
                                {#each phone.photos as photo, i (i)}
                                    <Carousel.Item class="h-screen">
                                        <img
                                            src={photo}
                                            alt="foto {i + 1}"
                                            class="w-full h-full object-contain"
                                        />
                                    </Carousel.Item>
                                {/each}
                            </Carousel.Content>
                        {/if}
                    </Carousel.Root>
                </div>

                <!-- Info 30% -->
                <div
                    class="w-[30%] flex flex-col justify-center gap-0 px-8 pt-20 pb-10 overflow-hidden"
                >
                    <!-- ① Nama + Harga -->
                    <div class="space-y-2 pb-5">
                        <div
                            class="text-[11px] uppercase tracking-[0.22em] text-primary font-bold"
                        >
                            {raw.model_type}
                        </div>
                        <h1
                            class="text-3xl font-bold tracking-tight leading-snug"
                        >
                            {phone.name}
                            <span
                                class="text-base ms-auto text-gray-400 font-light tracking-tighter"
                                >({phone.id})</span
                            >
                        </h1>
                        <div class="flex items-center gap-2">
                            <Tag class="size-5 text-primary shrink-0" />
                            <span class="text-2xl font-bold text-primary">{formatPrice(phone.price)}</span>
                        </div>
                    </div>

                    <!-- ② Spec -->
                    <div
                        class="border-t border-border/60 pt-5 pb-5 space-y-3 transition-all duration-700"
                    >
                        <div
                            class="text-[11px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5"
                        >
                            <Cpu class="size-4" /> Spesifikasi
                        </div>
                        <div class="flex flex-col gap-2">
                            <div
                                class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3"
                            >
                                <div
                                    class="size-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"
                                >
                                    <Cpu class="size-4 text-primary" />
                                </div>
                                <div>
                                    <div
                                        class="text-[11px] text-muted-foreground uppercase tracking-wider font-bold"
                                    >
                                        RAM
                                    </div>
                                    <div class="font-bold text-base">
                                        {phone.ram}
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3"
                            >
                                <div
                                    class="size-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"
                                >
                                    <HardDrive class="size-4 text-primary" />
                                </div>
                                <div>
                                    <div
                                        class="text-[11px] text-muted-foreground uppercase tracking-wider font-bold"
                                    >
                                        Storage
                                    </div>
                                    <div class="font-bold text-base">
                                        {phone.storage}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ③ Tanggal Penggantian -->
                    <div
                        class="border-t border-border/60 pt-5 pb-5 space-y-3 transition-all duration-700 delay-100"
                    >
                        <div
                            class="text-[11px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5"
                        >
                            <CalendarDays class="size-4" /> Tanggal Penggantian
                        </div>
                        <div
                            class="rounded-xl border border-border/50 bg-card/50 p-4"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted-foreground">Tanggal Pembelian</span>
                                <span class="text-sm font-semibold">{phone.buy_date}</span>
                            </div>
                            <div class="flex items-center justify-between mt-1.5">
                                <span class="text-sm text-muted-foreground">Brand</span>
                                <span class="text-sm font-semibold">{raw.brand?.name ?? raw.brand_id}</span>
                            </div>
                            <div class="flex items-center justify-between mt-1.5">
                                <span class="text-sm text-muted-foreground">Tipe</span>
                                <span class="text-sm font-semibold">{raw.model_type}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ④ Riwayat Pengecekan singkat -->
                    <div
                        class="border-t border-border/60 pt-5 space-y-3 transition-all duration-700 delay-200"
                    >
                        <div class="flex justify-between items-center">
                            <div
                                class="text-[11px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5"
                            >
                                <ClipboardList class="size-4" /> Riwayat Pengecekan
                            </div>
                        </div>
                        {#if phone.checks.length > 0}
                            <div class="space-y-1.5">
                                {#each phone.checks.slice(0, 4) as check}
                                    <div
                                        class="flex items-center gap-2 rounded-lg border border-border/50 bg-card/50 px-3 py-2"
                                    >
                                        {#if check.status === 'ok'}
                                            <CheckCircle2
                                                class="size-4 text-emerald-500 shrink-0"
                                            />
                                        {:else}
                                            <XCircle
                                                class="size-4 text-destructive shrink-0"
                                            />
                                        {/if}
                                        <span class="text-sm truncate flex-1"
                                            >{check.user} ({check.nik})</span
                                        >
                                        <span
                                            class="text-xs text-muted-foreground shrink-0"
                                            >{check.date.slice(5)}</span
                                        >
                                    </div>
                                {/each}
                            </div>
                        {:else}
                            <p class="text-sm text-muted-foreground">Belum ada riwayat pengecekan.</p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <!-- ══ SECTION: RIWAYAT PENGGUNAAN ══ -->
    <div class="bg-background border-t-2 border-border" id="usage">
        <div class="px-4 sm:px-12 py-10 sm:py-16 space-y-6">
            <div class="flex items-center gap-3">
                <div
                    class="size-9 sm:size-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0"
                >
                    <User class="size-4 sm:size-5 text-primary" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-2xl font-bold tracking-tight">
                        Riwayat Penggunaan
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        {phone.usages.length} sesi tercatat
                    </p>
                </div>
            </div>

            {#if phone.usages.length > 0}
                <!-- Desktop table -->
                <div
                    class="hidden md:block rounded-2xl border border-border/60 overflow-hidden"
                >
                    <div
                        class="grid grid-cols-[1.5fr_1.5fr_2fr_2fr] bg-muted/60 border-b border-border"
                    >
                        {#each [{ icon: User, label: 'Nama' }, { icon: CreditCard, label: 'NIK' }, { icon: LogIn, label: 'Absence' }, { icon: FileText, label: 'Keterangan' }] as col}
                            <div class="flex items-center gap-1.5 px-4 py-3">
                                <col.icon class="size-4 text-primary shrink-0" />
                                <span
                                    class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                                    >{col.label}</span
                                >
                            </div>
                        {/each}
                    </div>
                    {#each phone.usages as usage, i (i)}
                        <div
                            class="grid grid-cols-[1.5fr_1.5fr_2fr_2fr] border-b border-border/40 last:border-0 transition-colors
                            {i % 2 === 0
                                ? 'bg-card/40'
                                : 'bg-card/20'} hover:bg-primary/5"
                        >
                            <div class="px-4 py-4 flex items-center gap-2">
                                <div
                                    class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-xs font-bold text-primary"
                                >
                                    {usage.name.charAt(0)}
                                </div>
                                <span class="text-base font-medium truncate"
                                    >{usage.name}</span
                                >
                            </div>
                            <div class="px-4 py-4 flex items-center">
                                <span
                                    class="font-mono text-sm text-muted-foreground"
                                    >{usage.nik}</span
                                >
                            </div>
                            <div class="px-4 py-4 flex items-center gap-1.5">
                                <LogIn class="size-4 text-emerald-500 shrink-0" />
                                <span class="text-sm whitespace-nowrap"
                                    >{usage.login}</span
                                >
                            </div>
                            <div class="px-4 py-4 flex items-center">
                                <span class="text-sm text-muted-foreground"
                                    >{usage.note}</span
                                >
                            </div>
                        </div>
                    {/each}
                </div>

                <!-- Mobile cards -->
                <div class="md:hidden space-y-3">
                    {#each phone.usages as usage, i (i)}
                        <div
                            class="rounded-2xl border border-border/60 bg-card/60 p-4 space-y-3"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-base font-bold text-primary shrink-0"
                                >
                                    {usage.name.charAt(0)}
                                </div>
                                <div>
                                    <div class="font-semibold text-base">
                                        {usage.name}
                                    </div>
                                    <div
                                        class="font-mono text-xs text-muted-foreground"
                                    >
                                        {usage.nik}
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-3 py-2"
                            >
                                <LogIn class="size-4 text-emerald-500 shrink-0" />
                                <div>
                                    <div
                                        class="text-[11px] text-muted-foreground uppercase font-bold tracking-wider"
                                    >
                                        Absence
                                    </div>
                                    <div class="font-medium text-sm">
                                        {usage.login}
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-start gap-2 text-sm text-muted-foreground"
                            >
                                <FileText class="size-4 shrink-0 mt-0.5" />
                                <span>{usage.note}</span>
                            </div>
                        </div>
                    {/each}
                </div>
            {:else}
                <p class="text-sm text-muted-foreground">Belum ada riwayat penggunaan.</p>
            {/if}
        </div>
    </div>

    <!-- ══ SECTION: RIWAYAT PENGECEKAN ══ -->
    <div class="bg-muted/20 border-t border-border" id="checks">
        <div class="px-4 sm:px-12 py-10 sm:py-16 space-y-6">
            <div class="flex items-center gap-3">
                <div
                    class="size-9 sm:size-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0"
                >
                    <ClipboardList class="size-4 sm:size-5 text-primary" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-2xl font-bold tracking-tight">
                        Riwayat Pengecekan
                    </h2>
                    <p class="text-xs text-muted-foreground">
                        {phone.checks.length} catatan ditemukan
                    </p>
                </div>
            </div>

            {#if phone.checks.length > 0}
                <!-- Desktop table -->
                <div
                    class="hidden md:block rounded-2xl border border-border/60 overflow-hidden"
                >
                    <div
                        class="grid grid-cols-[1fr_1fr_1.2fr_1.2fr_2fr] bg-muted/60 border-b border-border"
                    >
                        {#each [{ icon: User, label: 'Petugas' }, { icon: CreditCard, label: 'NIK' }, { icon: CheckCircle2, label: 'Status' }, { icon: FileText, label: 'Tanggal' }, { icon: FileText, label: 'Catatan' }] as col}
                            <div class="flex items-center gap-1.5 px-4 py-3">
                                <col.icon class="size-4 text-primary shrink-0" />
                                <span
                                    class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                                    >{col.label}</span
                                >
                            </div>
                        {/each}
                    </div>
                    {#each phone.checks as check, i (i)}
                        <div
                            class="grid grid-cols-[1fr_1fr_1.2fr_1.2fr_2fr] border-b border-border/40 last:border-0 transition-colors
                            {i % 2 === 0
                                ? 'bg-card/40'
                                : 'bg-card/20'} hover:bg-primary/5"
                        >
                            <div class="px-4 py-4 flex items-center gap-2">
                                <div
                                    class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-xs font-bold text-primary"
                                >
                                    {check.user.charAt(0)}
                                </div>
                                <span class="text-sm font-medium truncate"
                                    >{check.user}</span
                                >
                            </div>
                            <div class="px-4 py-4 flex items-center">
                                <span
                                    class="font-mono text-sm text-muted-foreground"
                                    >{check.nik}</span
                                >
                            </div>
                            <div class="px-4 py-4 flex items-center">
                                <span
                                    class="inline-flex items-center gap-1.5 text-sm px-2.5 py-1 rounded-full font-medium border
                                    {check.status === 'ok'
                                        ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                                        : 'bg-destructive/10 text-destructive border-destructive/20'}"
                                >
                                    {#if check.status === 'ok'}
                                        <CheckCircle2 class="size-3.5" />
                                    {:else}
                                        <XCircle class="size-3.5" />
                                    {/if}
                                    {check.status === 'ok' ? 'OK' : 'Gagal'}
                                </span>
                            </div>
                            <div
                                class="px-4 py-4 flex items-center text-sm text-muted-foreground"
                            >
                                {check.date}
                            </div>
                            <div
                                class="px-4 py-4 flex items-center text-sm text-muted-foreground"
                            >
                                {check.note}
                            </div>
                        </div>
                    {/each}
                </div>

                <!-- Mobile cards -->
                <div class="md:hidden space-y-3">
                    {#each phone.checks as check, i (i)}
                        <div
                            class="rounded-2xl border border-border/60 bg-card/60 p-4 space-y-2"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-base font-bold text-primary shrink-0"
                                    >
                                        {check.user.charAt(0)}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base">
                                            {check.user}
                                        </div>
                                        <div
                                            class="font-mono text-xs text-muted-foreground"
                                        >
                                            {check.nik}
                                        </div>
                                    </div>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium border
                                    {check.status === 'ok'
                                        ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                                        : 'bg-destructive/10 text-destructive border-destructive/20'}"
                                >
                                    {#if check.status === 'ok'}
                                        <CheckCircle2 class="size-3.5" />
                                    {:else}
                                        <XCircle class="size-3.5" />
                                    {/if}
                                    {check.status === 'ok' ? 'OK' : 'Gagal'}
                                </span>
                            </div>
                            <div class="text-xs text-muted-foreground font-medium">
                                {check.date}
                            </div>
                            <div
                                class="text-sm text-muted-foreground flex items-start gap-1.5"
                            >
                                <FileText class="size-4 shrink-0 mt-0.5" />
                                {check.note}
                            </div>
                        </div>
                    {/each}
                </div>
            {:else}
                <p class="text-sm text-muted-foreground">Belum ada riwayat pengecekan.</p>
            {/if}
        </div>
    </div>
</LayoutBG>
