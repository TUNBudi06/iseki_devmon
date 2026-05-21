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
        Users,
        ArrowLeft,
        Plus,
        Trash2,
        Edit,
        Check,
        Search,
        User,
        Shield,
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { dashboard, adminList } from '$routes/admin';
    import type { FormDataErrors } from '@inertiajs/core';

    type AdminUser = {
        id: number;
        name: string;
        username: string;
        created_at: string;
    };

    let { users }: { users: AdminUser[] } = $props();

    // ─── Add Form ──────────────────────────────────────────────────
    const addForm = useHttp({ name: '', username: '', password: '' });
    let showAddModal = $state(false);

    // ─── Edit Form ──────────────────────────────────────────────────
    const editForm = useHttp({ name: '', username: '', password: '' });
    let editTarget = $state<AdminUser | null>(null);

    // ─── Delete ─────────────────────────────────────────────────────
    const deleteForm = useHttp({});
    let deleteTarget = $state<AdminUser | null>(null);

    // ─── Search ─────────────────────────────────────────────────────
    let search = $state('');

    const filteredUsers = $derived.by(() => {
        if (!search.trim()) return users;
        const q = search.toLowerCase();
        return users.filter(
            (u) =>
                u.name.toLowerCase().includes(q) ||
                u.username.toLowerCase().includes(q),
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
        addForm.post(routeUrl(adminList()), {
            onSuccess: () => {
                closeAdd();
                router.reload();
                toast.success('Pengguna berhasil ditambahkan');
            },
            onError: (errors: FormDataErrors<{ name: string; username: string; password: string }>) => {
                const msg = errors.username ?? errors.password ?? errors.name ?? 'Gagal menambahkan';
                toast.error(msg);
            },
        });
    }

    function openEdit(user: AdminUser) {
        editForm.defaults({ name: user.name, username: user.username, password: '' }).reset();
        editTarget = user;
    }

    function closeEdit() {
        editTarget = null;
        editForm.reset();
    }

    function handleEdit() {
        if (!editTarget) return;
        editForm.put(routeUrl(adminList.update({ id: editTarget.id })), {
            onSuccess: () => {
                closeEdit();
                router.reload();
                toast.success('Pengguna berhasil diperbarui');
            },
            onError: (errors: FormDataErrors<{ name: string; username: string; password: string }>) => {
                const msg = errors.password ?? errors.username ?? errors.name ?? 'Gagal memperbarui';
                toast.error(msg);
            },
        });
    }

    function openDelete(user: AdminUser) {
        deleteTarget = user;
    }

    function closeDelete() {
        deleteTarget = null;
    }

    function handleDelete() {
        if (!deleteTarget) return;
        deleteForm.delete(routeUrl(adminList.destroy({ id: deleteTarget.id })), {
            onSuccess: () => {
                closeDelete();
                router.reload();
                toast.success('Pengguna berhasil dihapus');
            },
            onError: () => {
                toast.error('Gagal menghapus pengguna');
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
                        <div class="size-9 rounded-xl bg-violet-500/20 flex items-center justify-center">
                            <Users class="size-5 text-violet-400" />
                        </div>
                        <h1 class="text-xl font-bold tracking-tight">Admin List</h1>
                    </div>
                    <p class="text-xs text-muted-foreground ml-0.5 mt-0.5">
                        {users.length} admin terdaftar
                    </p>
                </div>
            </div>
            <Button onclick={openAdd} class="gap-2 shadow-lg shadow-violet-500/20">
                <Plus class="size-4" />
                Tambah Admin
            </Button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-6 space-y-6">
        <!-- Search -->
        <div class="relative max-w-md">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
            <Input
                bind:value={search}
                placeholder="Cari nama atau username..."
                class="pl-10 h-10 bg-card/50 border-border/60"
            />
        </div>

        <!-- Table Card -->
        <Card.Root class="border-border/60 bg-card/60 backdrop-blur-xl overflow-hidden shadow-xl">
            {#if filteredUsers.length === 0}
                <div class="p-16 text-center">
                    <div class="size-14 rounded-2xl bg-muted/50 flex items-center justify-center mx-auto mb-4">
                        <Shield class="size-7 text-muted-foreground/50" />
                    </div>
                    <p class="text-muted-foreground font-medium">
                        {search ? 'Tidak ada admin yang cocok' : 'Belum ada admin terdaftar'}
                    </p>
                    {#if !search}
                        <Button variant="outline" size="sm" onclick={openAdd} class="mt-4 gap-2">
                            <Plus class="size-3.5" />
                            Tambah Admin
                        </Button>
                    {/if}
                </div>
            {:else}
                <div class="overflow-x-auto">
                    <Table.Root>
                        <Table.Header>
                            <Table.Row class="border-border/40 bg-muted/30">
                                <Table.Head class="font-semibold text-xs uppercase tracking-wider w-12">#</Table.Head>
                                <Table.Head class="font-semibold text-xs uppercase tracking-wider">Name</Table.Head>
                                <Table.Head class="font-semibold text-xs uppercase tracking-wider">Username</Table.Head>
                                <Table.Head class="font-semibold text-xs uppercase tracking-wider">Joined</Table.Head>
                                <Table.Head class="text-right font-semibold text-xs uppercase tracking-wider">Actions</Table.Head>
                            </Table.Row>
                        </Table.Header>
                        <Table.Body>
                            {#each filteredUsers as user, idx (user.id)}
                                <Table.Row class="border-border/30 hover:bg-muted/20 transition-colors">
                                    <Table.Cell class="text-muted-foreground text-sm">
                                        {idx + 1}
                                    </Table.Cell>
                                    <Table.Cell>
                                        <div class="flex items-center gap-3">
                                            <div class="size-9 rounded-full bg-violet-500/15 flex items-center justify-center shrink-0">
                                                <User class="size-4 text-violet-400" />
                                            </div>
                                            <span class="font-medium">{user.name}</span>
                                        </div>
                                    </Table.Cell>
                                    <Table.Cell>
                                        <Badge variant="outline" class="font-mono text-xs bg-primary/5 border-primary/20">
                                            @{user.username}
                                        </Badge>
                                    </Table.Cell>
                                    <Table.Cell class="text-sm text-muted-foreground">
                                        {new Date(user.created_at).toLocaleDateString('id-ID', {
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
                                                onclick={() => openEdit(user)}
                                            >
                                                <Edit class="size-3.5" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="size-8 text-muted-foreground hover:text-red-400 hover:bg-red-500/10"
                                                onclick={() => openDelete(user)}
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
                    <div class="size-10 rounded-xl bg-violet-500/20 flex items-center justify-center">
                        <Plus class="size-5 text-violet-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Tambah Admin</Card.Title>
                        <Card.Description>Buat akun admin baru</Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="add-name" class="text-sm font-medium">Nama</Label>
                    <Input id="add-name" bind:value={addForm.name} placeholder="Nama lengkap" class="h-10" />
                    {#if addForm.errors.name}
                        <p class="text-xs text-red-400">{addForm.errors.name}</p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="add-username" class="text-sm font-medium">Username</Label>
                    <Input id="add-username" bind:value={addForm.username} placeholder="Username" class="h-10" />
                    {#if addForm.errors.username}
                        <p class="text-xs text-red-400">{addForm.errors.username}</p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="add-password" class="text-sm font-medium">Password</Label>
                    <Input id="add-password" type="password" bind:value={addForm.password} placeholder="Minimal 6 karakter" class="h-10" />
                    {#if addForm.errors.password}
                        <p class="text-xs text-red-400">{addForm.errors.password}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeAdd} disabled={addForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleAdd}
                    disabled={addForm.processing || !addForm.name.trim() || !addForm.username.trim() || !addForm.password.trim()}
                    class="gap-2">
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
                        <Card.Title class="text-lg">Edit Admin</Card.Title>
                        <Card.Description>
                            Mengubah <span class="font-medium text-foreground">{editTarget.name}</span>
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="edit-name" class="text-sm font-medium">Nama</Label>
                    <Input id="edit-name" bind:value={editForm.name} placeholder="Nama lengkap" class="h-10" />
                    {#if editForm.errors.name}
                        <p class="text-xs text-red-400">{editForm.errors.name}</p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="edit-username" class="text-sm font-medium">Username</Label>
                    <Input id="edit-username" bind:value={editForm.username} placeholder="Username" class="h-10" />
                    {#if editForm.errors.username}
                        <p class="text-xs text-red-400">{editForm.errors.username}</p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="edit-password" class="text-sm font-medium">
                        Password
                        <span class="text-xs text-muted-foreground font-normal">(biarkan kosong jika tidak diubah)</span>
                    </Label>
                    <Input id="edit-password" type="password" bind:value={editForm.password} placeholder="Kosongi jika tidak ingin mengubah" class="h-10" />
                    {#if editForm.errors.password}
                        <p class="text-xs text-red-400">{editForm.errors.password}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeEdit} disabled={editForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleEdit}
                    disabled={editForm.processing || !editForm.name.trim() || !editForm.username.trim()}
                    class="gap-2">
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
                        <Card.Title class="text-lg">Hapus Admin</Card.Title>
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
