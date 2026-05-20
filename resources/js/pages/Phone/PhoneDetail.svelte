<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { home } from '$routes';
    import { Button } from '$shadcn/components/ui/button';
    import {
        ArrowRightCircle, LucideHome, Cpu, HardDrive,
        Tag, ShoppingCart, ClipboardList, CheckCircle2,
        XCircle, User, CreditCard, LogIn, LogOut, FileText
    } from '@lucide/svelte';
    import { listPhone } from '$routes/phone';
    import * as Carousel from '$shadcn/components/ui/carousel';
    import { onMount, onDestroy } from 'svelte';
    import gsap from 'gsap';
    import Lenis from 'lenis';
    import ScrollTrigger from 'gsap/ScrollTrigger';
    import Autoplay from 'embla-carousel-autoplay';

    // ── DATA ──
    const phone = {
        id: "DEV-002",
        name: 'Samsung Galaxy A54',
        price: 'Rp 4.999.000',
        ram: '8 GB',
        storage: '256 GB',
        color: 'Awesome Violet',
        photos: [
            'https://images.samsung.com/is/image/samsung/p6pim/id/sm-a546ezkdxid/gallery/id-galaxy-a54-5g-sm-a546-sm-a546ezkdxid-535684283?$1164_776_PNG$',
            'https://images.samsung.com/is/image/samsung/p6pim/id/sm-a546ezkdxid/gallery/id-galaxy-a54-5g-sm-a546-sm-a546ezkdxid-535684266?$1164_776_PNG$',
            'https://images.samsung.com/is/image/samsung/p6pim/id/sm-a546ezkdxid/gallery/id-galaxy-a54-5g-sm-a546-sm-a546ezkdxid-535684267?$Q90_1368_1094_F_JPG$',
        ],
        purchases: [
            { date: '2024-03-10', vendor: 'iBox Indonesia', invoice: 'INV-2024-0312', price: 'Rp 4.999.000' },
        ],
        checks: [
            { date: '2026-01-20', user: 'Budi Santoso',  status: 'ok',   note: 'Semua fungsi normal' },
            { date: '2026-01-15', user: 'Siti Rahayu',   status: 'ok',   note: 'Layar & kamera baik' },
            { date: '2026-01-10', user: 'Ahmad Fauzi',   status: 'fail', note: 'Baterai kembung, diganti' },
            { date: '2025-12-28', user: 'Dewi Kartika',  status: 'ok',   note: 'Pengecekan rutin' },
            { date: '2025-12-15', user: 'Budi Santoso',  status: 'ok',   note: 'Pengecekan rutin' },
        ],
        usages: [
            { name: 'Budi Santoso',  nik: '3301234567890001', login: '2026-01-20 08:00', logout: '2026-01-20 17:00', note: 'Survey lapangan' },
            { name: 'Siti Rahayu',   nik: '3301234567890002', login: '2026-01-15 09:30', logout: '2026-01-15 16:00', note: 'Presentasi klien' },
            { name: 'Ahmad Fauzi',   nik: '3301234567890003', login: '2026-01-10 07:45', logout: '2026-01-10 15:30', note: 'Inspeksi gudang' },
            { name: 'Dewi Kartika',  nik: '3301234567890004', login: '2025-12-28 08:00', logout: '2025-12-28 17:00', note: 'Monitoring produksi' },
            { name: 'Rudi Hermawan', nik: '3301234567890005', login: '2025-12-15 10:00', logout: '2025-12-15 14:00', note: 'Cek inventori' },
        ],
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
    const desktopNavHidden = $derived(!isMobile && stickyProgress > 0.05 && stickyProgress < 0.95);

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
                console.log("mobile:" + self.scroll());
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
                console.log("desktop:" + self.scroll());
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
        ScrollTrigger.getAll().forEach(trigger => trigger.kill());
    });
</script>

