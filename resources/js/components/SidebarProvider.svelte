<script lang="ts">
    import * as Sidebar from '$shadcn/components/ui/sidebar/index.ts'
    import SidebarApp from "$/components/SidebarApp.svelte";
    import {SidebarTrigger} from "$shadcn/components/ui/sidebar";
    import {Separator} from "$shadcn/components/ui/separator";
    import * as Breadcrumb from "$shadcn/components/ui/breadcrumb";
    let { children } = $props();
</script>
<Sidebar.Provider>
    <SidebarApp />
    <main class="w-full">
        <header class="flex h-16 shrink-0 items-center gap-2 border-b">
            <div class="flex items-center gap-2 px-3">
                <SidebarTrigger />
            </div>
        </header>
        <div class="p-2">
            {@render children?.()}
        </div>
    </main>
</Sidebar.Provider>
