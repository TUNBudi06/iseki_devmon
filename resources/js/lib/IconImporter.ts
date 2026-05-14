import type { Component } from 'svelte';

const pathIcons = "../../../node_modules/@lucide/svelte/dist/icons/"

interface IconModule {
    default: Component;
}

type IconLoader = () => Promise<IconModule>;

const icons = import.meta.glob<IconModule>(
    '../../../node_modules/@lucide/svelte/dist/icons/*.svelte'
)

/**
 * Dynamically load an icon component by name
 * @param name - The name of the icon (without .svelte extension)
 * @returns The icon component or null if not found
 */
export async function getIcon(name: string): Promise<Component | null> {
    const path = `${pathIcons}${name}.svelte`;

    const loader = icons[path] as IconLoader | undefined;

    if (!loader) return null;

    const mod = await loader();
    return mod.default;
}

/**
 * Get a list of all available icon names
 * @returns Array of icon names
 */
export async function getListOfIcons(): Promise<string[]> {
    return Object.keys(icons).map((path) => {
        const tmp = path.replace(pathIcons, '')
        return tmp.replace('.svelte', '')
    });
}
