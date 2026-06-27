<script lang="ts">
    import { router, useHttp } from '@inertiajs/svelte';
    import {
        TableHandler,
        Datatable,
        ThSort,
        ThFilter,
        Th,
    } from '@vincjo/datatables';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
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
        ShieldCheck,
        CalendarDays,
        Fingerprint,
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { dashboard, adminList } from '$routes/admin';
    import { goBack } from '$lib/navigation';
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

    const adminTable = $derived.by(() => {
        return filteredUsers.length > 0
            ? new TableHandler(filteredUsers, { rowsPerPage: 10 })
            : null;
    });

    function formatDate(dateStr: string): string {
        return new Date(dateStr).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    }

    // ─── Add ────────────────────────────────────────────────────────
    function openAdd() {
        addForm.reset();
        showAddModal = true;
    }

    function closeAdd() {
        showAddModal = false;
        addForm.reset();
    }

    function handleAdd() {
        addForm.post(adminList().url, {
            onSuccess: () => {
                closeAdd();
                router.reload();
                toast.success('Pengguna berhasil ditambahkan');
            },
            onError: (
                errors: FormDataErrors<{
                    name: string;
                    username: string;
                    password: string;
                }>,
            ) => {
                const msg =
                    errors.username ??
                    errors.password ??
                    errors.name ??
                    'Gagal menambahkan';
                toast.error(msg);
            },
        });
    }

    // ─── Edit ───────────────────────────────────────────────────────
    function openEdit(user: AdminUser) {
        editForm
            .defaults({
                name: user.name,
                username: user.username,
                password: '',
            })
            .reset();
        editTarget = user;
    }

    function closeEdit() {
        editTarget = null;
        editForm.reset();
    }

    function handleEdit() {
        if (!editTarget) return;
        editForm.put(adminList.update({ id: editTarget.id }).url, {
            onSuccess: () => {
                closeEdit();
                router.reload();
                toast.success('Pengguna berhasil diperbarui');
            },
            onError: (
                errors: FormDataErrors<{
                    name: string;
                    username: string;
                    password: string;
                }>,
            ) => {
                const msg =
                    errors.password ??
                    errors.username ??
                    errors.name ??
                    'Gagal memperbarui';
                toast.error(msg);
            },
        });
    }

    // ─── Delete ─────────────────────────────────────────────────────
    function openDelete(user: AdminUser) {
        deleteTarget = user;
    }

    function closeDelete() {
        deleteTarget = null;
    }

    function handleDelete() {
        if (!deleteTarget) return;
        deleteForm.delete(adminList.destroy({ id: deleteTarget.id }).url, {
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

<div class="min-h-screen bg-mesh-pink">
    <!-- ──────── Decorative floating orbs ──────── -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div
            class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-violet-500/12 blur-3xl animate-float"
        />
        <div
            class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-pink-500/8 blur-3xl animate-float delay-300"
        />
    </div>

    <!-- ──────── Sticky Header ──────── -->
    <div
        class="sticky top-0 z-40 border-b border-border/60 bg-card/70 backdrop-blur-xl relative"
    >
        <div class="px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" onclick={goBack}>
                    <ArrowLeft class="size-5" />
                </Button>
                <div>
                    <div class="flex items-center gap-2.5">
                        <div
                            class="size-9 rounded-xl bg-violet-500/20 flex items-center justify-center"
                        >
                            <ShieldCheck class="size-5 text-violet-400" />
                        </div>
                        <h1 class="text-xl font-bold tracking-tight">
                            Admin List
                        </h1>
                    </div>
                    <p class="text-xs text-muted-foreground ml-0.5 mt-0.5">
                        {users.length} admin terdaftar
                    </p>
                </div>
            </div>
            <Button
                onclick={openAdd}
                class="gap-2 shadow-lg shadow-violet-500/20"
            >
                <Plus class="size-4" />
                Tambah Admin
            </Button>
        </div>
    </div>

    <div class="px-6 py-6 space-y-6 relative">
        <!-- ──────── Search ──────── -->
        <div class="relative max-w-md">
            <Search
                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
            />
            <Input
                bind:value={search}
                placeholder="Cari nama atau username..."
                class="pl-10 h-10 bg-card/60 border-border/60"
            />
        </div>

        <!-- ──────── Table Card ──────── -->
        <Card.Root
            class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl"
        >
            {#if filteredUsers.length === 0}
                <div class="p-16 text-center">
                    <div
                        class="size-14 rounded-2xl bg-violet-500/10 flex items-center justify-center mx-auto mb-4"
                    >
                        <Shield class="size-7 text-violet-400/60" />
                    </div>
                    <p class="text-muted-foreground font-medium">
                        {search
                            ? 'Tidak ada admin yang cocok'
                            : 'Belum ada admin terdaftar'}
                    </p>
                    {#if !search}
                        <Button
                            variant="outline"
                            size="sm"
                            onclick={openAdd}
                            class="mt-4 gap-2"
                        >
                            <Plus class="size-3.5" />
                            Tambah Admin
                        </Button>
                    {/if}
                </div>
            {:else if adminTable}
                <Datatable basic table={adminTable}>
                    <table>
                        <thead>
                            <tr>
                                <Th>#</Th>
                                <ThSort table={adminTable} field="name"
                                    >Name</ThSort
                                >
                                <ThSort table={adminTable} field="username"
                                    >Username</ThSort
                                >
                                <ThSort table={adminTable} field="created_at"
                                    >Created At</ThSort
                                >
                                <Th>Actions</Th>
                            </tr>
                            <tr>
                                <Th></Th>
                                <ThFilter table={adminTable} field="name" />
                                <ThFilter table={adminTable} field="username" />
                                <ThFilter
                                    table={adminTable}
                                    field="created_at"
                                />
                                <Th></Th>
                            </tr>
                        </thead>
                        <tbody>
                            {#each adminTable.rows as row (row.id)}
                                <tr>
                                    <td
                                        data-label="#"
                                        class="text-muted-foreground font-mono text-xs"
                                    >
                                        <span
                                            class="inline-flex items-center justify-center size-7 rounded-md bg-muted/40 text-xs font-medium"
                                        >
                                            {filteredUsers.indexOf(row) + 1}
                                        </span>
                                    </td>
                                    <td data-label="#">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-10 rounded-full bg-gradient-to-br from-violet-500/20 to-pink-500/20 flex items-center justify-center shrink-0 ring-1 ring-violet-500/20"
                                            >
                                                <User
                                                    class="size-4 text-violet-400"
                                                />
                                            </div>
                                            <div>
                                                <span class="font-medium"
                                                    >{row.name}</span
                                                >
                                                <p
                                                    class="text-xs text-muted-foreground/60 mt-0.5"
                                                >
                                                    ID: #{row.id}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Username">
                                        <Badge
                                            variant="outline"
                                            class="font-mono text-xs bg-violet-500/10 border-violet-300/30 text-violet-600 gap-1.5"
                                        >
                                            <Fingerprint class="size-3" />
                                            @{row.username}
                                        </Badge>
                                    </td>
                                    <td
                                        data-label="Created"
                                        class="text-sm text-muted-foreground"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1.5"
                                        >
                                            <CalendarDays
                                                class="size-3.5 text-muted-foreground/60 shrink-0"
                                            />
                                            {formatDate(row.created_at)}
                                        </span>
                                    </td>
                                    <td data-label="Actions" class="text-right">
                                        <div
                                            class="flex items-center justify-end gap-1"
                                        >
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="size-8 text-muted-foreground hover:text-violet-500 hover:bg-violet-500/10"
                                                onclick={() => openEdit(row)}
                                            >
                                                <Edit class="size-3.5" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                class="size-8 text-muted-foreground hover:text-red-400 hover:bg-red-500/10"
                                                onclick={() => openDelete(row)}
                                            >
                                                <Trash2 class="size-3.5" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </Datatable>
            {/if}
        </Card.Root>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     ADD MODAL
     ════════════════════════════════════════════════════════════════ -->
{#if showAddModal}
    <div
        class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
    >
        <Card.Root
            class="w-full max-w-md border-violet-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200"
        >
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div
                        class="size-10 rounded-xl bg-violet-500/20 flex items-center justify-center"
                    >
                        <Plus class="size-5 text-violet-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Tambah Admin</Card.Title>
                        <Card.Description>Buat akun admin baru</Card.Description
                        >
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="add-name" class="text-sm font-medium"
                        >Nama</Label
                    >
                    <Input
                        id="add-name"
                        bind:value={addForm.name}
                        placeholder="Nama lengkap"
                        class="h-10"
                    />
                    {#if addForm.errors.name}
                        <p class="text-xs text-rose-400">
                            {addForm.errors.name}
                        </p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="add-username" class="text-sm font-medium"
                        >Username</Label
                    >
                    <Input
                        id="add-username"
                        bind:value={addForm.username}
                        placeholder="Username"
                        class="h-10"
                    />
                    {#if addForm.errors.username}
                        <p class="text-xs text-rose-400">
                            {addForm.errors.username}
                        </p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="add-password" class="text-sm font-medium"
                        >Password</Label
                    >
                    <Input
                        id="add-password"
                        type="password"
                        bind:value={addForm.password}
                        placeholder="Minimal 6 karakter"
                        class="h-10"
                    />
                    {#if addForm.errors.password}
                        <p class="text-xs text-rose-400">
                            {addForm.errors.password}
                        </p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button
                    variant="outline"
                    onclick={closeAdd}
                    disabled={addForm.processing}
                >
                    Batal
                </Button>
                <Button
                    onclick={handleAdd}
                    disabled={addForm.processing ||
                        !addForm.name.trim() ||
                        !addForm.username.trim() ||
                        !addForm.password.trim()}
                    class="gap-2"
                >
                    {#if addForm.processing}
                        <div
                            class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                        ></div>
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

<!-- ════════════════════════════════════════════════════════════════
     EDIT MODAL
     ════════════════════════════════════════════════════════════════ -->
{#if editTarget}
    <div
        class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
    >
        <Card.Root
            class="w-full max-w-md border-emerald-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200"
        >
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div
                        class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center"
                    >
                        <Edit class="size-5 text-emerald-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Edit Admin</Card.Title>
                        <Card.Description>
                            Mengubah <span class="font-medium text-foreground"
                                >{editTarget.name}</span
                            >
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="edit-name" class="text-sm font-medium"
                        >Nama</Label
                    >
                    <Input
                        id="edit-name"
                        bind:value={editForm.name}
                        placeholder="Nama lengkap"
                        class="h-10"
                    />
                    {#if editForm.errors.name}
                        <p class="text-xs text-rose-400">
                            {editForm.errors.name}
                        </p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="edit-username" class="text-sm font-medium"
                        >Username</Label
                    >
                    <Input
                        id="edit-username"
                        bind:value={editForm.username}
                        placeholder="Username"
                        class="h-10"
                    />
                    {#if editForm.errors.username}
                        <p class="text-xs text-rose-400">
                            {editForm.errors.username}
                        </p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="edit-password" class="text-sm font-medium">
                        Password
                        <span class="text-xs text-muted-foreground font-normal"
                            >(biarkan kosong jika tidak diubah)</span
                        >
                    </Label>
                    <Input
                        id="edit-password"
                        type="password"
                        bind:value={editForm.password}
                        placeholder="Kosongi jika tidak ingin mengubah"
                        class="h-10"
                    />
                    {#if editForm.errors.password}
                        <p class="text-xs text-rose-400">
                            {editForm.errors.password}
                        </p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button
                    variant="outline"
                    onclick={closeEdit}
                    disabled={editForm.processing}
                >
                    Batal
                </Button>
                <Button
                    onclick={handleEdit}
                    disabled={editForm.processing ||
                        !editForm.name.trim() ||
                        !editForm.username.trim()}
                    class="gap-2"
                >
                    {#if editForm.processing}
                        <div
                            class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                        ></div>
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

<!-- ════════════════════════════════════════════════════════════════
     DELETE MODAL
     ════════════════════════════════════════════════════════════════ -->
{#if deleteTarget}
    <div
        class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
    >
        <Card.Root
            class="w-full max-w-md border-red-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200"
        >
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div
                        class="size-10 rounded-xl bg-red-500/20 flex items-center justify-center"
                    >
                        <Trash2 class="size-5 text-red-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Hapus Admin</Card.Title>
                        <Card.Description>
                            Apakah Anda yakin ingin menghapus
                            <span class="font-medium text-foreground"
                                >{deleteTarget.name}</span
                            >?
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Card.Footer class="flex justify-end gap-3">
                <Button
                    variant="outline"
                    onclick={closeDelete}
                    disabled={deleteForm.processing}
                >
                    Batal
                </Button>
                <Button
                    variant="destructive"
                    onclick={handleDelete}
                    disabled={deleteForm.processing}
                    class="gap-2"
                >
                    {#if deleteForm.processing}
                        <div
                            class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                        ></div>
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

<style>
    :global(.svelte-simple-datatable table) {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        background: inherit;
    }
    :global(.svelte-simple-datatable table thead) {
        position: sticky;
        inset-block-start: 0;
        background: inherit;
        z-index: 1;
    }
    :global(.svelte-simple-datatable thead tr) {
        background: inherit;
    }
    :global(.svelte-simple-datatable thead tr th) {
        background: inherit;
    }
    :global(.svelte-simple-datatable thead tr:first-child th) {
        padding: 8px 20px;
        background: inherit;
    }
    :global(.svelte-simple-datatable tbody) {
        background: inherit;
    }
    :global(.svelte-simple-datatable tbody tr) {
        transition: background, 0.2s;
        background: inherit;
    }
    :global(.svelte-simple-datatable tbody tr:hover) {
        background: var(--grey-lighten-3, #fafafa);
    }
    :global(.svelte-simple-datatable tbody td) {
        padding: 4px 20px;
        border-right: 1px solid var(--grey-lighten, #eee);
        border-bottom: 1px solid var(--grey-lighten, #eee);
        background: inherit;
    }
    :global(.svelte-simple-datatable tbody td:last-child) {
        border-right: none;
    }
    :global(.svelte-simple-datatable u.highlight) {
        text-decoration: none;
        background: rgba(251, 192, 45, 0.6);
        border-radius: 2px;
    }
    :global(.svelte-simple-datatable footer.divider) {
        border-top: 1px solid var(--grey, #e0e0e0);
    }

    @media (max-width: 640px) {
        :global(.svelte-simple-datatable) {
            height: auto !important;
        }
        :global(.svelte-simple-datatable article) {
            overflow: visible !important;
            height: auto !important;
        }
        :global(.svelte-simple-datatable table) {
            display: block !important;
        }
        :global(.svelte-simple-datatable thead) {
            display: none !important;
        }
        :global(.svelte-simple-datatable tbody),
        :global(.svelte-simple-datatable tr),
        :global(.svelte-simple-datatable th),
        :global(.svelte-simple-datatable td) {
            display: block;
        }
        :global(.svelte-simple-datatable tbody tr) {
            margin-bottom: 12px;
            padding: 12px;
            border: 1px solid var(--grey-lighten, #eee);
            border-radius: 12px;
            background: inherit;
        }
        :global(.svelte-simple-datatable tbody td) {
            padding: 6px 4px;
            border: none !important;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            text-align: left;
            gap: 8px;
        }
        :global(.svelte-simple-datatable tbody td:before) {
            content: attr(data-label);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--font-grey, #9e9e9e);
            min-width: 80px;
            flex-shrink: 0;
        }
    }
</style>
