<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import * as Table from '$shadcn/components/ui/table';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import {
        Smartphone,
        ArrowLeft,
        Plus,
        Trash2,
        Edit,
        X,
        Check,
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { dashboard, listDevice } from '$routes/admin';

    type Device = {
        id: string;
        name: string;
        created_at: string;
        updated_at: string;
    };

    let { devices }: { devices: Device[] } = $props();

    let showAddModal = $state(false);
    let newId = $state('');
    let newName = $state('');
    let isSubmitting = $state(false);

    let editingId = $state<string | null>(null);
    let editName = $state('');
    let editSubmitting = $state(false);

    let deleteConfirmId = $state<string | null>(null);

    function goBack() {
        router.visit(routeUrl(dashboard()));
    }

    async function handleAdd() {
        if (!newId.trim() || !newName.trim()) return;
        isSubmitting = true;

        router.post(routeUrl(listDevice()), {
            id: newId,
            name: newName,
            preserveScroll: true,
            onSuccess: () => {
                showAddModal = false;
                newId = '';
                newName = '';
                toast.success('Perangkat berhasil ditambahkan');
            },
            onError: () => {
                toast.error('Gagal menambahkan perangkat');
            },
            onFinish: () => {
                isSubmitting = false;
            },
        });
    }

    function startEdit(device: Device) {
        editingId = device.id;
        editName = device.name;
    }

    function cancelEdit() {
        editingId = null;
        editName = '';
    }

    function handleUpdate(id: string) {
        if (!editName.trim()) return;
        editSubmitting = true;

        router.put(
            routeUrl(listDevice.update({ id })),
            { name: editName },
            {
                preserveScroll: true,
                onSuccess: () => {
                    editingId = null;
                    editName = '';
                    toast.success('Perangkat berhasil diperbarui');
                },
                onError: () => {
                    toast.error('Gagal memperbarui perangkat');
                },
                onFinish: () => {
                    editSubmitting = false;
                },
            },
        );
    }

    function handleDelete(id: string) {
        router.delete(routeUrl(listDevice.destroy({ id })), {
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirmId = null;
                toast.success('Perangkat berhasil dihapus');
            },
            onError: () => {
                toast.error('Gagal menghapus perangkat');
            },
        });
    }
</script>

<div class="min-h-screen bg-background p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <Button variant="ghost" size="icon" onclick={goBack}>
                <ArrowLeft class="size-5" />
            </Button>
            <div>
                <div class="flex items-center gap-2">
                    <Smartphone class="size-6 text-blue-400" />
                    <h1 class="text-2xl font-bold">List Device</h1>
                </div>
                <p class="text-sm text-muted-foreground mt-1">
                    Daftar, tambah, dan hapus perangkat
                </p>
            </div>
        </div>

        <Button onclick={() => (showAddModal = true)} class="gap-2">
            <Plus class="size-4" />
            Tambah Perangkat
        </Button>
    </div>

    <!-- Add Modal -->
    {#if showAddModal}
        <div
            class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <Card.Root class="w-full max-w-md border-border/60 bg-card backdrop-blur-xl">
                <Card.Header>
                    <Card.Title>Tambah Perangkat Baru</Card.Title>
                    <Card.Description
                        >Masukkan ID dan nama perangkat</Card.Description
                    >
                </Card.Header>
                <Card.Content class="space-y-4">
                    <div class="space-y-2">
                        <Label for="device-id">Device ID</Label>
                        <Input
                            id="device-id"
                            bind:value={newId}
                            placeholder="Contoh: DEV-011"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="device-name">Device Name</Label>
                        <Input
                            id="device-name"
                            bind:value={newName}
                            placeholder="Contoh: Samsung Galaxy A55"
                        />
                    </div>
                </Card.Content>
                <Card.Footer class="flex justify-end gap-3">
                    <Button
                        variant="outline"
                        onclick={() => {
                            showAddModal = false;
                            newId = '';
                            newName = '';
                        }}
                    >
                        Batal
                    </Button>
                    <Button
                        onclick={handleAdd}
                        disabled={isSubmitting || !newId.trim() || !newName.trim()}
                    >
                        {#if isSubmitting}
                            <div
                                class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                            />
                            Menyimpan...
                        {:else}
                            Simpan
                        {/if}
                    </Button>
                </Card.Footer>
            </Card.Root>
        </div>
    {/if}

    <!-- Delete Confirmation -->
    {#if deleteConfirmId}
        <div
            class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <Card.Root class="w-full max-w-md border-border/60 bg-card backdrop-blur-xl">
                <Card.Header>
                    <Card.Title>Hapus Perangkat</Card.Title>
                    <Card.Description
                        >Apakah Anda yakin ingin menghapus perangkat ini?</Card.Description
                    >
                </Card.Header>
                <Card.Footer class="flex justify-end gap-3">
                    <Button
                        variant="outline"
                        onclick={() => (deleteConfirmId = null)}
                    >
                        Batal
                    </Button>
                    <Button
                        variant="destructive"
                        onclick={() => handleDelete(deleteConfirmId!)}
                    >
                        Hapus
                    </Button>
                </Card.Footer>
            </Card.Root>
        </div>
    {/if}

    <!-- Table -->
    <Card.Root class="border-border/60 bg-card/60 backdrop-blur-xl">
        <Card.Content class="p-0">
            {#if devices.length === 0}
                <div class="p-12 text-center text-muted-foreground">
                    Belum ada perangkat terdaftar
                </div>
            {:else}
                <Table.Root>
                    <Table.Header>
                        <Table.Row>
                            <Table.Head>Device ID</Table.Head>
                            <Table.Head>Name</Table.Head>
                            <Table.Head>Created At</Table.Head>
                            <Table.Head class="text-right">Actions</Table.Head>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        {#each devices as device (device.id)}
                            <Table.Row>
                                <Table.Cell class="font-mono text-sm">
                                    {device.id}
                                </Table.Cell>
                                <Table.Cell>
                                    {#if editingId === device.id}
                                        <div class="flex items-center gap-2">
                                            <Input
                                                bind:value={editName}
                                                class="h-8 text-sm"
                                            />
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="size-8 text-emerald-400"
                                                onclick={() => handleUpdate(device.id)}
                                                disabled={editSubmitting}
                                            >
                                                <Check class="size-4" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="size-8 text-muted-foreground"
                                                onclick={cancelEdit}
                                            >
                                                <X class="size-4" />
                                            </Button>
                                        </div>
                                    {:else}
                                        {device.name}
                                    {/if}
                                </Table.Cell>
                                <Table.Cell class="text-sm text-muted-foreground">
                                    {new Date(device.created_at).toLocaleDateString(
                                        'id-ID',
                                        {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                        },
                                    )}
                                </Table.Cell>
                                <Table.Cell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="size-8"
                                            onclick={() => startEdit(device)}
                                        >
                                            <Edit class="size-4" />
                                        </Button>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="size-8 text-red-400"
                                            onclick={() =>
                                                (deleteConfirmId = device.id)}
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </Table.Cell>
                            </Table.Row>
                        {/each}
                    </Table.Body>
                </Table.Root>
            {/if}
        </Card.Content>
    </Card.Root>
</div>
