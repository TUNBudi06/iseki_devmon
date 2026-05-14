<script lang="ts">
    import SidebarProvider from '$/components/SidebarProvider.svelte';
    import { router } from '@inertiajs/svelte';
    import * as Card from '$shadcn/components/ui/card/index.js';
    import CardDashboard from '$/components/CardDashboard.svelte';
    import * as Table from '$shadcn/components/ui/table/index.js';
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
    <Card.Root class="w-full mt-5 h-160">
        <Card.Header>
            <Card.Title>All Device Log</Card.Title>
            <Card.Description>Ini adalah semua log terbaru terkait device yang ada</Card.Description>
        </Card.Header>
        <Card.Content>
            <div class="overflow-y-auto h-130">
                <Table.Root>
                    <Table.Caption>A list of your recent invoices.</Table.Caption>
                    <Table.Header>
                        <Table.Row>
                            <Table.Head class="w-40">Device ID</Table.Head>
                            <Table.Head>Device Name</Table.Head>
                            <Table.Head>Context</Table.Head>
                            <Table.Head class="text-end">Time</Table.Head>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                        <Table.Row>
                            <Table.Cell class="font-medium">THE56ZKN</Table.Cell>
                            <Table.Cell>ThinkPad E14</Table.Cell>
                            <Table.Cell>Melakukkan login pada device</Table.Cell>
                            <Table.Cell class="text-end">7.40</Table.Cell>
                        </Table.Row>
                    </Table.Body>
                </Table.Root>
            </div>
        </Card.Content>
    </Card.Root>
</SidebarProvider>
