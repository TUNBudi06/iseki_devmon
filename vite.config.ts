import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    base: '/iseki_devmon/public/build',
    //i disable this make heayv load chunk since the bundling really big
    // build: {
    //     rolldownOptions: {
    //         output: {
    //             // manualChunks:{
    //             //     'vendor-icons': ['@lucide/svelte'],
    //             //     'vendor-shadcn': ['bits-ui'],
    //             // },
    //             manualChunks: (moduleId,meta) => {
    //                 if (moduleId.includes('node_modules/svelte/')) {
    //                     // console.log(moduleId)
    //                     return 'vendor-svelte';
    //                 }
    //                 if (moduleId.includes('node_modules/@lucide/svelte/')) {
    //                     // console.log(moduleId)
    //                     return 'vendor-icons';
    //                 }
    //                 if (moduleId.includes('node_modules/bits-ui/')) {
    //                     // console.log(moduleId)
    //                     console.log(meta.getModuleInfo(moduleId))
    //                     return 'vendor-UI';
    //                 }
    //                 console.log('module:' + moduleId)
    //             },
    //             chunkFileNames: (chunkInfo) => {
    //                 if (chunkInfo.name.startsWith('vendor')) {
    //                     const name = chunkInfo.name.split('-')[1];
    //
    //                     return 'vendor/'+name+'.[hash].js';
    //                 }
    //                 return 'assets/[name].[hash].js';
    //             }
    //         }
    //     }
    // },
    build:{
      rolldownOptions:{
          output:{
              manualChunks: (id,meta) => {
                  if (id.includes('node_modules/@lucide/svelte/')) {
                      const match = id.match(/icons\/([^/]+)/)

                      if (match) {
                          return `icons/${match[1]}`
                      }
                  }
                  if(id.includes('IconImporter')){
                      return 'icons/default'
                  }
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
            actions:false
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
