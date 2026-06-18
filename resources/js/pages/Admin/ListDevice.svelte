<script lang="ts">
    import { router, useHttp, useForm } from '@inertiajs/svelte';
    import storage from '$routes/storage';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { TableHandler, RowsPerPage, RowCount, Pagination } from '@vincjo/datatables';
    import { Search as DtSearch } from '@vincjo/datatables';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Separator } from '$shadcn/components/ui/separator';
    import * as FileDropZone from '$shadcn/components/ui/file-drop-zone';
    import { QRCode } from '$shadcn/components/spell/qrcode';
    import { Toaster } from '$shadcn/components/ui/sonner';
    import {
        Smartphone,
        ArrowLeft,
        Plus,
        Trash2,
        Edit,
        X,
        Search,
        Tablet,
        Cpu,
        HardDrive,
        CalendarDays,
        Banknote,
        CircleCheck,
        CircleX,
        Check,
        AppWindow,
        Image,
        ImageUp,
        QrCode,
        ScanQrCode,
        Layers,
    } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { dashboard, listDevice } from '$routes/admin';
    import type { FormDataErrors } from '@inertiajs/core';

    // ─── Types ───────────────────────────────────────────────────
    type Brand = {
        id: string;
        name: string;
        created_at: string;
        updated_at: string;
    };

    type PhoneList = {
        id: number;
        brand_id: string;
        model_id: string;
        model_name: string;
        model_type: string;
        buy_date: string;
        price: string;
        ram: string;
        storage: string;
        thumbnail: string | null;
        list_photos: string[] | null;
        registered: boolean;
        approved: boolean;
        hash_token: string | null;
        created_at: string;
        updated_at: string;
        deleted_at: string | null;
        brand: Brand | null;
        departemen: string;
    };

    let { brands, phoneLists, departemenOptions }: { brands: Brand[]; phoneLists: PhoneList[]; departemenOptions: { id: string; name: string }[] } = $props();

    // ─── Active Tab ──────────────────────────────────────────────
    const tabs = [
        { id: 'brands', label: 'Brand / Perangkat', icon: Smartphone },
        { id: 'phones', label: 'List Devices', icon: AppWindow },
        { id: 'departemen', label: 'Departemen', icon: Layers },
    ] as const;
    type TabId = (typeof tabs)[number]['id'];
    let activeTab: TabId = $state('brands');

    // ─── Add Brand Form ──────────────────────────────────────────
    const addBrandForm = useHttp({ id: '', name: '' });
    let showAddBrandModal = $state(false);

    // ─── Edit Brand Form ─────────────────────────────────────────
    const editBrandForm = useHttp({ name: '' });
    let editBrandTarget = $state<Brand | null>(null);

    // ─── Delete Brand ────────────────────────────────────────────
    const deleteBrandForm = useHttp({});
    let deleteBrandTarget = $state<Brand | null>(null);

    // ─── Add Phone Form ──────────────────────────────────────────
    const addPhoneForm = useForm({
        brand_id: '',
        model_id: '',
        model_name: '',
        model_type: 'Phone',
        buy_date: '',
        price: '',
        ram: '',
        storage: '',
        departemen: 'Production',
        list_photos: [] as unknown,
    });
    let showAddPhoneModal = $state(false);
    let addPhotos = $state<File[]>([]);
    let addPhotosPreview = $state<string[]>([]);
    let addThumbnailIdx = $state(0);

    // ─── Edit Phone Form ─────────────────────────────────────────
    const editPhoneForm = useForm({
        brand_id: '',
        model_id: '',
        model_name: '',
        model_type: 'Phone',
        buy_date: '',
        price: '',
        ram: '',
        storage: '',
        departemen: 'Production',
        list_photos: [] as unknown,
        thumbnail: '',
    });
    let editPhotos = $state<File[]>([]);
    let editPhotosPreview = $state<string[]>([]);
    let editThumbnailIdx = $state(0);
    let editExistingThumbnailIdx = $state(0);
    let editPhoneTarget = $state<PhoneList | null>(null);

    // ─── QR Code ─────────────────────────────────────────────────
    let qrTarget = $state<PhoneList | null>(null);
    let showQrModal = $state(false);

    function openQr(phone: PhoneList) {
        qrTarget = phone;
        showQrModal = true;
    }

    function closeQr() {
        showQrModal = false;
        qrTarget = null;
    }

    function getQrValue(phone: PhoneList): string {
        return phone.hash_token ?? phone.model_id;
    }

    // ─── Delete Phone ────────────────────────────────────────────
    const deletePhoneForm = useHttp({});
    let deletePhoneTarget = $state<PhoneList | null>(null);

    // ─── Approve Phone ────────────────────────────────────────────
    let approvingId = $state<number | null>(null);

    function handleApprovePhone(phone: PhoneList) {
        approvingId = phone.id;
        router.post(listDevice.phone.approve({ id: phone.id }).url, {}, {
            preserveScroll: true,
            onSuccess: () => {
                approvingId = null;
                toast.success('Perangkat disetujui');
            },
            onError: () => {
                approvingId = null;
                toast.error('Gagal menyetujui perangkat');
            },
        });
    }

    // ─── Departemen CRUD ─────────────────────────────────────────
    const addDeptForm = useHttp({ id: '', name: '' });
    let showAddDeptModal = $state(false);

    const editDeptForm = useHttp({ name: '' });
    let editDeptTarget = $state<{ id: string; name: string } | null>(null);

    const deleteDeptForm = useHttp({});
    let deleteDeptTarget = $state<{ id: string; name: string } | null>(null);

    // ─── Delete Photo ────────────────────────────────────────────
    const deletePhotoForm = useHttp({});
    let deletingPhotoUrl = $state<string | null>(null);

    // ─── Search ──────────────────────────────────────────────────
    let brandSearch = $state('');
    let phoneSearch = $state('');
    let departemenFilter = $state<string>('all');

    const filteredBrands = $derived.by(() => {
        if (!brandSearch.trim()) return brands;
        const q = brandSearch.toLowerCase();
        return brands.filter(
            (d) => d.id.toLowerCase().includes(q) || d.name.toLowerCase().includes(q),
        );
    });

    const filteredPhones = $derived.by(() => {
        let items = phoneLists;

        // Filter by departemen (p.departemen is the stored string id)
        if (departemenFilter !== 'all') {
            items = items.filter((p) => p.departemen === departemenFilter);
        }

        // Filter by search
        if (phoneSearch.trim()) {
            const q = phoneSearch.toLowerCase();
            items = items.filter(
                (p) =>
                    p.model_id.toLowerCase().includes(q) ||
                    p.model_name.toLowerCase().includes(q) ||
                    p.brand?.name.toLowerCase().includes(q) ||
                    p.model_type.toLowerCase().includes(q) ||
                    p.imei?.toLowerCase().includes(q) ||
                    p.mac_address?.toLowerCase().includes(q),
            );
        }

        return items;
    });

    const brandsTable = $derived(filteredBrands.length > 0 ? new TableHandler(filteredBrands, { rowsPerPage: 10 }) : null);
    const phonesTable = $derived(filteredPhones.length > 0 ? new TableHandler(filteredPhones, { rowsPerPage: 10 }) : null);

    // ─── Helpers ─────────────────────────────────────────────────
    function formatPrice(val: string): string {
        const num = parseInt(val.replace(/\D/g, ''), 10);
        if (isNaN(num)) return val;
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
    }

    function parsePrice(val: string): number {
        return parseInt(val.replace(/\D/g, ''), 10) || 0;
    }

    const totalBudget = $derived(
        phoneLists.reduce((sum, p) => sum + parsePrice(p.price), 0),
    );

    const totalBudgetFormatted = $derived(
        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(totalBudget),
    );

    const removedDevices = $derived(phoneLists.filter((p) => p.deleted_at !== null).length);

    function storageUrl(path: string | null): string | null {
        if (!path) return null;
        // Strip 'storage/' prefix if present (handles both old and new stored paths)
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }

    function formatDate(dateStr: string): string {
        return new Date(dateStr).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    }



    // ─── Navigation ──────────────────────────────────────────────
    function goBack() {
        router.visit(dashboard().url);
    }

    // ═══════════════════════════════════════════════════════════════
    //  BRAND CRUD
    // ═══════════════════════════════════════════════════════════════

    function openAddBrand() {
        addBrandForm.reset();
        showAddBrandModal = true;
    }

    function closeAddBrand() {
        showAddBrandModal = false;
        addBrandForm.reset();
    }

    function handleAddBrand() {
        addBrandForm.post(listDevice().url, {
            onSuccess: () => {
                closeAddBrand();
                router.reload();
                toast.success('Perangkat berhasil ditambahkan');
            },
            onError: (errors: FormDataErrors<{ id: string; name: string }>) => {
                toast.error(errors.id ?? errors.name ?? 'Gagal menambahkan');
            },
        });
    }

    function openEditBrand(device: Brand) {
        editBrandForm.defaults({ name: device.name }).reset();
        editBrandTarget = device;
    }

    function closeEditBrand() {
        editBrandTarget = null;
        editBrandForm.reset();
    }

    function handleEditBrand() {
        if (!editBrandTarget) return;
        editBrandForm.put(listDevice.update({ id: editBrandTarget.id }).url, {
            onSuccess: () => {
                closeEditBrand();
                router.reload();
                toast.success('Perangkat berhasil diperbarui');
            },
            onError: (errors: FormDataErrors<{ name: string }>) => {
                toast.error(errors.name ?? 'Gagal memperbarui');
            },
        });
    }

    function openDeleteBrand(device: Brand) {
        deleteBrandTarget = device;
    }

    function closeDeleteBrand() {
        deleteBrandTarget = null;
    }

    function handleDeleteBrand() {
        if (!deleteBrandTarget) return;
        deleteBrandForm.delete(listDevice.destroy({ id: deleteBrandTarget.id }).url, {
            onSuccess: () => {
                closeDeleteBrand();
                router.reload();
                toast.success('Perangkat berhasil dihapus');
            },
            onError: () => {
                toast.error('Gagal menghapus perangkat');
            },
        });
    }

    // ═══════════════════════════════════════════════════════════════
    //  PHONE CRUD
    // ═══════════════════════════════════════════════════════════════

    function openAddPhone() {
        addPhoneForm.reset();
        addPhotos = [];
        addPhotosPreview = [];
        addThumbnailIdx = 0;
        if (brands.length > 0) addPhoneForm.brand_id = brands[0].id;
        showAddPhoneModal = true;
    }

    function closeAddPhone() {
        showAddPhoneModal = false;
        addPhoneForm.reset();
        addPhotos = [];
        addPhotosPreview = [];
        addThumbnailIdx = 0;
    }

    async function handleAddPhone() {
        // Reorder so thumbnail photo is first
        if (addPhotos.length > 0 && addThumbnailIdx > 0) {
            const reordered = [...addPhotos];
            const [thumb] = reordered.splice(addThumbnailIdx, 1);
            reordered.unshift(thumb);
            addPhoneForm.list_photos = reordered;
        } else {
            addPhoneForm.list_photos = addPhotos;
        }

        addPhoneForm.post(listDevice.phone.store().url, {
            forceFormData: true,
            onSuccess: () => {
                closeAddPhone();
                toast.success('Phone berhasil ditambahkan');
            },
            onError: (errors: Record<string, string>) => {
                const msg = Object.values(errors)[0] ?? 'Gagal menambahkan phone';
                toast.error(msg);
            },
        });
    }

    function openEditPhone(phone: PhoneList) {
        editPhoneForm
            .defaults({
                brand_id: phone.brand_id,
                model_id: phone.model_id,
                model_name: phone.model_name,
                model_type: phone.model_type,
                buy_date: phone.buy_date,
                price: phone.price,
                ram: phone.ram,
                storage: phone.storage,
                departemen: phone.departemen,
                thumbnail: phone.thumbnail ?? '',
                list_photos: phone.list_photos ?? [] as unknown,
            })
            .reset();
        editPhotos = [];
        editPhotosPreview = [];
        editThumbnailIdx = 0;
        editExistingThumbnailIdx = 0;
        editPhoneTarget = phone;
    }

    function closeEditPhone() {
        editPhoneTarget = null;
        editPhoneForm.reset();
        editPhotos = [];
        editPhotosPreview = [];
        editThumbnailIdx = 0;
        editExistingThumbnailIdx = 0;
    }

    async function handleEditPhone() {
        if (!editPhoneTarget) return;

        const existingPhotos = editPhoneTarget.list_photos ?? [];

        if (editPhotos.length > 0) {
            // New photos uploaded — reorder so picked thumbnail is first
            const reordered = [...editPhotos];
            if (editThumbnailIdx > 0 && editThumbnailIdx < reordered.length) {
                const [thumb] = reordered.splice(editThumbnailIdx, 1);
                reordered.unshift(thumb);
            }
            editPhoneForm.list_photos = reordered;
            // Clear thumbnail so backend uses the first new photo
            editPhoneForm.thumbnail = '';
        } else {
            // No new photos — use existing photo as thumbnail
            if (existingPhotos.length > 0) {
                editPhoneForm.thumbnail = existingPhotos[editExistingThumbnailIdx];
            }
            // Clear list_photos to avoid sending old URL strings as files
            editPhoneForm.setStore('list_photos', [] as unknown);
        }

        // POST with _method spoofing for multipart file upload support
        editPhoneForm
            .transform((data: Record<string, unknown>) => ({ ...data, _method: 'put' }))
            .post(listDevice.phone.update({ id: editPhoneTarget.id }).url, {
                forceFormData: true,
                onSuccess: () => {
                    closeEditPhone();
                    toast.success('Phone berhasil diperbarui');
                },
                onError: (errors: Record<string, string>) => {
                    const msg = Object.values(errors)[0] ?? 'Gagal memperbarui phone';
                    toast.error(msg);
                },
            });
    }

    function openDeletePhone(phone: PhoneList) {
        deletePhoneTarget = phone;
    }

    function closeDeletePhone() {
        deletePhoneTarget = null;
    }

    function handleDeletePhone() {
        if (!deletePhoneTarget) return;
        deletePhoneForm.delete(listDevice.phone.destroy({ id: deletePhoneTarget.id }).url, {
            onSuccess: () => {
                closeDeletePhone();
                router.reload();
                toast.success('Phone berhasil dihapus');
            },
            onError: () => {
                toast.error('Gagal menghapus phone');
            },
        });
    }

    function handleDeletePhoto(photoUrl: string) {
        if (!editPhoneTarget) return;
        deletingPhotoUrl = photoUrl;

        fetch(listDevice.phone.photo.destroy({ id: editPhoneTarget.id }).url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ photo_url: photoUrl }),
        })
            .then((res) => {
                if (!res.ok) throw new Error('Gagal menghapus foto');
                deletingPhotoUrl = null;
                // Update local state — modal stays open, photo removed instantly
                if (editPhoneTarget) {
                    const photos = [...(editPhoneTarget.list_photos ?? [])];
                    editPhoneTarget.list_photos = photos.filter(p => p !== photoUrl);
                    if (editPhoneTarget.thumbnail === photoUrl) {
                        const remaining = editPhoneTarget.list_photos ?? [];
                        editPhoneTarget.thumbnail = remaining[0] ?? null;
                    }
                    editExistingThumbnailIdx = 0;
                }
                toast.success('Foto berhasil dihapus');
            })
            .catch(() => {
                deletingPhotoUrl = null;
                toast.error('Gagal menghapus foto');
            });
    }
