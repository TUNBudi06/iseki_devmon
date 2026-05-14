<script lang="ts">
    import AppHead from '$/components/AppHead.svelte';
    import LockIcon from '@lucide/svelte/icons/lock';
    import UserIcon from '@lucide/svelte/icons/user';
    import ArrowLeftIcon from '@lucide/svelte/icons/arrow-left';
    import {
        FieldGroup,
        Field,
        FieldLabel,
        FieldDescription,
    } from '$shadcn/components/ui/field/index.js';
    import { Input } from '$shadcn/components/ui/input/index.js';
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { home } from '$routes';
    import LayoutTop from '$/components/LayoutTop.svelte';
    import { useHttp } from '@inertiajs/svelte';
    import { dashboard, loginPost } from '$routes/admin';
    import { FieldError } from '$shadcn/components/ui/field/index.ts';
    import { toast } from 'svelte-sonner';

    const form = useHttp({
        username: '',
        password: '',
    });

    function handleLogin(e: SubmitEvent) {
        e.preventDefault();
        console.log('Admin login attempt:', {
            username: form.username,
            password: form.password,
        });
        form.post(routeUrl(loginPost()), {
            onSuccess: async (params) => {
                console.log('Admin login successful', params);
                toast.success(
                    'Login berhasil! Selamat datang kembali, Admin.',
                    {
                        description:
                            'Anda telah berhasil masuk ke panel administrasi.',
                        duration: 4000,
                    },
                );
                router.visit(routeUrl(dashboard()));
            },
            onError: (errors) => {
                console.error('Admin login failed:', errors);
            },
        });
    }

    function handleBack() {
        router.get(routeUrl(home()));
    }
</script>

<LayoutTop>
    <AppHead title="Admin Login" />
    <div
        class="bg-linear-to-br from-pink-50 to-pink-100 dark:from-pink-950 dark:to-pink-900
            flex min-h-svh flex-col items-center justify-center gap-4 sm:gap-6 p-4 sm:p-6 md:p-10"
    >
        <div class="w-full max-w-sm mx-auto">
            <div class="flex flex-col gap-4 sm:gap-6">
                <!-- Header -->
                <div class="flex flex-col items-center gap-2 text-center px-2">
                    <div
                        class="flex size-10 sm:size-12 items-center justify-center rounded-lg
                            bg-pink-500/10 border border-pink-500/20"
                    >
                        <UserIcon
                            class="size-5 sm:size-6 text-pink-600 dark:text-pink-400"
                        />
                    </div>
                    <h1
                        class="text-xl sm:text-2xl font-bold text-pink-900 dark:text-pink-100"
                    >
                        Admin Login
                    </h1>
                    <FieldDescription
                        class="text-sm text-pink-700 dark:text-pink-300"
                    >
                        Masuk ke panel administrasi iseki Devmon
                    </FieldDescription>
                </div>

                <!-- Login Form -->
                <form onsubmit={handleLogin} class="px-2">
                    <FieldGroup>
                        <Field>
                            <FieldLabel for="email" class="text-sm font-medium"
                                >Username</FieldLabel
                            >
                            <Input
                                id="email"
                                type="text"
                                placeholder="admin@example.com"
                                bind:value={form.username}
                                required
                                class="text-base"
                            />
                        </Field>
                        <FieldError>{form.errors.username}</FieldError>

                        <Field>
                            <FieldLabel
                                for="password"
                                class="text-sm font-medium">Password</FieldLabel
                            >
                            <Input
                                id="password"
                                type="password"
                                placeholder="••••••••"
                                bind:value={form.password}
                                required
                                class="text-base"
                            />
                        </Field>

                        <button
                            type="submit"
                            disabled={!form.username.trim() ||
                                !form.password.trim()}
                            class="w-full h-11 text-base font-medium rounded-lg
                               bg-pink-600 hover:bg-pink-700 text-white
                               disabled:opacity-50 disabled:cursor-not-allowed
                               transition-colors duration-200 shadow-sm hover:shadow-md"
                        >
                            Masuk sebagai Admin
                        </button>
                    </FieldGroup>
                </form>

                <!-- Back button -->
                <div class="px-2">
                    <button
                        type="button"
                        onclick={handleBack}
                        class="w-full h-10 text-sm font-medium rounded-lg border border-pink-400
                           text-pink-600 dark:text-pink-400 bg-transparent
                           hover:bg-pink-600 hover:text-white hover:border-pink-600
                           dark:border-pink-500 dark:hover:bg-pink-500
                           transition-all duration-200"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <ArrowLeftIcon class="size-4" />
                            Kembali ke Beranda
                        </span>
                    </button>
                </div>

                <!-- Footer -->
                <FieldDescription
                    class="text-center text-xs text-pink-600 dark:text-pink-400 px-2"
                >
                    Aplikasi monitoring perangkat milik <span
                        class="font-semibold">iseki</span
                    >
                </FieldDescription>
            </div>
        </div>
    </div>
</LayoutTop>
