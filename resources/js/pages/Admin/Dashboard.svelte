<script lang="ts">
    import SidebarProvider from '$/components/SidebarProvider.svelte';
    import * as Card from '$shadcn/components/ui/card/index.js';
    import CardDashboard from '$/components/CardDashboard.svelte';
    import {getIcon,getListOfIcons} from "$lib/IconImporter.ts";

    getListOfIcons().then((icons) => {
        console.log(icons)
    })

    const icon = getIcon('activity')
    console.log(icon)
</script>

<SidebarProvider>
    <div class="flex flex-col lg:flex-row gap-4 px-4 w-full h-40">
        <CardDashboard
            title="Total Device"
            value={20}
            comment="Ini adalah total dari device yang terdaftar"
        />
        <CardDashboard
            title="Total Active Device"
            value={14}
            comment="jumlah device yang aktif digunakan"
        />
        <CardDashboard
            title="Total Deactive Device"
            value={6}
            comment="jumlah device yang tidak aktif digunakan"
        />
    </div>
    {#await icon}
        <span>loading...</span>
    {:then Icon}
        <svelte:component this={Icon} />
    {/await}

</SidebarProvider>