</script>

<div class="min-h-screen bg-mesh-pink">
    <!-- ──────── Sticky Header ──────── -->
    <div class="sticky top-0 z-40 border-b border-border/60 bg-card/80 backdrop-blur-xl">
        <div class="px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" onclick={goBack}>
                    <ArrowLeft class="size-5" />
                </Button>
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-xl bg-pink-500/20 flex items-center justify-center">
                            <Smartphone class="size-5 text-pink-400" />
                        </div>
                        <h1 class="text-xl font-bold tracking-tight">List Device</h1>
                    </div>
                    <p class="text-xs text-muted-foreground ml-0.5 mt-0.5">
                        {brands.length} brand &middot; {phoneLists.length} device
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6 space-y-6">

        <!-- ──────── Tab Navigation ──────── -->
        <div class="flex items-center gap-1 p-1 rounded-xl bg-card/60 border border-border/50 w-fit shadow-sm">
            {#each tabs as tab}
                <button
                    onclick={() => (activeTab = tab.id)}
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                        {activeTab === tab.id
                            ? 'bg-pink-500/15 text-pink-500 shadow-sm'
                            : 'text-muted-foreground hover:text-foreground hover:bg-muted/40'}"
                >
                    <tab.icon class="size-4" />
                {tab.label}
            </button>
        {/each}
    </div>

    <!-- ──────── Summary Cards ──────── -->
    <div class="flex items-center gap-3 flex-wrap">
        <!-- Total Budget -->
        <div class="flex items-center gap-3 rounded-xl bg-gradient-to-r from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 px-5 py-2.5 shadow-sm w-fit">
            <div class="size-9 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                <Banknote class="size-4 text-emerald-400" />
            </div>
            <div class="text-right">
                <p class="text-xs text-emerald-500/70 font-medium uppercase tracking-wider">Total Budget</p>
                <p class="text-base font-bold text-emerald-600 dark:text-emerald-300">{totalBudgetFormatted}</p>
            </div>
        </div>
        <!-- Removed Devices -->
        <div class="flex items-center gap-3 rounded-xl bg-gradient-to-r from-red-500/10 to-rose-600/5 border border-red-500/20 px-5 py-2.5 shadow-sm w-fit">
            <div class="size-9 rounded-lg bg-red-500/20 flex items-center justify-center">
                <Trash2 class="size-4 text-red-400" />
            </div>
            <div class="text-right">
                <p class="text-xs text-red-500/70 font-medium uppercase tracking-wider">Removed Device</p>
                <p class="text-base font-bold text-red-600 dark:text-red-300">{removedDevices}</p>
            </div>
        </div>
    </div>

        <!-- ════════════════════════════════════════════════════════
             BRANDS TAB
             ════════════════════════════════════════════════════════ -->
        {#if activeTab === 'brands'}
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="relative max-w-sm w-full">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                    <Input
                        bind:value={brandSearch}
                        placeholder="Cari ID atau nama brand..."
                        class="pl-10 h-10 bg-card/60 border-border/60"
                    />
                </div>
                <Button onclick={openAddBrand} class="gap-2 shadow-lg shadow-pink-500/20">
                    <Plus class="size-4" />
                    Tambah Brand
                </Button>
            </div>

            <Card.Root class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl">
                {#if filteredBrands.length === 0}
                    <div class="p-16 text-center">
                        <div class="size-14 rounded-2xl bg-pink-500/10 flex items-center justify-center mx-auto mb-4">
                            <Smartphone class="size-7 text-pink-400/60" />
                        </div>
                        <p class="text-muted-foreground font-medium">
                            {brandSearch ? 'Tidak ada brand yang cocok' : 'Belum ada brand terdaftar'}
                        </p>
                        {#if !brandSearch}
                            <Button variant="outline" size="sm" onclick={openAddBrand} class="mt-4 gap-2">
                                <Plus class="size-3.5" />
                                Tambah Brand
                            </Button>
                        {/if}
                    </div>
                {:else if brandsTable}
                    <!-- Search + Rows per page -->
                    <div class="flex items-center gap-3 px-6 py-3 border-b border-border/30 bg-muted/10 flex-wrap">
                        <DtSearch table={brandsTable} />
                        <RowsPerPage table={brandsTable} />
                    </div>

                    <!-- Brand Cards -->
                    <div class="p-4 space-y-3">
                        {#each brandsTable.rows as brand (brand.id)}
                            <div class="rounded-xl border border-border/60 bg-card p-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <Badge variant="outline" class="font-mono text-xs bg-pink-500/8 border-pink-300/30 text-pink-600 shrink-0">
                                        {brand.id}
                                    </Badge>
                                    <div class="min-w-0">
                                        <span class="font-medium text-sm">{brand.name}</span>
                                        <p class="text-xs text-muted-foreground">{formatDate(brand.created_at)}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-pink-500 hover:bg-pink-500/10" onclick={() => openEditBrand(brand)}>
                                        <Edit class="size-3.5" />
                                    </Button>
                                    <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-red-400 hover:bg-red-500/10" onclick={() => openDeleteBrand(brand)}>
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </div>
                        {/each}
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between px-6 py-3 border-t border-border/30 bg-muted/10">
                        <RowCount table={brandsTable} />
                        <Pagination table={brandsTable} />
                    </div>
                {/if}
            </Card.Root>
        {/if}

        <!-- ════════════════════════════════════════════════════════
             PHONE LIST TAB
             ════════════════════════════════════════════════════════ -->
        {#if activeTab === 'phones'}
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="relative max-w-sm w-full">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                    <Input
                        bind:value={phoneSearch}
                        placeholder="Cari model ID, nama, brand, IMEI, MAC..."
                        class="pl-10 h-10 bg-card/60 border-border/60"
                    />
                </div>
                <Button onclick={openAddPhone} class="gap-2 shadow-lg shadow-pink-500/20">
                    <Plus class="size-4" />
                    Tambah Phone
                </Button>
            </div>

            <!-- Departemen Filter -->
            <div class="flex items-center gap-2 flex-wrap px-6 pb-0">
                <button
                    onclick={() => departemenFilter = 'all'}
                    class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all duration-200
                        {departemenFilter === 'all'
                            ? 'bg-pink-500/15 text-pink-500 shadow-sm'
                            : 'text-muted-foreground hover:text-foreground hover:bg-muted/40'}"
                >
                    Semua ({phoneLists.length})
                </button>
                {#each departemenOptions as dept}
                    {@const count = phoneLists.filter(p => p.departemen === dept.id).length}
                    <button
                        onclick={() => departemenFilter = dept.id}
                        class="px-4 py-1.5 rounded-lg text-xs font-medium transition-all duration-200
                            {departemenFilter === dept.id
                                ? 'bg-pink-500/15 text-pink-500 shadow-sm'
                                : 'text-muted-foreground hover:text-foreground hover:bg-muted/40'}"
                    >
                        {dept.name} ({count})
                    </button>
                {/each}
            </div>

            <Card.Root class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl">
                {#if filteredPhones.length === 0}
                    <div class="p-16 text-center">
                        <div class="size-14 rounded-2xl bg-pink-500/10 flex items-center justify-center mx-auto mb-4">
                            <AppWindow class="size-7 text-pink-400/60" />
                        </div>
                        <p class="text-muted-foreground font-medium">
                            {phoneSearch || departemenFilter !== 'all' ? 'Tidak ada phone yang cocok' : 'Belum ada phone terdaftar'}
                        </p>
                        {#if !phoneSearch}
                            <Button variant="outline" size="sm" onclick={openAddPhone} class="mt-4 gap-2">
                                <Plus class="size-3.5" />
                                Tambah Phone
                            </Button>
                        {/if}
                    </div>
                {:else if phonesTable}
                    <!-- Search + Rows per page -->
                    <div class="flex items-center gap-3 px-6 py-3 border-b border-border/30 bg-muted/10 flex-wrap">
                        <DtSearch table={phonesTable} />
                        <RowsPerPage table={phonesTable} />
                    </div>

                    <!-- Phone Cards -->
                    <div class="p-4 space-y-3">
                        {#each phonesTable.rows as phone (phone.id)}
                            <div class="rounded-xl border border-border/60 bg-card p-4 space-y-3 {phone.deleted_at ? 'opacity-50' : ''}">
                                <!-- Header: Thumbnail + Nama + Status -->
                                <div class="flex items-start gap-3">
                                    <!-- Thumbnail -->
                                    <div class="shrink-0">
                                        {#if phone.thumbnail}
                                            <img src={storageUrl(phone.thumbnail)} alt={phone.model_name} class="size-14 rounded-xl object-cover ring-1 ring-pink-300/30" />
                                        {:else if phone.list_photos && phone.list_photos.length > 0}
                                            <img src={storageUrl(phone.list_photos[0])} alt={phone.model_name} class="size-14 rounded-xl object-cover ring-1 ring-pink-300/30" />
                                        {:else}
                                            <div class="size-14 rounded-xl bg-pink-500/10 flex items-center justify-center ring-1 ring-pink-300/20">
                                                <Image class="size-6 text-pink-400/60" />
                                            </div>
                                        {/if}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <div class="font-semibold text-sm leading-tight">{phone.model_name}</div>
                                                <code class="text-xs font-mono text-muted-foreground">{phone.model_id}</code>
                                            </div>
                                            <div class="flex items-center gap-1 shrink-0">
                                                {#if !phone.registered}
                                                    <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-emerald-500 hover:bg-emerald-500/10" onclick={() => openQr(phone)} title="QR Code">
                                                        <QrCode class="size-3.5" />
                                                    </Button>
                                                {/if}
                                                {#if !phone.approved}
                                                    <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-amber-500 hover:bg-amber-500/10" onclick={() => handleApprovePhone(phone)} title="Setujui" disabled={approvingId === phone.id}>
                                                        {#if approvingId === phone.id}
                                                            <div class="size-3.5 border-2 border-amber-500 border-t-transparent rounded-full animate-spin" />
                                                        {:else}
                                                            <Check class="size-3.5" />
                                                        {/if}
                                                    </Button>
                                                {/if}
                                                <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-pink-500 hover:bg-pink-500/10" onclick={() => openEditPhone(phone)}>
                                                    <Edit class="size-3.5" />
                                                </Button>
                                                <Button size="icon" variant="ghost" class="size-7 text-muted-foreground hover:text-red-400 hover:bg-red-500/10" onclick={() => openDeletePhone(phone)}>
                                                    <Trash2 class="size-3.5" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Info -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <Badge variant="outline" class="text-[10px] bg-violet-500/10 border-violet-300/30 text-violet-600 font-medium px-2 py-0">
                                            {phone.brand?.name ?? phone.brand_id}
                                        </Badge>
                                        {#if phone.departemen === 'QC'}
                                            <Badge class="bg-sky-500/15 text-sky-600 border-sky-300/30 text-[10px] px-2 py-0">{departemenOptions.find(d => d.id === phone.departemen)?.name ?? phone.departemen}</Badge>
                                        {:else}
                                            <Badge class="bg-amber-500/15 text-amber-600 border-amber-300/30 text-[10px] px-2 py-0">{departemenOptions.find(d => d.id === phone.departemen)?.name ?? phone.departemen}</Badge>
                                        {/if}
                                    </div>
                                    <div>
                                        {#if phone.model_type === 'Phone'}
                                            <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 gap-1 text-[10px] px-2 py-0"><Smartphone class="size-3" /> Phone</Badge>
                                        {:else}
                                            <Badge class="bg-amber-500/15 text-amber-600 border-amber-300/30 gap-1 text-[10px] px-2 py-0"><Tablet class="size-3" /> Tablet</Badge>
                                        {/if}
                                    </div>
                                    <div class="flex items-center gap-1"><Cpu class="size-3 text-muted-foreground" /> {phone.ram}</div>
                                    <div class="flex items-center gap-1"><HardDrive class="size-3 text-muted-foreground" /> {phone.storage}</div>
                                    <div class="text-muted-foreground">📅 {phone.buy_date}</div>
                                    <div class="font-semibold text-emerald-600 dark:text-emerald-400">{formatPrice(phone.price)}</div>
                                </div>

                                <!-- IMEI / MAC Address -->
                                <div class="flex flex-wrap gap-2 pt-1">
                                    {#if phone.imei}
                                        <Badge variant="outline" class="bg-primary/10 border-primary/30 text-primary gap-1.5 text-[11px] font-mono font-bold px-2.5 py-1">
                                            <span class="text-[9px] uppercase tracking-wider text-primary/70 font-bold">IMEI</span>
                                            {phone.imei}
                                        </Badge>
                                    {/if}
                                    {#if phone.mac_address}
                                        <Badge variant="outline" class="bg-primary/10 border-primary/30 text-primary gap-1.5 text-[11px] font-mono font-bold px-2.5 py-1">
                                            <span class="text-[9px] uppercase tracking-wider text-primary/70 font-bold">MAC</span>
                                            {phone.mac_address}
                                        </Badge>
                                    {/if}
                                    {#if !phone.imei && !phone.mac_address && phone.registered}
                                        <Badge variant="outline" class="bg-amber-500/10 border-amber-300/30 text-amber-500 gap-1 text-[10px]">
                                            IMEI/MAC belum diisi
                                        </Badge>
                                    {/if}
                                </div>

                                <!-- Status badges -->
                                <div class="flex flex-wrap gap-1.5 pt-1 border-t border-border/30">
                                    {#if phone.registered}
                                        <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 gap-1 text-xs"><CircleCheck class="size-3" /> Registered</Badge>
                                    {:else}
                                        <Badge variant="secondary" class="bg-rose-500/10 text-rose-500 border-rose-300/30 gap-1 text-xs"><CircleX class="size-3" /> Unregistered</Badge>
                                    {/if}
                                    {#if !phone.approved}
                                        <Badge variant="outline" class="bg-amber-500/10 border-amber-300/30 text-amber-500 gap-1 text-xs">Pending</Badge>
                                    {:else}
                                        <Badge class="bg-sky-500/15 text-sky-600 border-sky-300/30 gap-1 text-xs"><Check class="size-3" /> Disetujui</Badge>
                                    {/if}
                                    {#if phone.deleted_at}
                                        <Badge variant="outline" class="bg-red-500/10 border-red-300/30 text-red-500 gap-1 text-xs"><X class="size-3" /> Deleted</Badge>
                                    {/if}
                                </div>
                            </div>
                        {/each}
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between px-6 py-3 border-t border-border/30 bg-muted/10">
                        <RowCount table={phonesTable} />
                        <Pagination table={phonesTable} />
                    </div>
                {/if}
            </Card.Root>
        {/if}

        <!-- ════════════════════════════════════════════════════════
             DEPARTEMEN TAB
             ════════════════════════════════════════════════════════ -->
        {#if activeTab === 'departemen'}
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="relative max-w-sm w-full">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                    <Input
                        bind:value={phoneSearch}
                        placeholder="Cari ID atau nama departemen..."
                        class="pl-10 h-10 bg-card/60 border-border/60"
                    />
                </div>
                <Button onclick={() => { addDeptForm.reset(); showAddDeptModal = true; }} class="gap-2 shadow-lg shadow-pink-500/20">
                    <Plus class="size-4" />
                    Tambah Departemen
                </Button>
            </div>

            <Card.Root class="border-border/60 bg-card/70 backdrop-blur-xl overflow-hidden shadow-xl">
                {#if departemenOptions.length === 0}
                    <div class="p-16 text-center">
                        <div class="size-14 rounded-2xl bg-pink-500/10 flex items-center justify-center mx-auto mb-4">
                            <Layers class="size-7 text-pink-400/60" />
                        </div>
                        <p class="text-muted-foreground font-medium">Belum ada departemen</p>
                    </div>
                {:else}
                    <div class="p-4 space-y-3">
                        {#each departemenOptions as dept}
                            {@const deptCount = phoneLists.filter(p => p.departemen === dept.id).length}
                            <div class="rounded-xl border border-border/60 bg-card p-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    {#if dept.id === 'QC'}
                                        <Badge class="bg-sky-500/15 text-sky-600 border-sky-300/30 px-3 py-1">{dept.name}</Badge>
                                    {:else}
                                        <Badge class="bg-amber-500/15 text-amber-600 border-amber-300/30 px-3 py-1">{dept.name}</Badge>
                                    {/if}
                                    <span class="text-xs text-muted-foreground">{deptCount} perangkat</span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-pink-500 hover:bg-pink-500/10" onclick={() => { editDeptForm.defaults({ name: dept.name }).reset(); editDeptTarget = dept; }}>
                                        <Edit class="size-3.5" />
                                    </Button>
                                    <Button size="icon" variant="ghost" class="size-8 text-muted-foreground hover:text-red-400 hover:bg-red-500/10" onclick={() => { deleteDeptTarget = dept; }} disabled={deptCount > 0}>
                                        <Trash2 class="size-3.5" />
                                    </Button>
                                </div>
                            </div>
                        {/each}
                    </div>
                {/if}
            </Card.Root>
        {/if}
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     BRAND MODALS
     ════════════════════════════════════════════════════════════════ -->

<!-- ─── Add Brand Modal ─── -->
{#if showAddBrandModal}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                        <Plus class="size-5 text-pink-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Tambah Brand</Card.Title>
                        <Card.Description>Masukkan ID dan nama brand baru</Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="add-brand-id" class="text-sm font-medium">Device ID</Label>
                    <Input id="add-brand-id" bind:value={addBrandForm.id} placeholder="Contoh: DEV-011" class="h-10" />
                    {#if addBrandForm.errors.id}
                        <p class="text-xs text-rose-400">{addBrandForm.errors.id}</p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="add-brand-name" class="text-sm font-medium">Device Name</Label>
                    <Input id="add-brand-name" bind:value={addBrandForm.name} placeholder="Contoh: Samsung Galaxy A55" class="h-10" />
                    {#if addBrandForm.errors.name}
                        <p class="text-xs text-rose-400">{addBrandForm.errors.name}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeAddBrand} disabled={addBrandForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleAddBrand} disabled={addBrandForm.processing || !addBrandForm.id.trim() || !addBrandForm.name.trim()} class="gap-2">
                    {#if addBrandForm.processing}
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

<!-- ─── Edit Brand Modal ─── -->
{#if editBrandTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <Edit class="size-5 text-emerald-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Edit Brand</Card.Title>
                        <Card.Description>
                            <span class="font-mono text-xs">{editBrandTarget.id}</span>
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="edit-brand-name" class="text-sm font-medium">Device Name</Label>
                    <Input id="edit-brand-name" bind:value={editBrandForm.name} placeholder="Nama brand" class="h-10" />
                    {#if editBrandForm.errors.name}
                        <p class="text-xs text-rose-400">{editBrandForm.errors.name}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeEditBrand} disabled={editBrandForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleEditBrand} disabled={editBrandForm.processing || !editBrandForm.name.trim()} class="gap-2">
                    {#if editBrandForm.processing}
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

<!-- ─── Delete Brand Modal ─── -->
{#if deleteBrandTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-red-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                        <Trash2 class="size-5 text-red-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Hapus Brand</Card.Title>
                        <Card.Description>
                            Apakah Anda yakin ingin menghapus
                            <span class="font-medium text-foreground">{deleteBrandTarget.name}</span>?
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Card.Footer class="flex justify-end gap-3">
                <Button variant="outline" onclick={closeDeleteBrand} disabled={deleteBrandForm.processing}>
                    Batal
                </Button>
                <Button variant="destructive" onclick={handleDeleteBrand} disabled={deleteBrandForm.processing} class="gap-2">
                    {#if deleteBrandForm.processing}
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

<!-- ════════════════════════════════════════════════════════════════
     PHONE MODALS
     ════════════════════════════════════════════════════════════════ -->

<!-- ─── Add Phone Modal ─── -->
{#if showAddPhoneModal}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-lg border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                        <AppWindow class="size-5 text-pink-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Tambah Phone</Card.Title>
                        <Card.Description>Masukkan data phone baru</Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                <!-- Brand -->
                <div class="space-y-2">
                    <Label for="add-phone-brand" class="text-sm font-medium">Brand</Label>
                    <select
                        id="add-phone-brand"
                        bind:value={addPhoneForm.brand_id}
                        class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        {#each brands as brand}
                            <option value={brand.id}>{brand.name}</option>
                        {/each}
                    </select>
                    {#if addPhoneForm.errors.brand_id}
                        <p class="text-xs text-rose-400">{addPhoneForm.errors.brand_id}</p>
                    {/if}
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="add-phone-model-id" class="text-sm font-medium">Model ID</Label>
                        <Input id="add-phone-model-id" bind:value={addPhoneForm.model_id} placeholder="Xiaomi-12T-001" class="h-10" />
                        {#if addPhoneForm.errors.model_id}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.model_id}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="add-phone-model-name" class="text-sm font-medium">Model Name</Label>
                        <Input id="add-phone-model-name" bind:value={addPhoneForm.model_name} placeholder="Xiaomi 12T" class="h-10" />
                        {#if addPhoneForm.errors.model_name}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.model_name}</p>
                        {/if}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="add-phone-type" class="text-sm font-medium">Type</Label>
                        <select
                            id="add-phone-type"
                            bind:value={addPhoneForm.model_type}
                            class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="Phone">Phone</option>
                            <option value="Tablet">Tablet</option>
                        </select>
                        {#if addPhoneForm.errors.model_type}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.model_type}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="add-phone-buy-date" class="text-sm font-medium">Buy Date</Label>
                        <Input id="add-phone-buy-date" bind:value={addPhoneForm.buy_date} placeholder="2025-01-15" class="h-10" />
                        {#if addPhoneForm.errors.buy_date}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.buy_date}</p>
                        {/if}
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="add-phone-price" class="text-sm font-medium">Price</Label>
                        <Input id="add-phone-price" bind:value={addPhoneForm.price} placeholder="5000000" class="h-10" />
                        {#if addPhoneForm.errors.price}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.price}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="add-phone-ram" class="text-sm font-medium">RAM</Label>
                        <Input id="add-phone-ram" bind:value={addPhoneForm.ram} placeholder="8GB" class="h-10" />
                        {#if addPhoneForm.errors.ram}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.ram}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="add-phone-storage" class="text-sm font-medium">Storage</Label>
                        <Input id="add-phone-storage" bind:value={addPhoneForm.storage} placeholder="256GB" class="h-10" />
                        {#if addPhoneForm.errors.storage}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.storage}</p>
                        {/if}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="add-phone-imei" class="text-sm font-medium">IMEI <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                        <Input id="add-phone-imei" bind:value={addPhoneForm.imei} placeholder="15 digit IMEI" maxlength={15} class="h-10 font-mono text-sm" />
                        {#if addPhoneForm.errors.imei}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.imei}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="add-phone-mac" class="text-sm font-medium">MAC Address <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                        <Input id="add-phone-mac" bind:value={addPhoneForm.mac_address} placeholder="00:1A:2B:3C:4D:5E" maxlength={17} class="h-10 font-mono text-sm" />
                        {#if addPhoneForm.errors.mac_address}
                            <p class="text-xs text-rose-400">{addPhoneForm.errors.mac_address}</p>
                        {/if}
                    </div>
                </div>

                <!-- ─── Departemen ─── -->
                <div class="space-y-2">
                    <Label for="add-phone-departemen" class="text-sm font-medium">Departemen</Label>
                    <select
                        id="add-phone-departemen"
                        bind:value={addPhoneForm.departemen}
                        class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        {#each departemenOptions as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>
                    {#if addPhoneForm.errors.departemen}
                        <p class="text-xs text-rose-400">{addPhoneForm.errors.departemen}</p>
                    {/if}
                </div>

                <!-- ─── Foto Upload ─── -->
                <div class="space-y-2">
                    <Label class="text-sm font-medium">Foto Perangkat</Label>
                    <p class="text-xs text-muted-foreground">Foto pertama akan menjadi thumbnail</p>
                    <FileDropZone.Root
                        accept="image/*"
                        maxFileSize={FileDropZone.MEGABYTE * 5}
                        fileCount={addPhotos.length}
                        onUpload={async (files) => {
                            for (const file of files) {
                                addPhotos = [...addPhotos, file];
                                addPhotosPreview = [...addPhotosPreview, URL.createObjectURL(file)];
                            }
                        }}
                    >
                        <FileDropZone.Trigger />
                    </FileDropZone.Root>
                    {#if addPhotosPreview.length > 0}
                        <div class="flex flex-wrap gap-2 mt-2">
                            {#each addPhotosPreview as preview, i}
                                <div class="relative group cursor-pointer" onclick={() => addThumbnailIdx = i}>
                                    <img src={preview} alt="Photo {i + 1}"
                                        class="size-16 rounded-lg object-cover transition-all duration-200
                                            {i === addThumbnailIdx
                                                ? 'ring-2 ring-pink-500 ring-offset-2 ring-offset-background scale-110'
                                                : 'ring-1 ring-pink-300/30 hover:ring-pink-400/60'}" />
                                    {#if i === addThumbnailIdx}
                                        <span class="absolute -top-2 -left-2 size-5 rounded-full bg-pink-500 text-[9px] font-bold text-white flex items-center justify-center shadow-lg">T</span>
                                    {/if}
                                    <button
                                        type="button"
                                        onclick={(e) => { e.stopPropagation(); addPhotos = addPhotos.filter((_, idx) => idx !== i); addPhotosPreview = addPhotosPreview.filter((_, idx) => idx !== i); if (addThumbnailIdx >= addPhotos.length) addThumbnailIdx = Math.max(0, addPhotos.length - 1); }}
                                        class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                    >
                                        <X class="size-3 text-white" />
                                    </button>
                                </div>
                            {/each}
                        </div>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeAddPhone} disabled={addPhoneForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleAddPhone} disabled={addPhoneForm.processing} class="gap-2">
                    {#if addPhoneForm.processing}
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

<!-- ─── Edit Phone Modal ─── -->
{#if editPhoneTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-lg border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <Edit class="size-5 text-emerald-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Edit Phone</Card.Title>
                        <Card.Description>
                            <span class="font-mono text-xs">{editPhoneTarget.model_id}</span>
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                <!-- Brand -->
                <div class="space-y-2">
                    <Label for="edit-phone-brand" class="text-sm font-medium">Brand</Label>
                    <select
                        id="edit-phone-brand"
                        bind:value={editPhoneForm.brand_id}
                        class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        {#each brands as brand}
                            <option value={brand.id}>{brand.name}</option>
                        {/each}
                    </select>
                    {#if editPhoneForm.errors.brand_id}
                        <p class="text-xs text-rose-400">{editPhoneForm.errors.brand_id}</p>
                    {/if}
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-phone-model-id" class="text-sm font-medium">Model ID</Label>
                        <Input id="edit-phone-model-id" bind:value={editPhoneForm.model_id} class="h-10" />
                        {#if editPhoneForm.errors.model_id}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.model_id}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-model-name" class="text-sm font-medium">Model Name</Label>
                        <Input id="edit-phone-model-name" bind:value={editPhoneForm.model_name} class="h-10" />
                        {#if editPhoneForm.errors.model_name}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.model_name}</p>
                        {/if}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-phone-type" class="text-sm font-medium">Type</Label>
                        <select
                            id="edit-phone-type"
                            bind:value={editPhoneForm.model_type}
                            class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="Phone">Phone</option>
                            <option value="Tablet">Tablet</option>
                        </select>
                        {#if editPhoneForm.errors.model_type}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.model_type}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-buy-date" class="text-sm font-medium">Buy Date</Label>
                        <Input id="edit-phone-buy-date" bind:value={editPhoneForm.buy_date} class="h-10" />
                        {#if editPhoneForm.errors.buy_date}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.buy_date}</p>
                        {/if}
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-phone-price" class="text-sm font-medium">Price</Label>
                        <Input id="edit-phone-price" bind:value={editPhoneForm.price} class="h-10" />
                        {#if editPhoneForm.errors.price}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.price}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-ram" class="text-sm font-medium">RAM</Label>
                        <Input id="edit-phone-ram" bind:value={editPhoneForm.ram} class="h-10" />
                        {#if editPhoneForm.errors.ram}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.ram}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-storage" class="text-sm font-medium">Storage</Label>
                        <Input id="edit-phone-storage" bind:value={editPhoneForm.storage} class="h-10" />
                        {#if editPhoneForm.errors.storage}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.storage}</p>
                        {/if}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-phone-imei" class="text-sm font-medium">IMEI <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                        <Input id="edit-phone-imei" bind:value={editPhoneForm.imei} placeholder="15 digit IMEI" maxlength={15} class="h-10 font-mono text-sm" />
                        {#if editPhoneForm.errors.imei}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.imei}</p>
                        {/if}
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-mac" class="text-sm font-medium">MAC Address <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                        <Input id="edit-phone-mac" bind:value={editPhoneForm.mac_address} placeholder="00:1A:2B:3C:4D:5E" maxlength={17} class="h-10 font-mono text-sm" />
                        {#if editPhoneForm.errors.mac_address}
                            <p class="text-xs text-rose-400">{editPhoneForm.errors.mac_address}</p>
                        {/if}
                    </div>
                </div>

                <!-- ─── Departemen ─── -->
                <div class="space-y-2">
                    <Label for="edit-phone-departemen" class="text-sm font-medium">Departemen</Label>
                    <select
                        id="edit-phone-departemen"
                        bind:value={editPhoneForm.departemen}
                        class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        {#each departemenOptions as dept}
                            <option value={dept.id}>{dept.name}</option>
                        {/each}
                    </select>
                    {#if editPhoneForm.errors.departemen}
                        <p class="text-xs text-rose-400">{editPhoneForm.errors.departemen}</p>
                    {/if}
                </div>

                <!-- ─── Foto Upload ─── -->
                <div class="space-y-2">
                    <Label class="text-sm font-medium">Foto Perangkat</Label>
                    <p class="text-xs text-muted-foreground">Upload foto tambahan (foto pertama jadi thumbnail)</p>

                    {#if editPhoneTarget.list_photos && editPhoneTarget.list_photos.length > 0 && editPhotosPreview.length === 0}
                        <div class="flex flex-wrap gap-2 mb-2">
                            {#each editPhoneTarget.list_photos as photo, i}
                                <div class="relative group">
                                    <div onclick={() => editExistingThumbnailIdx = i} class="cursor-pointer">
                                        <img src={storageUrl(photo)} alt="Gallery {i + 1}"
                                            class="size-16 rounded-lg object-cover transition-all duration-200
                                                {i === editExistingThumbnailIdx
                                                    ? 'ring-2 ring-emerald-500 ring-offset-2 ring-offset-background scale-110'
                                                    : 'ring-1 ring-emerald-300/30 hover:ring-emerald-400/60'}" />
                                        {#if i === editExistingThumbnailIdx}
                                            <span class="absolute -top-2 -left-2 size-5 rounded-full bg-emerald-500 text-[9px] font-bold text-white flex items-center justify-center shadow-lg">T</span>
                                        {/if}
                                    </div>
                                    <button
                                        onclick={() => handleDeletePhoto(photo)}
                                        disabled={deletingPhotoUrl === photo}
                                        class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-500 shadow-sm"
                                        title="Hapus foto"
                                    >
                                        {#if deletingPhotoUrl === photo}
                                            <div class="size-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                        {:else}
                                            <X class="size-3" />
                                        {/if}
                                    </button>
                                </div>
                            {/each}
                        </div>
                    {/if}

                    <FileDropZone.Root
                        accept="image/*"
                        maxFileSize={FileDropZone.MEGABYTE * 5}
                        fileCount={editPhotos.length}
                        onUpload={async (files) => {
                            for (const file of files) {
                                editPhotos = [...editPhotos, file];
                                editPhotosPreview = [...editPhotosPreview, URL.createObjectURL(file)];
                            }
                        }}
                    >
                        <FileDropZone.Trigger />
                    </FileDropZone.Root>
                    {#if editPhotosPreview.length > 0}
                        <div class="flex flex-wrap gap-2 mt-2">
                            {#each editPhotosPreview as preview, i}
                                <div class="relative group" onclick={() => editThumbnailIdx = i}>
                                    <img src={preview} alt="Photo {i + 1}"
                                        class="size-16 rounded-lg object-cover transition-all duration-200
                                            {i === editThumbnailIdx
                                                ? 'ring-2 ring-emerald-500 ring-offset-2 ring-offset-background scale-110'
                                                : 'ring-1 ring-emerald-300/30 hover:ring-emerald-400/60'}" />
                                    {#if i === editThumbnailIdx}
                                        <span class="absolute -top-2 -left-2 size-5 rounded-full bg-emerald-500 text-[9px] font-bold text-white flex items-center justify-center shadow-lg">T</span>
                                    {/if}
                                    <button
                                        type="button"
                                        onclick={(e) => { e.stopPropagation(); editPhotos = editPhotos.filter((_, idx) => idx !== i); editPhotosPreview = editPhotosPreview.filter((_, idx) => idx !== i); if (editThumbnailIdx >= editPhotos.length) editThumbnailIdx = Math.max(0, editPhotos.length - 1); }}
                                        class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                    >
                                        <X class="size-3 text-white" />
                                    </button>
                                </div>
                            {/each}
                        </div>
                    {/if}
                    <p class="text-xs text-muted-foreground">Klik foto untuk pilih thumbnail (T) &middot; Upload baru untuk tambah galeri</p>
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={closeEditPhone} disabled={editPhoneForm.processing}>
                    Batal
                </Button>
                <Button onclick={handleEditPhone} disabled={editPhoneForm.processing} class="gap-2">
                    {#if editPhoneForm.processing}
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

<!-- ════════════════════════════════════════════════════════════════
     QR CODE MODAL
     ════════════════════════════════════════════════════════════════ -->
{#if showQrModal && qrTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-sm border-emerald-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header class="text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <ScanQrCode class="size-5 text-emerald-400" />
                    </div>
                    <div class="text-left">
                        <Card.Title class="text-lg">QR Code</Card.Title>
                        <Card.Description>
                            Scan untuk mendaftarkan <span class="font-medium">{qrTarget.model_name}</span>
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-6 pb-2 flex flex-col items-center gap-4">
                <div class="rounded-2xl bg-white p-4 shadow-lg ring-1 ring-emerald-500/20">
                    <QRCode
                        value={getQrValue(qrTarget)}
                        size={220}
                        fgColor="#000000"
                        bgColor="#ffffff"
                    />
                </div>
                <div class="text-center space-y-1">
                    <Badge variant="outline" class="font-mono text-xs bg-emerald-500/10 border-emerald-300/30 text-emerald-600 dark:text-emerald-300">
                        {qrTarget.model_id}
                    </Badge>
                    <p class="text-xs text-muted-foreground mt-2">
                        Buka aplikasi di HP, pilih "Daftarkan Perangkat" lalu scan QR ini
                    </p>
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-center pt-2">
                <Button variant="outline" onclick={closeQr} class="gap-2">
                    <X class="size-4" />
                    Tutup
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}

<!-- ─── Delete Phone Modal ─── -->
{#if deletePhoneTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-red-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                        <Trash2 class="size-5 text-red-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Hapus Phone</Card.Title>
                        <Card.Description>
                            Apakah Anda yakin ingin menghapus
                            <span class="font-medium text-foreground">{deletePhoneTarget.model_name}</span>
                            (<span class="font-mono text-xs">{deletePhoneTarget.model_id}</span>)?
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Card.Footer class="flex justify-end gap-3">
                <Button variant="outline" onclick={closeDeletePhone} disabled={deletePhoneForm.processing}>
                    Batal
                </Button>
                <Button variant="destructive" onclick={handleDeletePhone} disabled={deletePhoneForm.processing} class="gap-2">
                    {#if deletePhoneForm.processing}
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

<!-- ════════════════════════════════════════════════════════════════
     DEPARTEMEN MODALS
     ════════════════════════════════════════════════════════════════ -->

<!-- ─── Add Departemen Modal ─── -->
{#if showAddDeptModal}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
                        <Layers class="size-5 text-pink-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Tambah Departemen</Card.Title>
                        <Card.Description>Masukkan ID dan nama departemen baru</Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="add-dept-id" class="text-sm font-medium">ID Departemen</Label>
                    <Input id="add-dept-id" bind:value={addDeptForm.id} placeholder="Contoh: QC" class="h-10" />
                    {#if addDeptForm.errors.id}
                        <p class="text-xs text-rose-400">{addDeptForm.errors.id}</p>
                    {/if}
                </div>
                <div class="space-y-2">
                    <Label for="add-dept-name" class="text-sm font-medium">Nama Departemen</Label>
                    <Input id="add-dept-name" bind:value={addDeptForm.name} placeholder="Contoh: Quality Control" class="h-10" />
                    {#if addDeptForm.errors.name}
                        <p class="text-xs text-rose-400">{addDeptForm.errors.name}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={() => { showAddDeptModal = false; addDeptForm.reset(); }} disabled={addDeptForm.processing}>
                    Batal
                </Button>
                <Button onclick={() => {
                    addDeptForm.post(listDevice.departemen.store().url, {
                        onSuccess: () => {
                            showAddDeptModal = false;
                            addDeptForm.reset();
                            router.reload();
                            toast.success('Departemen berhasil ditambahkan');
                        },
                        onError: (errors: Record<string, string>) => {
                            toast.error(errors.id ?? errors.name ?? 'Gagal menambahkan departemen');
                        },
                    });
                }} disabled={addDeptForm.processing || !addDeptForm.id.trim() || !addDeptForm.name.trim()} class="gap-2">
                    {#if addDeptForm.processing}
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

<!-- ─── Edit Departemen Modal ─── -->
{#if editDeptTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <Edit class="size-5 text-emerald-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Edit Departemen</Card.Title>
                        <Card.Description>
                            <span class="font-mono text-xs">{editDeptTarget.id}</span>
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4">
                <div class="space-y-2">
                    <Label for="edit-dept-name" class="text-sm font-medium">Nama Departemen</Label>
                    <Input id="edit-dept-name" bind:value={editDeptForm.name} placeholder="Nama departemen" class="h-10" />
                    {#if editDeptForm.errors.name}
                        <p class="text-xs text-rose-400">{editDeptForm.errors.name}</p>
                    {/if}
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-end gap-3 pt-2">
                <Button variant="outline" onclick={() => { editDeptTarget = null; editDeptForm.reset(); }} disabled={editDeptForm.processing}>
                    Batal
                </Button>
                <Button onclick={() => {
                    if (!editDeptTarget) return;
                    editDeptForm.put(listDevice.departemen.update({ id: editDeptTarget.id }).url, {
                        onSuccess: () => {
                            editDeptTarget = null;
                            editDeptForm.reset();
                            router.reload();
                            toast.success('Departemen berhasil diperbarui');
                        },
                        onError: (errors: Record<string, string>) => {
                            toast.error(errors.name ?? 'Gagal memperbarui departemen');
                        },
                    });
                }} disabled={editDeptForm.processing || !editDeptForm.name.trim()} class="gap-2">
                    {#if editDeptForm.processing}
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

<!-- ─── Delete Departemen Modal ─── -->
{#if deleteDeptTarget}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm"
         role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-md border-red-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                        <Trash2 class="size-5 text-red-400" />
                    </div>
                    <div>
                        <Card.Title class="text-lg">Hapus Departemen</Card.Title>
                        <Card.Description>
                            Apakah Anda yakin ingin menghapus
                            <span class="font-medium text-foreground">{deleteDeptTarget.name}</span>?
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Card.Footer class="flex justify-end gap-3">
                <Button variant="outline" onclick={() => { deleteDeptTarget = null; }} disabled={deleteDeptForm.processing}>
                    Batal
                </Button>
                <Button variant="destructive" onclick={() => {
                    if (!deleteDeptTarget) return;
                    deleteDeptForm.delete(listDevice.departemen.destroy({ id: deleteDeptTarget.id }).url, {
                        onSuccess: () => {
                            deleteDeptTarget = null;
                            router.reload();
                            toast.success('Departemen berhasil dihapus');
                        },
                        onError: (errors: { message?: string }) => {
                            toast.error(errors.message ?? 'Gagal menghapus departemen');
                        },
                    });
                }} disabled={deleteDeptForm.processing} class="gap-2">
                    {#if deleteDeptForm.processing}
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

<Toaster richColors position="top-right" duration={3000} />

<style>
    :global(.svelte-simple-datatable table) { border-collapse: separate; border-spacing: 0; width: 100%; background: inherit; }
    :global(.svelte-simple-datatable table thead) { position: sticky; inset-block-start: 0; background: inherit; z-index: 1; }
    :global(.svelte-simple-datatable thead tr) { background: inherit; }
    :global(.svelte-simple-datatable thead tr th) { background: inherit; }
    :global(.svelte-simple-datatable thead tr:first-child th) { padding: 8px 20px; background: inherit; }
    :global(.svelte-simple-datatable tbody) { background: inherit; }
    :global(.svelte-simple-datatable tbody tr) { transition: background, 0.2s; background: inherit; }
    :global(.svelte-simple-datatable tbody tr:hover) { background: var(--grey-lighten-3, #fafafa); }
    :global(.svelte-simple-datatable tbody td) { padding: 4px 20px; border-right: 1px solid var(--grey-lighten, #eee); border-bottom: 1px solid var(--grey-lighten, #eee); background: inherit; }
    :global(.svelte-simple-datatable tbody td:last-child) { border-right: none; }
    :global(.svelte-simple-datatable u.highlight) { text-decoration: none; background: rgba(251, 192, 45, 0.6); border-radius: 2px; }
    :global(.svelte-simple-datatable footer.divider) { border-top: 1px solid var(--grey, #e0e0e0); }

    @media (max-width: 640px) {
        :global(.svelte-simple-datatable) { height: auto !important; }
        :global(.svelte-simple-datatable article) {
            overflow: visible !important;
            height: auto !important;
        }
        :global(.svelte-simple-datatable table) { display: block !important; }
        :global(.svelte-simple-datatable thead) { display: none !important; }
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
