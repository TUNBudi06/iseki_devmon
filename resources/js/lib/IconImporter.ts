const pathIcons = "../../../node_modules/@lucide/svelte/dist/icons/"

const icons = import.meta.glob(
    '../../../node_modules/@lucide/svelte/dist/icons/*.svelte'
)

export async function getIcon(name: string) {
    const path = `${pathIcons}${name}.svelte`;

    const loader = icons[path];

    if (!loader) return null;

    const mod = await loader();
    return mod.default;
}

export async function getListOfIcons() {
    return Object.keys(icons).map((path) => {
        const tmp =  path.replace(pathIcons, '')
        return tmp.replace('.svelte', '')
    });
}
