<script lang="ts">
    import { router, useHttp } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import * as Table from '$shadcn/components/ui/table';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Separator } from '$shadcn/components/ui/separator';
    import {
        Smartphone,
        ArrowLeft,
        Plus,
        Trash2,
        Edit,
        X,
        Check,
        Search,    
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { dashboard, listDevice } from '$routes/admin';
    import type { FormDataErrors } from '@inertiajs/core';

    type Device = {
        id: string;
        name: string;
        created_at: string;
        updated_at: string;
    };

    let { devices }: { devices: Device[] } = $props();

    // ─── Add Form ──────────────────────────────────────────────────
    const addForm = useHttp({ id: '', name: '' });
    let showAddModal = $state(false);

    // ─── Edit Form ──────────────────────────────────────────────────
    const editForm = useHttp({ name: '' });
    let editTarget = $state<Device | null>(null);

    // ─── Delete ─────────────────────────────────────────────────────
    const deleteForm = useHttp({});
    let deleteTarget = $state<Device | null>(null);

    // ─── Search ─────────────────────────────────────────────────────
    let search = $state('');

    const filteredDevices = $derived.by(() => {
        if (!search.trim()) return devices;
        const q = search.toLowerCase();
        return devices.filter(
            (d) => d.id.toLowerCase().includes(q) || d.name.toLowerCase().includes(q),
        );
    });

    function goBack() {
        router.visit(routeUrl(dashboard()));
    }

    function openAdd() {
        addForm.reset();
        showAddModal = true;
    }

    function closeAdd() {
        showAddModal = false;
        addForm.reset();
    }

    function handleAdd() {
        addForm.post(routeUrl(listDevice()), {
            onSuccess: () => {
                closeAdd();
                router.reload();
                toast.success('Perangkat berhasil ditambahkan');
            },
            onError: (errors: FormDataErrors<{ id: string; name: string }>) => {
                const msg = errors.id ?? errors.name ?? 'Gagal menambahkan';
                toast.error(msg);
            },
        });
    }

    function openEdit(device: Device) {
        editForm.defaults({ name: device.name }).reset();
        editTarget = device;
    }

    function closeEdit() {
        editTarget = null;
        editForm.reset();
    }

    function handleEdit() {
        if (!editTarget) return;
        editForm.put(routeUrl(listDevice.update({ id: editTarget.id })), {
            onSuccess: () => {
                closeEdit();
                router.reload();
                toast.success('Perangkat berhasil diperbarui');
            },
            onError: (errors: FormDataErrors<{ name: string }>) => {
                toast.error(errors.name ?? 'Gagal memperbarui');
            },
        });
    }

    function openDelete(device: Device) {
        deleteTarget = device;
    }

    function closeDelete() {
        deleteTarget = null;
    }

    function handleDelete() {
        if (!deleteTarget) return;
        deleteForm.delete(routeUrl(listDevice.destroy({ id: deleteTarget.id })), {
            onSuccess: () => {
                closeDelete();
                router.reload();
                toast.success('Perangkat berhasil dihapus');
            },
            onError: () => {
                toast.error('Gagal menghapus perangkat');
            },
        });
    }
</script>

<div class="min-h-screen bg-background">
    <!-- Header -->
    <div
        class="sticky top-0 z-40 border-b border-border/60 bg-card/80 backdrop-blur-xl"
    >
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" onclick={goBack}>
                    <ArrowLeft class="size-5" />
                </Button>
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-xl bg-blue-500/20 flex items-center justify-center">
                            <Smartphone class="size-5 text-blue-400" />
                        </div>
                        <h1 class="text-xl font-bold tracking-tight">List Device</h1>
                    </div>
                    <p class="text-xs text-muted-foreground ml-0.5 mt-0.5">
                        {devices.length} perangkat terdaftar
                    </p>
                </div>
            </div>
            <Button onclick={openAdd} class="gap-2 shadow-lg shadow-primary/20">
                <Plus class="size-4" />
                Tambah Perangkat
            </Button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-6 space-y-6">
        <!-- Search -->
        <div class="relative max-w-md">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
            <Input
                bind:value={search}
                placeholder="Cari ID atau nama perangkat..."
                class="pl-10 h-10 bg-card/50 border-border/60"
            />
        </div>

        <!-- Table Card -->
        <Card.Root class="border-border/60 bg-card/60 backdrop-blur-xl overflow-hidden shadow-xl">
            {#if filteredDevices.length === 0}
                <div class="p-16 text-center">
                    <div class="size-14 rounded-2xl bg-muted/50 flex items-center justify-center mx-auto mb-4">
                        <Smartphone class="size-7 text-muted-foreground/50" />
                    </div>
                    <p class="text-muted-foreground font-medium">
                        {search ? 'Tidak ada perangkat yang cocok' : 'Belum ada perangkat terdaftar'}
                    </p>
                    {#if !search}
                        <Button variant="outline" size="sm" onclick={openAdd} class="mt-4 gap-2">
                            <Plus class="size-3.5" />
                            Tambah Perangkat
                        </Button>
                    {/if}
                </div>
            {:else}
                <div class="overflow-x-auto">
                    <Table.Root>
                        <Table.Header>
                            <Table.Row class="border-border/40 bg-muted/30">
                                <Table.Head class="font-semibold text-xs uppercase tracking-wider">Device ID</Table.Head>
                                <Table.Head class="font-semibold text-xs uppercase tracking-wider">Name</Table.Head>
                                <Table.Head class="font-semibold text-xs uppercase tracking-wider">Created</Table.Head>
                                <Table.Head class="text-right font-semibold text-xs uppercase tracking-wider">Actions</Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each filteredDevices as device (device.id)}
                                <Table.Row class="border-border/30 hover:bg-muted/20 transition-colors">
                                    <Table.Cell>
                                        <Badge variant="outline" class="font-mono text-xs bg-primary/5 border-primary/20">
                                            {device.id}
                                        </Badge>
                                    </Table.Cell>
                                    <Table.Cell class="font-medium">{device.name}</Table.Cell>
                                    <Table.Cell class="text-sm text-muted-foreground">
                                        {new Date(device.created_at).toLocaleDateString('id-ID', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                        })}
                                    </Table.Cell>
                                    <Table.Cell class="text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="size-8 text-muted-foreground hover:text-primary hover:bg-primary/10"
                                                onclick={() => openEdit(device)}
                                            >
                                                <Edit class="size-3.5" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="size-8 text-muted-foreground hover:text-red-400 hover:bg-red-500/10"
                                                onclick={() => openDelete(device)}
                                            >
                                                <Trash2 class="size-3.5" />
                                            </Button>
                                        </div>
                                    </Table.Cell>
                                </Table.Row>
                            {/each}
                        </Table.Body>
                    </Table.Root>
                </div>
            {/if}
        </Card.Root>
    </div>
</div>

<!-- ────────── Add Modal ────────── -->
{#if showAddModal}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <Plus class="size-5 text-blue-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Tambah Perangkat</Card.Title>
                        <Card.Description>Masukkan ID dan nama perangkat baru</Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="add-id" class="text-sm font-medium">Device ID</Label>
                    <Input id="add-id" bind:value={addForm.id} placeholder="Contoh: DEV-011" class="h-10" />
                    {#if addForm.errors.id}
                        <p class="text-xs text-red-400">{addForm.errors.id}</p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="add-name" class="text-sm font-medium">Device Name</Label>
                    <Input id="add-name" bind:value={addForm.name} placeholder="Contoh: Samsung Galaxy A55" class="h-10" />
                    {#if addForm.errors.name}
                        <p class="text-xs text-red-400">{addForm.errors.name}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeAdd} disabled={addForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleAdd} disabled={addForm.processing || !addForm.id.trim() || !addForm.name.trim()} class="gap-2">
                    {#if addForm.processing}
                        <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        Menyimpan...
                    {:else}
                        <Check class="size-4" />
                        Simpan
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}

<!-- ────────── Edit Modal ────────── -->
{#if editTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <Edit class="size-5 text-emerald-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Edit Perangkat</Card.Title>
                        <Card.Description>
                            <span class="font-mono text-xs">{editTarget.id}</span>
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="edit-name" class="text-sm font-medium">Device Name</Label>
                    <Input id="edit-name" bind:value={editForm.name} placeholder="Nama perangkat" class="h-10" />
                    {#if editForm.errors.name}
                        <p class="text-xs text-red-400">{editForm.errors.name}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeEdit} disabled={editForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleEdit} disabled={editForm.processing || !editForm.name.trim()} class="gap-2">
                    {#if editForm.processing}
                        <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        Menyimpan...
                    {:else}
                        <Check class="size-4" />
                        Simpan
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}

<!-- ────────── Delete Confirmation ────────── -->
{#if deleteTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-red-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                        <Trash2 class="size-5 text-red-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Hapus Perangkat</Card.Title>
                        <Card.Description>
                            Apakah Anda yakin ingin menghapus
                            <span class="font-medium text-foreground">{deleteTarget.name}</span>?
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Card.Footer class="flex justify-end gap-3">
                <Button variant="outline" onclick={closeDelete} disabled={deleteForm.processing}>
                    Batal
                </Button>
                <Button variant="destructive" onclick={handleDelete} disabled={deleteForm.processing} class="gap-2">
                    {#if deleteForm.processing}
                        <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        Menghapus...
                    {:else}
                        <Trash2 class="size-4" />
                        Hapus
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}
