<script lang="ts">
    import {
        LogOut,
        MonitorSmartphone,
        HouseIcon,
        ShieldAlert,
        UserStar,
        Settings,
        Logs,
    } from '@lucide/svelte';

    import * as Sidebar from '$shadcn/components/ui/sidebar/index.js';
    import { isCurrentRoute, routeUrl } from '@tunbudi06/inertia-route-helper';
    import { dashboard,logoutGet } from '$routes/admin';
    import {router} from "@inertiajs/svelte";

    // Menu items.
    const items = [
        {
            title: 'Home',
            url: routeUrl(dashboard()),
            icon: HouseIcon,
        },
        {
            title: 'List Devices',
            url: '#',
            icon: MonitorSmartphone,
        },
        {
            title: 'Verify Device',
            url: '#',
            icon: ShieldAlert,
        },
        {
            title: 'List Logs',
            url: '#',
            icon: Logs,
        },
    ];

    const admins = [
        {
            title: 'List Admins',
            url: '#',
            icon: UserStar,
        },
        {
            title: 'Pengaturan',
            url: '#',
            icon: Settings,
        },
    ];
</script>

<Sidebar.Root>
    <Sidebar.Content>
        <Sidebar.Group>
            <Sidebar.GroupLabel>Management Device</Sidebar.GroupLabel>
            <Sidebar.GroupContent>
                <Sidebar.Menu>
                    {#each items as item (item.title)}
                        <Sidebar.MenuItem>
                            <Sidebar.MenuButton
                                isActive={isCurrentRoute(item.url)}
                            >
                                {#snippet child({ props })}
                                    <a href={item.url} {...props}>
                                        <item.icon />
                                        <span>{item.title}</span>
                                    </a>
                                {/snippet}
                            </Sidebar.MenuButton>
                        </Sidebar.MenuItem>
                    {/each}
                </Sidebar.Menu>
            </Sidebar.GroupContent>
        </Sidebar.Group>
        <Sidebar.Group>
            <Sidebar.GroupLabel>Management Admins</Sidebar.GroupLabel>
            <Sidebar.GroupContent>
                <Sidebar.Menu>
                    {#each admins as item (item.title)}
                        <Sidebar.MenuItem>
                            <Sidebar.MenuButton>
                                {#snippet child({ props })}
                                    <a href={item.url} {...props}>
                                        <item.icon />
                                        <span>{item.title}</span>
                                    </a>
                                {/snippet}
                            </Sidebar.MenuButton>
                        </Sidebar.MenuItem>
                    {/each}
                </Sidebar.Menu>
            </Sidebar.GroupContent>
        </Sidebar.Group>
    </Sidebar.Content>
    <Sidebar.Footer>
        <Sidebar.Menu>
            <Sidebar.MenuItem>
                <Sidebar.MenuButton>
                    {#snippet child({ props })}
                        <a href={routeUrl(logoutGet())} {...props}>
                            <h2 class="text-xl ps-2 me-auto">Logout</h2>
                            <LogOut />
                        </a>
                    {/snippet}
                </Sidebar.MenuButton>
            </Sidebar.MenuItem>
        </Sidebar.Menu>
    </Sidebar.Footer>
</Sidebar.Root>
