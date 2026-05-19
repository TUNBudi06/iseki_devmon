<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { home } from '$routes';
    import { Button } from '$shadcn/components/ui/button';
    import { ArrowRightCircle, LucideHome } from '@lucide/svelte';
    import { listPhone } from '$routes/phone';
    import * as Card from '$shadcn/components/ui/card';
    import Particles from '$shadcn/components/Particles.svelte';
    import * as Carousel from '$shadcn/components/ui/carousel';
    import { onMount, onDestroy } from 'svelte';
    import gsap from 'gsap';
    import Lenis from 'lenis'
    import ScrollTrigger from 'gsap/ScrollTrigger';
    import Autoplay from "embla-carousel-autoplay";

    let lenis: Lenis = $state();
    let progress = $state(0);
    let containerRef = $state();
    gsap.registerPlugin(ScrollTrigger);

    onMount(() => {
        lenis = new Lenis({
            duration: 0.6,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -4 * t)),
            smoothWheel: true,
            smoothTouch: false,
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);


        ScrollTrigger.create({
            trigger: containerRef,   // outer wrapper
            start: 'top top',        // mulai saat top wrapper = top viewport
            end: `+=${window.innerHeight}px`, // end setelah N×100vh
            onUpdate: (self) => {
                progress = self.progress; // 0 sampai 1
                console.log('Progress:', progress); // cek di console dulu
            }
        });
    });

    onDestroy(() => {
        lenis?.destroy();
    });
</script>

<LayoutBG>
    <Navbar class="border-b-2 border-border fixed top-0 z-50 w-full">
        <div>
            <div
                class="text-lg font-semibold tracking-tight text-gradient-pink"
            >
                DevControl
            </div>
            <div class="text-xs text-muted-foreground">
                Device Management System
            </div>
        </div>
        <Button
            size="default"
            variant="ghost"
            class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
            onclick={() => router.visit(routeUrl(home()))}
        >
            Home
            <LucideHome class="size-4" />
        </Button>
        <Button
            size="sm"
            variant="ghost"
            class="border border-border/60 bg-card/40 backdrop-blur-xl hover:bg-primary/10 gap-2"
            onclick={() => router.visit(routeUrl(listPhone()))}
        >
            Kembali
            <ArrowRightCircle class="size-4" />
        </Button>
    </Navbar>
    <div class="relative" style="height: 400vh" bind:this={containerRef}>
        <div class="sticky top-15 h-screen bg-primary/5 flex">
            <div class="w-2/3 justify-center items-center flex bg-black">
                <Carousel.Root class="w-full max-w-2xl" plugins={[
                    Autoplay({ delay: 2000, stopOnInteraction: false })
                ]}>
                    <Carousel.Content>
                        {#each Array(5) as _, i (i)}
                            <Carousel.Item>
                                <div class="p-1">
                                    <Card.Root>
                                        <Card.Content
                                            class="flex aspect-square items-center justify-center p-6"
                                        >
                                            <span class="text-4xl font-semibold"
                                                >{i + 1}</span
                                            >
                                        </Card.Content>
                                    </Card.Root>
                                </div>
                            </Carousel.Item>
                        {/each}
                    </Carousel.Content>
                </Carousel.Root>
            </div>
        </div>
    </div>
</LayoutBG>
