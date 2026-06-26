<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { router } from '@inertiajs/svelte';
    import { home } from '$routes';
    import { Button } from '$shadcn/components/ui/button';
    import { assetUrl, formatPrice } from '$lib/asset';
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
    import { TableHandler, RowsPerPage, RowCount, Pagination } from '@vincjo/datatables';
    import { Search as DtSearch } from '@vincjo/datatables';
    import * as Carousel from '$shadcn/components/ui/carousel';
    import { X } from '@lucide/svelte';
    import { onMount, onDestroy } from 'svelte';
    import gsap from 'gsap';
    import Lenis from 'lenis';
    import ScrollTrigger from 'gsap/ScrollTrigger';
    import Autoplay from 'embla-carousel-autoplay';

    // ─── Props ──────────────────────────────────────────────────
    let { phone: raw, usages: rawUsages, latestUser, checks: rawChecks }: { phone: PhoneType; usages: { name: string; nik: string; login: string; note: string | null }[]; latestUser: { name: string; nik: string; login: string; note: string | null } | null; checks?: { date: string; user: string; username: string; status: string; imei_ok: boolean; mac_ok: boolean; note: string | null; foto: string[] | null }[] } = $props();

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
        imei: string | null;
        mac_address: string | null;
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
        checks: (rawChecks ?? []).map(c => ({ ...c, nik: c.username })) as { date: string; user: string; nik: string; status: string; note: string; imei_ok: boolean; mac_ok: boolean; foto: string[] | null }[],
        usages: rawUsages ?? [],
    };

    const usagesTable = $derived(phone.usages.length > 0 ? new TableHandler(phone.usages, { rowsPerPage: 10 }) : null);
    const checksTable = $derived(phone.checks.length > 0 ? new TableHandler(phone.checks, { rowsPerPage: 10 }) : null);

    // ── STATE with runes ──
    let lenis: Lenis | null = $state(null);
    let stickyRef = $state<HTMLDivElement | null>(null);
    let isMobile = $state(false);
    let selectedCheck = $state<{ date: string; user: string; nik: string; status: string; note: string; imei_ok: boolean; mac_ok: boolean; foto: string[] | null } | null>(null);
    let checkModalOpen = $state(false);

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
                                    class="w-full h-full object-contain p-4"
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

                    {#if raw.imei || raw.mac_address}
                    <div class="border-t border-primary/30 pt-5 pb-1 space-y-2">
                        <div class="text-[11px] uppercase tracking-[0.22em] text-primary font-bold flex items-center gap-1.5">
                            <span class="size-2 rounded-full bg-primary animate-pulse" /> Hardware ID
                        </div>
                        <div class="grid gap-2">
                            {#if raw.imei}
                            <div class="rounded-xl border-2 border-primary/30 bg-primary/5 p-3 flex items-center gap-3 shadow-sm shadow-primary/10">
                                <div class="size-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-black text-primary tracking-wider">IMEI</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-primary/70 uppercase tracking-wider font-bold">IMEI</div>
                                    <code class="text-sm font-mono font-bold text-foreground tracking-wide">{raw.imei}</code>
                                </div>
                                <div class="ml-auto shrink-0">
                                    <span class="size-2 rounded-full bg-emerald-500 inline-block" />
                                </div>
                            </div>
                            {/if}
                            {#if raw.mac_address}
                            <div class="rounded-xl border-2 border-primary/30 bg-primary/5 p-3 flex items-center gap-3 shadow-sm shadow-primary/10">
                                <div class="size-9 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-black text-primary tracking-wider">MAC</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-primary/70 uppercase tracking-wider font-bold">MAC Address</div>
                                    <code class="text-sm font-mono font-bold text-foreground tracking-wide">{raw.mac_address}</code>
                                </div>
                                <div class="ml-auto shrink-0">
                                    <span class="size-2 rounded-full bg-emerald-500 inline-block" />
                                </div>
                            </div>
                            {/if}
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
        </div>

        <!-- ══ DESKTOP STICKY LAYOUT ══ -->
    {:else}
        <div class="relative" style="height: 200vh" bind:this={stickyRef}>
            <div class="sticky top-0 h-screen flex overflow-hidden">
                <!-- Foto 70% -->
                <div class="w-[70%] relative overflow-hidden bg-black/60">
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
                                            class="w-full h-full object-contain p-4"
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

                    {#if raw.imei || raw.mac_address}
                    <!-- Identitas Hardware -->
                    <div class="border-t border-primary/30 pt-5 pb-3 space-y-3 transition-all duration-700 delay-75">
                        <div class="text-[11px] uppercase tracking-[0.22em] text-primary font-bold flex items-center gap-1.5">
                            <span class="size-2 rounded-full bg-primary animate-pulse" /> Identitas Hardware
                        </div>
                        <div class="grid gap-2">
                            {#if raw.imei}
                            <div class="rounded-xl border-2 border-primary/30 bg-primary/5 p-3 flex items-center gap-3 shadow-sm shadow-primary/10">
                                <div class="size-10 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-black text-primary tracking-wider">IMEI</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-primary/70 uppercase tracking-wider font-bold">IMEI</div>
                                    <code class="text-sm font-mono font-bold text-foreground tracking-wide">{raw.imei}</code>
                                </div>
                                <div class="ml-auto shrink-0">
                                    <span class="size-2 rounded-full bg-emerald-500 inline-block" title="Tervalidasi" />
                                </div>
                            </div>
                            {/if}
                            {#if raw.mac_address}
                            <div class="rounded-xl border-2 border-primary/30 bg-primary/5 p-3 flex items-center gap-3 shadow-sm shadow-primary/10">
                                <div class="size-10 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-black text-primary tracking-wider">MAC</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-primary/70 uppercase tracking-wider font-bold">MAC Address</div>
                                    <code class="text-sm font-mono font-bold text-foreground tracking-wide">{raw.mac_address}</code>
                                </div>
                                <div class="ml-auto shrink-0">
                                    <span class="size-2 rounded-full bg-emerald-500 inline-block" title="Tervalidasi" />
                                </div>
                            </div>
                            {/if}
                        </div>
                    </div>
                    {/if}

                    {#if phone.checks.length > 0}
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
                            <button
                                onclick={() => {
                                    const el = document.getElementById('checks');
                                    if (el) {
                                        lenis?.scrollTo(el, { offset: -80, duration: 1.2 });
                                    }
                                }}
                                class="text-[10px] text-muted-foreground hover:text-foreground underline underline-offset-2 transition-colors"
                            >
                                Lihat Semua
                            </button>
                        </div>
                            <button
                                onclick={() => {
                                    const el = document.getElementById('checks');
                                    if (el) {
                                        lenis?.scrollTo(el, { offset: -80, duration: 1.2 });
                                    }
                                }}
                                class="w-full flex items-center gap-3 rounded-lg border border-border/50 bg-card/50 hover:bg-card hover:border-primary/40 transition-all px-4 py-3 group"
                            >
                                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 group-hover:bg-primary/20 transition-colors">
                                    <ClipboardList class="size-4 text-primary" />
                                </div>
                                <div class="flex-1 text-left">
                                    <div class="text-sm font-medium group-hover:text-primary transition-colors">Lihat Riwayat Pengecekan</div>
                                    <div class="text-xs text-muted-foreground">{phone.checks.length} catatan &middot; Klik untuk detail</div>
                                </div>
                                <svg class="size-4 text-muted-foreground group-hover:text-primary transition-colors shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    {/if}

    {#if phone.usages.length > 0}
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

            <div class="flex items-center gap-3 px-4 py-2 border-b border-border/30 bg-muted/10 flex-wrap rounded-t-2xl">
                <DtSearch table={usagesTable} />
                <RowsPerPage table={usagesTable} />
            </div>

            <div class="p-3 space-y-2">
                {#each usagesTable!.rows as row}
                    <div class="rounded-xl border border-border/60 bg-card p-3 flex items-center gap-3">
                        <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-xs font-bold text-primary">
                            {row.name.charAt(0)}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm">{row.name}</div>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground flex-wrap">
                                <span class="font-mono">{row.nik}</span>
                                <span>·</span>
                                <span>{row.login}</span>
                                {#if row.note}
                                    <span>·</span>
                                    <span>{row.note}</span>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/each}
            </div>

            <div class="flex items-center justify-between px-4 py-2 border-t border-border/30 bg-muted/10">
                <RowCount table={usagesTable} />
                <Pagination table={usagesTable} />
            </div>
        </div>
    </div>
    {/if}

    {#if phone.checks.length > 0}
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

            <div class="flex items-center gap-3 px-4 py-2 border-b border-border/30 bg-muted/10 flex-wrap rounded-t-2xl">
                <DtSearch table={checksTable!} />
                <RowsPerPage table={checksTable!} />
            </div>

            <div class="p-3 space-y-2">
                {#each checksTable!.rows as row}
                    <div
                        class="rounded-xl border border-border/60 bg-card p-3 space-y-2 cursor-pointer transition-all hover:border-primary/40 hover:shadow-sm"
                        onclick={() => { selectedCheck = { ...row }; checkModalOpen = true; }}
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="size-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-xs font-bold text-primary">
                                    {row.user.charAt(0)}
                                </div>
                                <span class="font-medium text-sm">{row.user}</span>
                                <span class="font-mono text-xs text-muted-foreground">({row.nik})</span>
                            </div>
                            <span
                                class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium border
                                {row.status === 'ok'
                                    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                                    : 'bg-destructive/10 text-destructive border-destructive/20'}"
                            >
                                {#if row.status === 'ok'}
                                    <CheckCircle2 class="size-3" />
                                {:else}
                                    <XCircle class="size-3" />
                                {/if}
                                {row.status === 'ok' ? 'OK' : 'Gagal'}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                            <span>{row.date}</span>
                            {#if row.imei_ok || row.mac_ok}
                                <span>·</span>
                                <div class="flex items-center gap-1">
                                    {#if row.imei_ok}<span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 font-mono">IMEI ✓</span>{/if}
                                    {#if row.mac_ok}<span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 font-mono">MAC ✓</span>{/if}
                                </div>
                            {/if}
                            {#if row.note}
                                <span>·</span>
                                <span class="truncate max-w-[200px]">{row.note}</span>
                            {/if}
                        </div>
                        {#if row.foto && row.foto.length > 0}
                            <div class="flex gap-1.5 mt-1.5">
                                {#each row.foto as f}
                                    <img
                                        src={assetUrl(f)}
                                        alt="check foto"
                                        class="size-10 rounded object-cover ring-1 ring-border/50"
                                    />
                                {/each}
                            </div>
                        {/if}
                    </div>
                {/each}
            </div>

            <div class="flex items-center justify-between px-4 py-2 border-t border-border/30 bg-muted/10">
                <RowCount table={checksTable!} />
                <Pagination table={checksTable!} />
            </div>

            <!-- Tombol scroll ke Riwayat Penggunaan -->
            <div class="flex justify-center pt-4">
                <button
                    onclick={() => {
                        const el = document.getElementById('usage');
                        if (el) {
                            lenis?.scrollTo(el, { offset: -80, duration: 1.2 });
                        }
                    }}
                    class="inline-flex items-center gap-2 text-xs text-muted-foreground hover:text-foreground transition-colors px-4 py-2 rounded-full border border-border/40 hover:border-border/80"
                >
                    Lihat Riwayat Penggunaan
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M7 13l5 5 5-5M7 6l5 5 5-5"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    {/if}
    {#if checkModalOpen && selectedCheck}
        <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
        <div
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overscroll-contain"
            onclick={() => checkModalOpen = false}
        >
            <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
            <div
                class="w-full max-w-2xl max-h-[85vh] overflow-y-auto rounded-2xl border border-border/60 bg-background shadow-2xl"
                onclick={(e) => e.stopPropagation()}
                onwheel={(e) => e.stopPropagation()}
            >
                <!-- Close button -->
                <div class="flex items-center justify-between px-5 pt-4 pb-0">
                    <h3 class="font-bold text-lg">Detail Pengecekan</h3>
                    <button
                        onclick={() => checkModalOpen = false}
                        class="size-8 rounded-full flex items-center justify-center hover:bg-muted transition-colors"
                    >
                        <X class="size-4" />
                    </button>
                </div>

                <!-- Carousel Foto Pengecekan -->
                {#if selectedCheck.foto && selectedCheck.foto.length > 0}
                    <div class="px-5 pt-3">
                        <Carousel.Root
                            class="w-full"
                            plugins={[
                                Autoplay({ delay: 1500, stopOnInteraction: true }),
                            ]}
                        >
                            <Carousel.Content>
                                {#each selectedCheck.foto as f, i (i)}
                                    <Carousel.Item>
                                        <div class="aspect-video bg-muted/20 rounded-xl overflow-hidden">
                                            <img
                                                src={assetUrl(f)}
                                                alt="Foto pengecekan {i + 1}"
                                                class="w-full h-full object-contain"
                                            />
                                        </div>
                                    </Carousel.Item>
                                {/each}
                            </Carousel.Content>
                        </Carousel.Root>
                    </div>
                {:else}
                    <div class="px-5 pt-3">
                        <div class="aspect-video bg-muted/20 rounded-xl flex items-center justify-center text-muted-foreground text-sm">
                            Tidak ada foto pengecekan
                        </div>
                    </div>
                {/if}

                <!-- Description -->
                <div class="p-5 space-y-3">
                    <div class="flex items-center gap-3 pb-3 border-b border-border/40">
                        <div class="size-10 rounded-full bg-primary/15 flex items-center justify-center shrink-0 text-lg font-bold text-primary">
                            {selectedCheck.user.charAt(0)}
                        </div>
                        <div>
                            <div class="font-semibold text-base">{selectedCheck.user}</div>
                            <div class="text-xs text-muted-foreground font-mono">{selectedCheck.nik}</div>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between rounded-lg bg-muted/30 px-4 py-2.5">
                            <span class="text-xs text-muted-foreground font-medium">Tanggal</span>
                            <span class="text-sm font-semibold">{selectedCheck.date}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-muted/30 px-4 py-2.5">
                            <span class="text-xs text-muted-foreground font-medium">Status</span>
                            <span
                                class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium border
                                {selectedCheck.status === 'ok'
                                    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                                    : 'bg-destructive/10 text-destructive border-destructive/20'}"
                            >
                                {#if selectedCheck.status === 'ok'}
                                    <CheckCircle2 class="size-3" />
                                {:else}
                                    <XCircle class="size-3" />
                                {/if}
                                {selectedCheck.status === 'ok' ? 'OK' : 'Gagal'}
                            </span>
                        </div>
                        {#if selectedCheck.imei_ok}
                            <div class="flex items-center justify-between rounded-lg bg-emerald-500/5 border border-emerald-500/15 px-4 py-2.5">
                                <span class="text-xs text-muted-foreground font-medium">IMEI</span>
                                <span class="text-sm font-semibold text-emerald-600">✓ Sesuai</span>
                            </div>
                        {/if}
                        {#if selectedCheck.mac_ok}
                            <div class="flex items-center justify-between rounded-lg bg-emerald-500/5 border border-emerald-500/15 px-4 py-2.5">
                                <span class="text-xs text-muted-foreground font-medium">MAC Address</span>
                                <span class="text-sm font-semibold text-emerald-600">✓ Sesuai</span>
                            </div>
                        {/if}
                        {#if selectedCheck.note}
                            <div class="rounded-lg bg-muted/30 px-4 py-2.5 space-y-1">
                                <span class="text-xs text-muted-foreground font-medium">Keterangan</span>
                                <p class="text-sm leading-relaxed">{selectedCheck.note}</p>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/if}

</LayoutBG>

<svelte:window onkeydown={(e) => { if (e.key === 'Escape') checkModalOpen = false; }} />

<style>
</style>
