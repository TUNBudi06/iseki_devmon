<script lang="ts">
    import { onMount, onDestroy } from 'svelte';
    import { fade, fly } from 'svelte/transition';
    import { cubicOut } from 'svelte/easing';
    import gsap from 'gsap';
    import { ScrollTrigger } from 'gsap/ScrollTrigger';
    import Lenis from 'lenis';

    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { home } from '$routes';
    import { Button } from '$shadcn/components/ui/button';
    import { ArrowRightCircle, Smartphone, Cpu, BatteryCharging, Wifi } from '@lucide/svelte';
    import type { Component } from 'svelte';

    gsap.registerPlugin(ScrollTrigger);

    type Feature = {
        id: number;
        title: string;
        desc: string;
        detail: string;
        icon: Component;
        accent: string;
    };

    const features: Feature[] = [
        {
            id: 1,
            title: 'Titanium Frame',
            desc: 'Struktur aerospace-grade yang lebih ringan dan tahan lama.',
            detail: 'Paduan Ti-6Al-4V, rasio kekuatan-terhadap-berat tertinggi di kelasnya.',
            icon: Smartphone,
            accent: '#38bdf8',
        },
        {
            id: 2,
            title: 'Neural Engine',
            desc: 'Chipset 3nm dengan performa AI generatif on-device.',
            detail: '45 TOPS, inferensi model bahasa besar secara lokal tanpa cloud.',
            icon: Cpu,
            accent: '#a78bfa',
        },
        {
            id: 3,
            title: 'Silicon-Carbon Battery',
            desc: 'Manajemen daya cerdas untuk daya tahan 48 jam.',
            detail: 'Kepadatan energi 40% lebih tinggi dari Li-Ion konvensional.',
            icon: BatteryCharging,
            accent: '#34d399',
        },
        {
            id: 4,
            title: 'Wi-Fi 7 Ready',
            desc: 'Latensi ultra-rendah di bawah 1ms.',
            detail: 'Throughput agregat 46 Gbps via Multi-Link Operation.',
            icon: Wifi,
            accent: '#fb923c',
        }
    ];

    let activeIndex = $state(0);
    let scrollProgress = $state(0);

    let containerRef = $state<HTMLElement | null>(null);
    let textContainerRef = $state<HTMLElement | null>(null);
    let glowRef = $state<HTMLElement | null>(null);
    let heroRef = $state<HTMLElement | null>(null);

    let tl: gsap.core.Timeline;
    let lenis: Lenis;

    onMount(() => {
        lenis = new Lenis({
            duration: 1.2,
            easing: (t: number) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
            touchMultiplier: 2,
        });

        lenis.on('scroll', ScrollTrigger.update);
        gsap.ticker.add((time) => lenis.raf(time * 1000));
        gsap.ticker.lagSmoothing(0);

        const sectionH = window.innerHeight;

        // Hero exit animation
        if (heroRef) {
            gsap.to(heroRef, {
                opacity: 0,
                y: -60,
                ease: 'power2.in',
                scrollTrigger: {
                    trigger: heroRef,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: true,
                }
            });
        }

        // Main scrollytelling timeline
        tl = gsap.timeline({
            scrollTrigger: {
                trigger: containerRef,
                start: 'top top',
                end: `+=${sectionH * features.length}px`,
                scrub: 1,
                onUpdate: (self) => {
                    scrollProgress = self.progress;
                    activeIndex = Math.min(
                        Math.floor(self.progress * features.length),
                        features.length - 1
                    );
                },
            }
        });

        // Slide text up
        if (textContainerRef) {
            tl.to(textContainerRef, {
                y: -(sectionH * (features.length - 1)),
                ease: 'none',
                duration: 1,
            }, 0);
        }

        // Blur/fade per text section
        const sections = textContainerRef?.querySelectorAll('[data-feature-section]');
        sections?.forEach((section, i) => {
            const start = i / features.length;
            const end = (i + 0.85) / features.length;

            tl.fromTo(section,
                { opacity: 0.1, filter: 'blur(8px)', y: 30 },
                { opacity: 1, filter: 'blur(0px)', y: 0, ease: 'power2.out', duration: 0.3 / features.length },
                start
            );

            if (i < features.length - 1) {
                tl.to(section,
                    { opacity: 0.1, filter: 'blur(8px)', y: -30, ease: 'power2.in', duration: 0.3 / features.length },
                    end
                );
            }
        });
    });

    onDestroy(() => {
        ScrollTrigger.getAll().forEach(t => t.kill());
        tl?.kill();
        lenis?.destroy();
    });

    let progressPct = $derived(Math.round(scrollProgress * 100));
