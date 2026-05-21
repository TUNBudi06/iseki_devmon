<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import * as Table from '$shadcn/components/ui/table';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Users, ArrowLeft, Plus, Trash2, User } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { dashboard, adminList } from '$routes/admin';

    type AdminUser = {
        id: number;
        name: string;
        username: string;
        created_at: string;
    };

    let { users }: { users: AdminUser[] } = $props();

    let showAddModal = $state(false);
    let newName = $state('');
    let newUsername = $state('');
    let newPassword = $state('');
    let isSubmitting = $state(false);

    let deleteConfirmId = $state<number | null>(null);

    function goBack() {
        router.visit(routeUrl(dashboard()));
    }

    async function handleAdd() {
        if (!newName.trim() || !newUsername.trim() || !newPassword.trim())
            return;
        isSubmitting = true;

        router.post(routeUrl(adminList()), {
            name: newName,
            username: newUsername,
            password: newPassword,
            preserveScroll: true,
            onSuccess: () => {
                showAddModal = false;
                newName = '';
                newUsername = '';
                newPassword = '';
                toast.success('Pengguna berhasil ditambahkan');
            },
            onError: (errors) => {
                toast.error(errors?.username ?? 'Gagal menambahkan pengguna');
            },
            onFinish: () => {
                isSubmitting = false;
            },
        });
    }

    function handleDelete(id: number) {
        router.delete(routeUrl(adminList.destroy({ id })), {
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirmId = null;
                toast.success('Pengguna berhasil dihapus');
            },
            onError: () => {
                toast.error('Gagal menghapus pengguna');
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
                    <Users class="size-6 text-violet-400" />
                    <h1 class="text-2xl font-bold">Admin List</h1>
                </div>
                <p class="text-sm text-muted-foreground mt-1">
                    Kelola pengguna admin
                </p>
            </div>
        </div>

        <Button onclick={() => (showAddModal = true)} class="gap-2">
            <Plus class="size-4" />
            Tambah Admin
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
                    <Card.Title>Tambah Admin Baru</Card.Title>
                    <Card.Description
                        >Masukkan data admin baru</Card.Description
                    >
                </Card.Header>
                <Card.Content class="space-y-4">
                    <div class="space-y-2">
                        <Label for="name">Nama</Label>
                        <Input
                            id="name"
                            bind:value={newName}
                            placeholder="Nama lengkap"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="username">Username</Label>
                        <Input
                            id="username"
                            bind:value={newUsername}
                            placeholder="Username"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            bind:value={newPassword}
                            placeholder="Minimal 6 karakter"
                        />
                    </div>
                </Card.Content>
                <Card.Footer class="flex justify-end gap-3">
                    <Button
                        variant="outline"
                        onclick={() => {
                            showAddModal = false;
                            newName = '';
                            newUsername = '';
                            newPassword = '';
                        }}
                    >
                        Batal
                    </Button>
                    <Button
                        onclick={handleAdd}
                        disabled={
                            isSubmitting ||
                            !newName.trim() ||
                            !newUsername.trim() ||
                            !newPassword.trim()
                        }
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
                    <Card.Title>Hapus Admin</Card.Title>
                    <Card.Description
                        >Apakah Anda yakin ingin menghapus admin ini?</Card.Description
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
            {#if users.length === 0}
                <div class="p-12 text-center text-muted-foreground">
                    Belum ada admin terdaftar
                </div>
            {:else}
                <Table.Root>
                    <Table.Header>
                        <Table.Row>
                            <Table.Head>#</Table.Head>
                            <Table.Head>Name</Table.Head>
                            <Table.Head>Username</Table.Head>
                            <Table.Head>Joined</Table.Head>
                            <Table.Head class="text-right">Actions</Table.Head>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        {#each users as user, idx (user.id)}
                            <Table.Row>
                                <Table.Cell class="text-muted-foreground">
                                    {idx + 1}
                                </Table.Cell>
                                <Table.Cell class="flex items-center gap-2">
                                    <div
                                        class="size-8 rounded-full bg-violet-500/20 flex items-center justify-center"
                                    >
                                        <User class="size-4 text-violet-400" />
                                    </div>
                                    {user.name}
                                </Table.Cell>
                                <Table.Cell class="font-mono text-sm">
                                    {user.username}
                                </Table.Cell>
                                <Table.Cell class="text-sm text-muted-foreground">
                                    {new Date(user.created_at).toLocaleDateString(
                                        'id-ID',
                                        {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                        },
                                    )}
                                </Table.Cell>
                                <Table.Cell class="text-right">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="size-8 text-red-400"
                                        onclick={() => (deleteConfirmId = user.id)}
                                    >
                                        <Trash2 class="size-4" />
                                    </Button>
                                </Table.Cell>
                            </Table.Row>
                        {/each}
                    </Table.Body>
                </Table.Root>
            {/if}
        </Card.Content>
    </Card.Root>
</div>
