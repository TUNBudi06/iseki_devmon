<script lang="ts">
    import SidebarProvider from '$/components/SidebarProvider.svelte';
    import { router } from '@inertiajs/svelte';
    import * as Card from '$shadcn/components/ui/card/index.js';
    import CardDashboard from '$/components/CardDashboard.svelte';
    import { getIcon } from '$lib/IconImporter.ts';
    import { onMount } from 'svelte';
    import { useInterval } from 'runed';

    // const interval = useInterval(10000,{
    //     callback:()=> {
    //         router.reload({only:['devices']})
    //     }
    // });
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
</SidebarProvider>