</script>

<LayoutBG>
    <!-- Navbar -->
    <Navbar class="z-50 border-b border-border/30 bg-background/70 backdrop-blur-xl">
        <div class="flex items-center gap-3">
            <span class="text-base font-bold tracking-tight">Galaxy S26 Ultra</span>
            <span class="hidden text-border md:inline">·</span>
            <span class="hidden text-xs text-muted-foreground md:block">Experience</span>
        </div>
        <Button
            size="sm"
            variant="ghost"
            class="gap-2 text-muted-foreground hover:text-foreground"
            onclick={() => router.visit(routeUrl(home()))}
        >
            Kembali
            <ArrowRightCircle class="size-4" />
        </Button>
    </Navbar>

    <!-- Hero -->
    <section
        bind:this={heroRef}
        class="relative flex min-h-[90vh] flex-col items-center justify-center overflow-hidden px-6 text-center"
    >
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/10 via-background to-background"></div>

        <p class="mb-6 font-mono text-xs font-semibold uppercase tracking-[0.3em] text-muted-foreground">
            Samsung Galaxy S26 Ultra
        </p>
        <h1 class="mb-8 bg-gradient-to-b from-white via-white to-white/40 bg-clip-text text-6xl font-bold tracking-tighter text-transparent md:text-8xl">
            Beyond<br />
            <span class="bg-gradient-to-r from-sky-400 via-violet-400 to-pink-400 bg-clip-text text-transparent">
                Real.
            </span>
        </h1>
        <p class="max-w-xl text-lg leading-relaxed text-muted-foreground">
            Empat pilar inovasi yang mendefinisikan ulang batas teknologi mobile.
        </p>
        <div class="mt-12 animate-bounce text-muted-foreground/60">
            <svg class="mx-auto size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </section>

    <!-- SCROLLYTELLING CONTAINER -->
    <!-- height tetap inline style karena dynamic dari features.length -->
    <div
        bind:this={containerRef}
        class="relative"
        style="height: {(features.length + 1) * 100}vh"
    >
        <div class="sticky top-0 h-screen w-full overflow-hidden bg-black">

            <!-- Glow: warna accent dynamic, tidak bisa pure Tailwind -->
            <div
                class="pointer-events-none absolute inset-0 transition-all duration-700"
                style="background: radial-gradient(ellipse at 60% 40%, {features[activeIndex].accent}18 0%, transparent 65%);"
            ></div>

            <!-- Grid texture -->
            <div
                class="pointer-events-none absolute inset-0 opacity-[0.025]"
                style="background-image: linear-gradient(rgba(255,255,255,0.15) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.15) 1px, transparent 1px); background-size: 50px 50px;"
            ></div>

            <!-- Main layout -->
            <div class="relative z-10 mx-auto flex h-full max-w-7xl flex-col items-center px-4 md:px-8 lg:flex-row">

                <!-- LEFT: Icon visual -->
                <div class="flex h-[45vh] w-full items-center justify-center lg:h-full lg:w-1/2">
                    {#key activeIndex}
                        {@const current = features[activeIndex]}
                        {@const Icon = current.icon}
                        <div
                            in:fly={{ y: 40, duration: 600, easing: cubicOut }}
                            out:fade={{ duration: 200 }}
                            class="flex flex-col items-center"
                        >
                            <!-- Card: rounded, border, size pakai Tailwind. Glow dynamic -->
                            <div
                                class="flex h-64 w-64 items-center justify-center rounded-[3rem] border border-white/10 md:h-80 md:w-80 lg:h-96 lg:w-96"
                                style="
                                    background: linear-gradient(145deg, rgba(255,255,255,0.07) 0%, rgba(255,255,255,0.02) 100%);
                                    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05), 0 0 100px {current.accent}25;
                                "
                            >
                                <Icon
                                    class="size-28 transition-transform duration-500 hover:scale-105 md:size-36 lg:size-44"
                                    style="color: {current.accent};"
                                />
                            </div>

                            <!-- Counter badge -->
                            <div
                                class="mt-6 rounded-full border px-4 py-1.5 font-mono text-xs font-semibold uppercase tracking-widest"
                                style="color: {current.accent}; border-color: {current.accent}40; background: {current.accent}10;"
                            >
                                {String(current.id).padStart(2, '0')} / {String(features.length).padStart(2, '0')}
                            </div>

                            <!-- Mobile title only -->
                            <h3 class="mt-4 text-center text-xl font-bold text-white lg:hidden">
                                {current.title}
                            </h3>
                        </div>
                    {/key}
                </div>

                <!-- RIGHT: Text (GSAP slides ke atas) -->
                <div class="hidden h-full w-full overflow-hidden lg:block lg:w-1/2 lg:pl-16 xl:pl-24">
                    <div bind:this={textContainerRef} class="will-change-transform">
                        {#each features as feature, i}
                            <div
                                data-feature-section={i}
                                class="flex h-screen flex-col justify-center py-12"
                            >
                                <p
                                    class="mb-4 font-mono text-xs font-semibold uppercase tracking-[0.25em]"
                                    style="color: {feature.accent};"
                                >
                                    Fitur {String(i + 1).padStart(2, '0')}
                                </p>

                                <h2 class="mb-5 text-4xl font-bold leading-tight text-white xl:text-5xl">
                                    {feature.title}
                                </h2>

                                <!-- Divider warna dynamic -->
                                <div class="mb-7 h-1 w-14 rounded-full" style="background: {feature.accent};"></div>

                                <p class="mb-4 max-w-md text-lg leading-relaxed text-gray-300">
                                    {feature.desc}
                                </p>

                                <p class="max-w-md font-mono text-sm leading-relaxed text-gray-500">
                                    {feature.detail}
                                </p>

                                <div class="mt-8">
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-medium"
                                        style="background: {feature.accent}10; color: {feature.accent}; border-color: {feature.accent}30;"
                                    >
                                        <span class="size-1.5 rounded-full" style="background: {feature.accent};"></span>
                                        Teknologi Premium
                                    </span>
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>
            </div>

            <!-- Progress bar -->
            <div class="absolute bottom-8 left-1/2 h-px w-48 -translate-x-1/2 overflow-hidden rounded-full bg-white/10">
                <div
                    class="h-full rounded-full transition-[width] duration-100"
                    style="width: {progressPct}%; background: {features[activeIndex].accent};"
                ></div>
            </div>

            <!-- Scroll % -->
            <div class="absolute bottom-8 right-8 hidden flex-col items-end gap-2 font-mono text-xs text-muted-foreground/50 lg:flex">
                <span>{progressPct}%</span>
                <div class="h-10 w-px bg-gradient-to-b from-transparent via-white/20 to-transparent"></div>
                <span>SCROLL</span>
            </div>
        </div>
    </div>
    <!-- END SCROLLYTELLING -->

    <!-- CTA -->
    <section class="relative z-20 bg-background py-32">
        <div class="container mx-auto px-4 text-center">
            <p class="mb-4 text-sm text-muted-foreground">Tersedia mulai Q2 2026</p>
            <h3 class="mb-10 text-3xl font-bold text-foreground md:text-5xl">
                Siap mengalami masa depan?
            </h3>
            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <Button size="lg" class="h-14 rounded-full px-10 text-lg font-semibold">
                    Pre-order Sekarang
                </Button>
                <Button size="lg" variant="outline" class="h-14 rounded-full px-10 text-lg">
                    Pelajari Spesifikasi
                </Button>
            </div>

            <!-- Feature recap grid -->
            <div class="mx-auto mt-24 grid max-w-4xl grid-cols-2 gap-6 md:grid-cols-4">
                {#each features as feature}
                    {@const Icon = feature.icon}
                    <div class="group rounded-2xl border border-border/40 bg-card/40 p-6 text-center backdrop-blur-sm transition-colors hover:border-primary/40">
                        <Icon
                            class="mx-auto mb-3 size-8 transition-transform duration-300 group-hover:scale-110"
                            style="color: {feature.accent};"
                        />
                        <p class="text-xs font-medium text-foreground">{feature.title}</p>
                    </div>
                {/each}
            </div>
        </div>
    </section>
</LayoutBG>