<LayoutBG>
    <!-- ══ NAVBAR ══ -->
    <Navbar
        class="border-b rounded-b-2xl border-border/60 bg-background/80 backdrop-blur-2xl fixed top-0 z-50 w-full transition-transform duration-300 ease-out"
        style="transform: {navTransform()}"
    >
        <div>
            <div class="text-lg font-semibold tracking-tight text-gradient-pink">DevControl</div>
            <div class="text-xs text-muted-foreground hidden sm:block">Device Management System</div>
        </div>
        <div class="flex gap-2">
            <Button size="sm" variant="ghost"
                    class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
                    onclick={() => router.visit(routeUrl(home()))}>
                <LucideHome class="size-4" />
                <span class="hidden sm:inline">Home</span>
            </Button>
            <Button size="sm" variant="ghost"
                    class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
                    onclick={() => router.visit(routeUrl(listPhone()))}>
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
                <Carousel.Root class="w-full h-full mb-0 pb-0 pt-30" plugins={[Autoplay({ delay: 3500, stopOnInteraction: false })]}>
                    <Carousel.Content class="h-full">
                        {#each phone.photos as photo, i (i)}
                            <Carousel.Item class="h-full">
                                <img src={photo} alt="foto {i+1}" class="w-full h-full object-contain" />
                            </Carousel.Item>
                        {/each}
                    </Carousel.Content>
                </Carousel.Root>
                <div class="absolute inset-x-0 bottom-0 h-24 bg-linear-to-t from-background to-transparent z-10" />
            </div>

            <!-- Info cards stacked -->
            <div class="px-4 space-y-4 -mt-6 relative z-10">

                <!-- Nama + Harga -->
                <div class="rounded-2xl border border-border/60 bg-card/80 backdrop-blur-xl p-5 space-y-2">
                    <div class="text-[10px] uppercase tracking-[0.22em] text-primary font-bold">Smartphone</div>
                    <h1 class="text-xl font-bold tracking-tight leading-snug">{phone.name}</h1>
                    <div class="text-md font-light text-gray-500">({phone.id})</div>
                    <div class="flex items-center gap-2">
                        <Tag class="size-4 text-primary shrink-0" />
                        <span class="text-lg font-bold text-primary">{phone.price}</span>
                    </div>
                </div>

                <!-- Spec -->
                <div class="rounded-2xl border border-border/60 bg-card/80 backdrop-blur-xl p-5 space-y-3">
                    <div class="text-[10px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5">
                        <Cpu class="size-3" /> Spesifikasi
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3">
                            <Cpu class="size-4 text-primary shrink-0" />
                            <div>
                                <div class="text-[10px] text-muted-foreground uppercase">RAM</div>
                                <div class="font-bold text-sm">{phone.ram}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3">
                            <HardDrive class="size-4 text-primary shrink-0" />
                            <div>
                                <div class="text-[10px] text-muted-foreground uppercase">Storage</div>
                                <div class="font-bold text-sm">{phone.storage}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pembelian -->
                <div class="rounded-2xl border border-border/60 bg-card/80 backdrop-blur-xl p-5 space-y-3">
                    <div class="text-[10px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5">
                        <ShoppingCart class="size-3" /> Riwayat Pembelian
                    </div>
                    {#each phone.purchases as p}
                        <div class="rounded-xl border border-border/50 bg-muted/30 p-3">
                            <div class="flex justify-between items-start">
                                <span class="text-xs font-semibold">{p.vendor}</span>
                                <span class="text-[10px] text-muted-foreground">{p.date}</span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="font-mono text-[10px] text-muted-foreground">{p.invoice}</span>
                                <span class="text-xs font-bold text-primary">{p.price}</span>
                            </div>
                        </div>
                    {/each}
                </div>

                <!-- Pengecekan singkat -->
                <div class="rounded-2xl border border-border/60 bg-card/80 backdrop-blur-xl p-5 space-y-3">
                    <div class="text-[10px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5">
                        <ClipboardList class="size-3" /> Pengecekan Terakhir
                    </div>
                    {#each phone.checks.slice(0, 3) as check}
                        <div class="flex items-center gap-2 rounded-lg border border-border/50 bg-card/50 px-3 py-2">
                            {#if check.status === 'ok'}
                                <CheckCircle2 class="size-3.5 text-emerald-500 shrink-0" />
                            {:else}
                                <XCircle class="size-3.5 text-destructive shrink-0" />
                            {/if}
                            <span class="text-xs truncate flex-1">{check.user}</span>
                            <span class="text-[10px] text-muted-foreground shrink-0">{check.date.slice(5)}</span>
                        </div>
                    {/each}
                </div>
            </div>
        </div>

        <!-- ══ DESKTOP STICKY LAYOUT ══ -->
    {:else}
        <div class="relative" style="height: 200vh" bind:this={stickyRef}>
            <div class="sticky top-0 h-screen flex overflow-hidden">

                <!-- Foto 70% -->
                <div class="w-[70%] relative overflow-hidden bg-muted/20">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-background/50 z-10 pointer-events-none" />
                    <Carousel.Root class="w-full h-full" plugins={[Autoplay({ delay: 3500, stopOnInteraction: false })]}>
                        <Carousel.Content class="h-full">
                            {#each phone.photos as photo, i (i)}
                                <Carousel.Item class="h-screen">
                                    <img src={photo} alt="foto {i+1}" class="w-full h-full object-cover" />
                                </Carousel.Item>
                            {/each}
                        </Carousel.Content>
                    </Carousel.Root>
                </div>

                <!-- Info 30% -->
                <div class="w-[30%] flex flex-col justify-center gap-0 px-8 pt-20 pb-10 overflow-hidden">

                    <!-- ① Nama + Harga -->
                    <div class="space-y-2 pb-5">
                        <div class="text-[10px] uppercase tracking-[0.22em] text-primary font-bold">Smartphone</div>
                        <h1 class="text-2xl font-bold tracking-tight leading-snug">{phone.name} <span class="text-sm ms-auto text-gray-400 font-light tracking-tighter">({phone.id})</span></h1>
                        <div class="flex items-center gap-2">
                            <Tag class="size-4 text-primary shrink-0" />
                            <span class="text-xl font-bold text-primary">{phone.price}</span>
                        </div>
                    </div>

                    <!-- ② Spec -->
                    <div class="border-t border-border/60 pt-5 pb-5 space-y-3 transition-all duration-700">
                        <div class="text-[10px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5">
                            <Cpu class="size-3" /> Spesifikasi
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3">
                                <div class="size-7 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                    <Cpu class="size-3.5 text-primary" />
                                </div>
                                <div>
                                    <div class="text-[10px] text-muted-foreground uppercase tracking-wider">RAM</div>
                                    <div class="font-bold text-sm">{phone.ram}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl border border-border/60 bg-muted/30 p-3">
                                <div class="size-7 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                    <HardDrive class="size-3.5 text-primary" />
                                </div>
                                <div>
                                    <div class="text-[10px] text-muted-foreground uppercase tracking-wider">Storage</div>
                                    <div class="font-bold text-sm">{phone.storage}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ③ Riwayat Pembelian -->
                    <div class="border-t border-border/60 pt-5 pb-5 space-y-3 transition-all duration-700 delay-100">
                        <div class="text-[10px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5">
                            <ShoppingCart class="size-3" /> Riwayat Pembelian
                        </div>
                        {#each phone.purchases as p}
                            <div class="rounded-xl border border-border/50 bg-card/50 p-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold truncate">{p.vendor}</span>
                                    <span class="text-[10px] text-muted-foreground shrink-0 ml-2">{p.date}</span>
                                </div>
                                <div class="flex items-center justify-between mt-0.5">
                                    <span class="font-mono text-[10px] text-muted-foreground">{p.invoice}</span>
                                    <span class="text-xs font-bold text-primary">{p.price}</span>
                                </div>
                            </div>
                        {/each}
                    </div>

                    <!-- ④ Riwayat Pengecekan singkat -->
                    <div class="border-t border-border/60 pt-5 space-y-3 transition-all duration-700 delay-200">
                        <div class="flex justify-between items-center">
                            <div class="text-[10px] uppercase tracking-[0.22em] text-muted-foreground font-bold flex items-center gap-1.5">
                                <ClipboardList class="size-3" /> Riwayat Pengecekan
                            </div>
                            <a href="#checks" class="text-[10px] flex items-center text-muted-foreground hover:text-primary hover:underline gap-1">
                                Selengkapnya <ArrowRightCircle class="size-3" />
                            </a>
                        </div>
                        <div class="space-y-1.5">
                            {#each phone.checks.slice(0, 4) as check}
                                <div class="flex items-center gap-2 rounded-lg border border-border/50 bg-card/50 px-3 py-2">
                                    {#if check.status === 'ok'}
                                        <CheckCircle2 class="size-3.5 text-emerald-500 shrink-0" />
                                    {:else}
                                        <XCircle class="size-3.5 text-destructive shrink-0" />
                                    {/if}
                                    <span class="text-xs truncate flex-1">{check.user}</span>
                                    <span class="text-[10px] text-muted-foreground shrink-0">{check.date.slice(5)}</span>
                                </div>
                            {/each}
                        </div>
                        <p class="text-[10px] text-muted-foreground text-center pt-1 animate-bounce">↓ Scroll untuk riwayat penggunaan</p>
                    </div>

                </div>
            </div>
        </div>
    {/if}

    <!-- ══ SECTION: RIWAYAT PENGGUNAAN ══ -->
    <div class="bg-background border-t-2 border-border" id="usage">
        <div class="px-4 sm:px-12 py-10 sm:py-16 space-y-6">
            <div class="flex items-center gap-3">
                <div class="size-9 sm:size-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <User class="size-4 sm:size-5 text-primary" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-2xl font-bold tracking-tight">Riwayat Penggunaan</h2>
                    <p class="text-xs text-muted-foreground">{phone.usages.length} sesi tercatat</p>
                </div>
            </div>

            <!-- Desktop table -->
            <div class="hidden md:block rounded-2xl border border-border/60 overflow-hidden">
                <div class="grid grid-cols-[1.5fr_1.5fr_1fr_1fr_2fr] bg-muted/60 border-b border-border">
                    {#each [
                        { icon: User,       label: 'Nama' },
                        { icon: CreditCard, label: 'NIK' },
                        { icon: LogIn,      label: 'Login' },
                        { icon: LogOut,     label: 'Logout' },
                        { icon: FileText,   label: 'Keterangan' },
                    ] as col}
                        <div class="flex items-center gap-1.5 px-4 py-3">
                            <col.icon class="size-3.5 text-primary shrink-0" />
                            <span class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground">{col.label}</span>
                        </div>
                    {/each}
                </div>
                {#each phone.usages as usage, i (i)}
                    <div class="grid grid-cols-[1.5fr_1.5fr_1fr_1fr_2fr] border-b border-border/40 last:border-0 transition-colors
                        {i % 2 === 0 ? 'bg-card/40' : 'bg-card/20'} hover:bg-primary/5">
                        <div class="px-4 py-4 flex items-center gap-2">
                            <div class="size-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-[11px] font-bold text-primary">
                                {usage.name.charAt(0)}
                            </div>
                            <span class="text-sm font-medium truncate">{usage.name}</span>
                        </div>
                        <div class="px-4 py-4 flex items-center">
                            <span class="font-mono text-xs text-muted-foreground">{usage.nik}</span>
                        </div>
                        <div class="px-4 py-4 flex items-center gap-1.5">
                            <LogIn class="size-3.5 text-emerald-500 shrink-0" />
                            <span class="text-xs whitespace-nowrap">{usage.login}</span>
                        </div>
                        <div class="px-4 py-4 flex items-center gap-1.5">
                            <LogOut class="size-3.5 text-destructive shrink-0" />
                            <span class="text-xs whitespace-nowrap">{usage.logout}</span>
                        </div>
                        <div class="px-4 py-4 flex items-center">
                            <span class="text-xs text-muted-foreground">{usage.note}</span>
                        </div>
                    </div>
                {/each}
            </div>

            <!-- Mobile cards -->
            <div class="md:hidden space-y-3">
                {#each phone.usages as usage, i (i)}
                    <div class="rounded-2xl border border-border/60 bg-card/60 p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary shrink-0">
                                {usage.name.charAt(0)}
                            </div>
                            <div>
                                <div class="font-semibold text-sm">{usage.name}</div>
                                <div class="font-mono text-[11px] text-muted-foreground">{usage.nik}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="flex items-center gap-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-3 py-2">
                                <LogIn class="size-3.5 text-emerald-500 shrink-0" />
                                <div>
                                    <div class="text-[10px] text-muted-foreground">Login</div>
                                    <div class="font-medium">{usage.login}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 rounded-lg bg-destructive/10 border border-destructive/20 px-3 py-2">
                                <LogOut class="size-3.5 text-destructive shrink-0" />
                                <div>
                                    <div class="text-[10px] text-muted-foreground">Logout</div>
                                    <div class="font-medium">{usage.logout}</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-muted-foreground">
                            <FileText class="size-3.5 shrink-0 mt-0.5" />
                            <span>{usage.note}</span>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </div>

    <!-- ══ SECTION: RIWAYAT PENGECEKAN ══ -->
    <div class="bg-muted/20 border-t border-border" id="checks">
        <div class="px-4 sm:px-12 py-10 sm:py-16 space-y-6">
            <div class="flex items-center gap-3">
                <div class="size-9 sm:size-10 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <ClipboardList class="size-4 sm:size-5 text-primary" />
                </div>
                <div>
                    <h2 class="text-lg sm:text-2xl font-bold tracking-tight">Riwayat Pengecekan</h2>
                    <p class="text-xs text-muted-foreground">{phone.checks.length} catatan ditemukan</p>
                </div>
            </div>

            <!-- Desktop table -->
            <div class="hidden md:block rounded-2xl border border-border/60 overflow-hidden">
                <div class="grid grid-cols-[1fr_1fr_1fr_2fr] bg-muted/60 border-b border-border">
                    {#each [
                        { icon: User,          label: 'Petugas' },
                        { icon: CheckCircle2,  label: 'Status' },
                        { icon: FileText,      label: 'Tanggal' },
                        { icon: FileText,      label: 'Catatan' },
                    ] as col}
                        <div class="flex items-center gap-1.5 px-4 py-3">
                            <col.icon class="size-3.5 text-primary shrink-0" />
                            <span class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground">{col.label}</span>
                        </div>
                    {/each}
                </div>
                {#each phone.checks as check, i (i)}
                    <div class="grid grid-cols-[1fr_1fr_1fr_2fr] border-b border-border/40 last:border-0 transition-colors
                        {i % 2 === 0 ? 'bg-card/40' : 'bg-card/20'} hover:bg-primary/5">
                        <div class="px-4 py-4 flex items-center gap-2">
                            <div class="size-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-[11px] font-bold text-primary">
                                {check.user.charAt(0)}
                            </div>
                            <span class="text-sm font-medium truncate">{check.user}</span>
                        </div>
                        <div class="px-4 py-4 flex items-center">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium border
                                {check.status === 'ok'
                                    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                                    : 'bg-destructive/10 text-destructive border-destructive/20'}">
                                {#if check.status === 'ok'}
                                    <CheckCircle2 class="size-3" />
                                {:else}
                                    <XCircle class="size-3" />
                                {/if}
                                {check.status === 'ok' ? 'OK' : 'Gagal'}
                            </span>
                        </div>
                        <div class="px-4 py-4 flex items-center text-xs text-muted-foreground">
                            {check.date}
                        </div>
                        <div class="px-4 py-4 flex items-center text-xs text-muted-foreground">
                            {check.note}
                        </div>
                    </div>
                {/each}
            </div>

            <!-- Mobile cards -->
            <div class="md:hidden space-y-3">
                {#each phone.checks as check, i (i)}
                    <div class="rounded-2xl border border-border/60 bg-card/60 p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">
                                    {check.user.charAt(0)}
                                </div>
                                <span class="font-semibold text-sm">{check.user}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium border
                                {check.status === 'ok'
                                    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'
                                    : 'bg-destructive/10 text-destructive border-destructive/20'}">
                                {#if check.status === 'ok'}
                                    <CheckCircle2 class="size-3" />
                                {:else}
                                    <XCircle class="size-3" />
                                {/if}
                                {check.status === 'ok' ? 'OK' : 'Gagal'}
                            </span>
                        </div>
                        <div class="text-[11px] text-muted-foreground">{check.date}</div>
                        <div class="text-xs text-muted-foreground flex items-start gap-1.5">
                            <FileText class="size-3.5 shrink-0 mt-0.5" />
                            {check.note}
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </div>

</LayoutBG>
