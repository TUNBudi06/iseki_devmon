<script lang="ts">
    import SidebarProvider from "$/components/SidebarProvider.svelte";
    import * as Card from "$shadcn/components/ui/card/index.js"
    import CardDashboard from "$/components/CardDashboard.svelte";
</script>

<SidebarProvider>
    <div class="flex flex-col lg:flex-row gap-4 px-4 w-full h-40">
        <CardDashboard title="Total Device" value={20} comment="Total Device" />
        <CardDashboard title="Total Active Device" value={14} comment="Total Active Device" />
        <CardDashboard title="Total Deactive Device" value={6} comment="Total Deactive Device" />
    </div>

</SidebarProvider>
