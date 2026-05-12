import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    base: '/iseki_kyarcom/public/build',
    build: {
        rollupOptions: {
            output: {
                manualChunks:{
                    'vendor-svelte': ['svelte', 'svelte/animate', 'svelte/easing', 'svelte/motion', 'svelte/store', 'svelte/transition'],
                    'vendor-icons': ['@lucide/svelte'],
                    'vendor-shadcn': ['bits-ui'],
                },
                chunkFileNames: (chunkInfo) => {
                    if (chunkInfo.name.startsWith('vendor')) {
                        const name = chunkInfo.name.split('-')[1];

                        return 'vendor/'+name+'.[hash].js';
                    }
                    return 'assets/[name].[hash].js';
                }
            }
        }
    },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        svelte({
            compilerOptions: {
                dev: process.env.NODE_ENV !== 'production',
            },
        }),
        wayfinder({
            formVariants: true,
        }),
    ],
    resolve: {
        alias: {
            '$': path.resolve(__dirname, './resources/js'),
            '$shadcn': path.resolve(__dirname, './resources/js/shadcn'),
            '$routes': path.resolve(__dirname, './resources/js/routes'),
            '$lib': path.resolve(__dirname, './resources/js/lib'),
        }
    },
});
