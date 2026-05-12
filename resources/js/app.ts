import type { ResolvedComponent } from '@inertiajs/svelte';
import { createInertiaApp } from '@inertiajs/svelte';
import { initRouteHelper } from '@tunbudi06/inertia-route-helper/init';
import { hydrate, mount } from 'svelte';
import './bootstrap';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name: string) => {
        const pages = import.meta.glob<ResolvedComponent>(
            './pages/**/*.svelte',
        );
        return pages[`./pages/${name}.svelte`]();
    },
    setup({ el, App, props }) {
        console.log('Inertia props:', props);
        initRouteHelper(props);
        if (!el) {
            return;
        }

        if (el.dataset.serverRendered === 'true') {
            hydrate(App, { target: el, props });
        } else if (el) {
            mount(App, { target: el, props });
        }
    },
    progress: {
        color: '#4B5563',
    },
});
