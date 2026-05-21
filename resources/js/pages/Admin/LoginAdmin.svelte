<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Shield, Lock, User, ArrowRight, ArrowLeft } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import LayoutBG from '$/components/LayoutBG.svelte';
    import { Button } from '$shadcn/components/ui/button';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import * as Card from '$shadcn/components/ui/card';
    import { home } from '$routes';

    let username = $state('');
    let password = $state('');
    let isLoading = $state(false);
    let error = $state('');

    async function handleSubmit() {
        if (!username || !password) {
            error = 'Harap isi username dan password';
            return;
        }

        isLoading = true;
        error = '';

        try {
            await new Promise((resolve) => setTimeout(resolve, 1500));

            if (username === 'admin' && password === 'admin123') {
                router.visit(routeUrl('admin.dashboard'));
            } else {
                error = 'Username atau password salah';
            }
        } catch (err) {
            error = 'Terjadi kesalahan, silakan coba lagi';
        } finally {
            isLoading = false;
        }
    }
</script>

<LayoutBG
    class="min-h-screen bg-background flex items-center justify-center p-4"
>
    <Particles
        particleCount={200}
        particleColors={['#000000', '#ff00ae', '#ffffff']}
        particleBaseSize={100}
        speed={0.05}
        class="absolute inset-0 z-0 opacity-30"
    />

    <div class="w-full max-w-md relative z-10">
        <button
            onclick={() => router.visit(routeUrl(home()))}
            class="mb-6 flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors"
        >
            <ArrowLeft class="size-4" />
            Kembali
        </button>

        <Card.Root class="border-border/60 bg-card/80 backdrop-blur-xl">
            <Card.Header class="text-center">
                <div
                    class="mx-auto size-14 rounded-2xl bg-primary/20 flex items-center justify-center mb-4"
                >
                    <Shield class="size-7 text-primary" />
                </div>
                <Card.Title class="text-2xl font-bold">Login Admin</Card.Title>
                <Card.Description>Masuk ke panel administrator</Card.Description
                >
            </Card.Header>

            <Card.Content>
                <form onsubmit={handleSubmit} class="space-y-4">
                    <div class="space-y-2">
                        <Label for="username">Username</Label>
                        <div class="relative">
                            <User
                                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                            />
                            <Input
                                id="username"
                                bind:value={username}
                                placeholder="admin"
                                class="pl-10"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <div class="relative">
                            <Lock
                                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                            />
                            <Input
                                id="password"
                                type="password"
                                bind:value={password}
                                placeholder="••••••••"
                                class="pl-10"
                            />
                        </div>
                    </div>

                    {#if error}
                        <p class="text-sm text-red-500">{error}</p>
                    {/if}

                    <Button
                        type="submit"
                        class="w-full gap-2"
                        disabled={isLoading}
                    >
                        {#if isLoading}
                            <div
                                class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                            />
                            Memproses...
                        {:else}
                            Login
                            <ArrowRight class="size-4" />
                        {/if}
                    </Button>
                </form>
            </Card.Content>
        </Card.Root>
    </div>
</LayoutBG>
