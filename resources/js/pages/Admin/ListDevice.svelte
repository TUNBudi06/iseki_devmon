<script lang="ts">
    import { router, useHttp, useForm } from '@inertiajs/svelte';
    import { Button } from '$shadcn/components/ui/button';
    import { Badge } from '$shadcn/components/ui/badge';
    import { TableHandler } from '@vincjo/datatables';
    import { Smartphone, ArrowLeft, Trash2, Banknote, QrCode, Printer } from '@lucide/svelte';
    import { toast } from 'svelte-sonner';
    import { listDevice, printQrSelect } from '$routes/admin';
    import { goBack } from '$lib/navigation.ts';
    import type { FormDataErrors } from '@inertiajs/core';
    // ─── Components ──────────────────────────────────────────────
    import BrandsTab from './ListDevice/BrandsTab.svelte';
    import PhonesTab from './ListDevice/PhonesTab.svelte';
    import DepartemenTab from './ListDevice/DepartemenTab.svelte';
    import BrandFormModal from './ListDevice/BrandFormModal.svelte';
    import AddPhoneModal from './ListDevice/AddPhoneModal.svelte';
    import EditPhoneModal from './ListDevice/EditPhoneModal.svelte';
    import ConfirmDeleteModal from './ListDevice/ConfirmDeleteModal.svelte';
    import QrModal from './ListDevice/QrModal.svelte';
    import DepartemenFormModal from './ListDevice/DepartemenFormModal.svelte';

    // ─── Types ───────────────────────────────────────────────────
    import type { Brand, PhoneList, DepartemenOption } from './ListDevice/types';

    // ─── Props ───────────────────────────────────────────────────
    let { brands, phoneLists, departemenOptions }: {
        brands: Brand[];
        phoneLists: PhoneList[];
        departemenOptions: DepartemenOption[];
    } = $props();

    // ─── Tabs ────────────────────────────────────────────────────
    const tabs = [
        { id: 'brands', label: 'Brand / Perangkat', icon: Smartphone },
        { id: 'phones', label: 'List Devices', icon: QrCode },
        { id: 'departemen', label: 'Departemen', icon: Trash2 },
    ] as const;
    type TabId = (typeof tabs)[number]['id'];
    let activeTab: TabId = $state('brands');

    // ─── Search & Filter ─────────────────────────────────────────
    let brandSearch = $state('');
    let phoneSearch = $state('');
    let departemenFilter = $state('all');
    let deptSearch = $state('');

    // ─── Derived ─────────────────────────────────────────────────
    const filteredBrands = $derived.by(() => {
        if (!brandSearch.trim()) return brands;
        const q = brandSearch.toLowerCase();
        return brands.filter(d => d.id.toLowerCase().includes(q) || d.name.toLowerCase().includes(q));
    });

    const filteredPhones = $derived.by(() => {
        let items = phoneLists;
        if (departemenFilter !== 'all') items = items.filter(p => p.departemen === departemenFilter);
        if (phoneSearch.trim()) {
            const q = phoneSearch.toLowerCase();
            items = items.filter(p => p.model_id.toLowerCase().includes(q) || p.model_name.toLowerCase().includes(q) || p.brand?.name.toLowerCase().includes(q) || p.model_type.toLowerCase().includes(q) || p.imei?.toLowerCase().includes(q) || p.mac_address?.toLowerCase().includes(q));
        }
        return items;
    });

    const filteredDepts = $derived.by(() => {
        if (!deptSearch.trim()) return departemenOptions;
        const q = deptSearch.toLowerCase();
        return departemenOptions.filter(d => d.id.toLowerCase().includes(q) || d.name.toLowerCase().includes(q));
    });

    const brandsTable = $derived(filteredBrands.length > 0 ? new TableHandler(filteredBrands, { rowsPerPage: 10 }) : null);
    const phonesTable = $derived(filteredPhones.length > 0 ? new TableHandler(filteredPhones, { rowsPerPage: 10 }) : null);

    // ─── Helpers ─────────────────────────────────────────────────
    function parsePrice(val: string): number {
        return parseInt(val.replace(/\D/g, ''), 10) || 0;
    }
    const budgetPerDept = $derived.by(() => {
        const map = new Map<string, number>();
        for (const p of phoneLists) map.set(p.departemen, (map.get(p.departemen) ?? 0) + parsePrice(p.price));
        return map;
    });
    const removedDevices = $derived(phoneLists.filter(p => p.deleted_at !== null).length);

    // ─── Brand CRUD ──────────────────────────────────────────────
    const addBrandForm = useHttp({ id: '', name: '' });
    let showAddBrandModal = $state(false);
    const editBrandForm = useHttp({ name: '' });
    let editBrandTarget = $state<Brand | null>(null);
    const deleteBrandForm = useHttp({});
    let deleteBrandTarget = $state<Brand | null>(null);

    function handleAddBrand() {
        addBrandForm.post(listDevice().url, {
            onSuccess: () => { showAddBrandModal = false; addBrandForm.reset(); router.reload(); toast.success('Perangkat berhasil ditambahkan'); },
            onError: (e: FormDataErrors<{ id: string; name: string }>) => toast.error(e.id ?? e.name ?? 'Gagal menambahkan'),
        });
    }
    function handleEditBrand() {
        if (!editBrandTarget) return;
        editBrandForm.put(listDevice.update({ id: editBrandTarget.id }).url, {
            onSuccess: () => { editBrandTarget = null; editBrandForm.reset(); router.reload(); toast.success('Perangkat berhasil diperbarui'); },
            onError: (e: FormDataErrors<{ name: string }>) => toast.error(e.name ?? 'Gagal memperbarui'),
        });
    }
    function handleDeleteBrand() {
        if (!deleteBrandTarget) return;
        deleteBrandForm.delete(listDevice.destroy({ id: deleteBrandTarget.id }).url, {
            onSuccess: () => { deleteBrandTarget = null; router.reload(); toast.success('Perangkat berhasil dihapus'); },
            onError: () => toast.error('Gagal menghapus perangkat'),
        });
    }

    // ─── Phone CRUD ──────────────────────────────────────────────
    const addPhoneForm = useForm({ brand_id: '', model_id: '', model_name: '', model_type: 'Phone', buy_date: '', price: '', ram: '', storage: '', departemen: 'Production', imei: '', mac_address: '', list_photos: [] as unknown });
    let showAddPhoneModal = $state(false);
    let addPhotos = $state<File[]>([]);
    let addPhotosPreview = $state<string[]>([]);
    let addThumbnailIdx = $state(0);

    const editPhoneForm = useForm({ brand_id: '', model_id: '', model_name: '', model_type: 'Phone', buy_date: '', price: '', ram: '', storage: '', departemen: 'Production', imei: '', mac_address: '', list_photos: [] as unknown, thumbnail: '' });
    let editPhotos = $state<File[]>([]);
    let editPhotosPreview = $state<string[]>([]);
    let editThumbnailIdx = $state(0);
    let editExistingThumbnailIdx = $state(0);
    let editPhoneTarget = $state<PhoneList | null>(null);
    let deletingPhotoUrl = $state<string | null>(null);

    const deletePhoneForm = useHttp({});
    let deletePhoneTarget = $state<PhoneList | null>(null);

    // ─── QR ──────────────────────────────────────────────────────
    let qrTarget = $state<PhoneList | null>(null);
    let showQrModal = $state(false);

    // ─── Departemen CRUD ─────────────────────────────────────────
    const addDeptForm = useHttp({ id: '', name: '', color: '#f59e0b' });
    let showAddDeptModal = $state(false);
    const editDeptForm = useHttp({ name: '', color: '#f59e0b' });
    let editDeptTarget = $state<DepartemenOption | null>(null);
    const deleteDeptForm = useHttp({});
    let deleteDeptTarget = $state<DepartemenOption | null>(null);

    // ─── Actions ─────────────────────────────────────────────────
    function openAddPhone() {
        addPhoneForm.reset();
        addPhotos = []; addPhotosPreview = []; addThumbnailIdx = 0;
        if (brands.length > 0) addPhoneForm.brand_id = brands[0].id;
        showAddPhoneModal = true;
    }

    async function handleAddPhone() {
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
            onSuccess: () => { showAddPhoneModal = false; toast.success('Phone berhasil ditambahkan'); },
            onError: (errors: Record<string, string>) => toast.error(Object.values(errors)[0] ?? 'Gagal menambahkan phone'),
        });
    }

    function openEditPhone(phone: PhoneList) {
        editPhoneForm.defaults({ brand_id: phone.brand_id, model_id: phone.model_id, model_name: phone.model_name, model_type: phone.model_type, buy_date: phone.buy_date, price: phone.price, ram: phone.ram, storage: phone.storage, departemen: phone.departemen, imei: phone.imei ?? '', mac_address: phone.mac_address ?? '', thumbnail: phone.thumbnail ?? '', list_photos: phone.list_photos ?? [] as unknown }).reset();
        editPhotos = []; editPhotosPreview = []; editThumbnailIdx = 0; editExistingThumbnailIdx = 0;
        editPhoneTarget = phone;
    }

    async function handleEditPhone() {
        if (!editPhoneTarget) return;
        const existingPhotos = editPhoneTarget.list_photos ?? [];
        if (editPhotos.length > 0) {
            const reordered = [...editPhotos];
            if (editThumbnailIdx > 0 && editThumbnailIdx < reordered.length) { const [thumb] = reordered.splice(editThumbnailIdx, 1); reordered.unshift(thumb); }
            editPhoneForm.list_photos = reordered;
            editPhoneForm.thumbnail = '';
        } else {
            if (existingPhotos.length > 0) editPhoneForm.thumbnail = existingPhotos[editExistingThumbnailIdx];
            editPhoneForm.setStore('list_photos', [] as unknown);
        }
        editPhoneForm.transform((data: Record<string, unknown>) => ({ ...data, _method: 'put' })).post(listDevice.phone.update({ id: editPhoneTarget.id }).url, {
            forceFormData: true,
            onSuccess: () => { editPhoneTarget = null; toast.success('Phone berhasil diperbarui'); },
            onError: (errors: Record<string, string>) => toast.error(Object.values(errors)[0] ?? 'Gagal memperbarui phone'),
        });
    }

    function handleDeletePhone() {
        if (!deletePhoneTarget) return;
        deletePhoneForm.delete(listDevice.phone.destroy({ id: deletePhoneTarget.id }).url, {
            onSuccess: () => { deletePhoneTarget = null; router.reload(); toast.success('Phone berhasil dihapus'); },
            onError: () => toast.error('Gagal menghapus phone'),
        });
    }

    function handleDeletePhoto(photoUrl: string) {
        if (!editPhoneTarget) return;
        deletingPhotoUrl = photoUrl;
        fetch(listDevice.phone.photo.destroy({ id: editPhoneTarget.id }).url, {
            method: 'DELETE', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ photo_url: photoUrl }),
        }).then(res => {
            if (!res.ok) throw new Error('Gagal');
            deletingPhotoUrl = null;
            if (editPhoneTarget) {
                const photos = [...(editPhoneTarget.list_photos ?? [])];
                editPhoneTarget.list_photos = photos.filter(p => p !== photoUrl);
                if (editPhoneTarget.thumbnail === photoUrl) editPhoneTarget.thumbnail = (editPhoneTarget.list_photos ?? [])[0] ?? null;
                editExistingThumbnailIdx = 0;
            }
            toast.success('Foto berhasil dihapus');
        }).catch(() => { deletingPhotoUrl = null; toast.error('Gagal menghapus foto'); });
    }

    function handleDeptAdd() {
        addDeptForm.post(listDevice.departemen.store().url, {
            onSuccess: () => { showAddDeptModal = false; addDeptForm.reset(); router.reload(); toast.success('Departemen berhasil ditambahkan'); },
            onError: (e: Record<string, string>) => toast.error(e.id ?? e.name ?? 'Gagal menambahkan'),
        });
    }

    function handleDeptEdit() {
        if (!editDeptTarget) return;
        editDeptForm.put(listDevice.departemen.update({ id: editDeptTarget.id }).url, {
            onSuccess: () => { editDeptTarget = null; editDeptForm.reset(); router.reload(); toast.success('Departemen berhasil diperbarui'); },
            onError: (e: Record<string, string>) => toast.error(e.name ?? 'Gagal memperbarui'),
        });
    }

    function handleDeptDelete() {
        if (!deleteDeptTarget) return;
        deleteDeptForm.delete(listDevice.departemen.destroy({ id: deleteDeptTarget.id }).url, {
            onSuccess: () => { deleteDeptTarget = null; router.reload(); toast.success('Departemen berhasil dihapus'); },
            onError: (e: { message?: string }) => toast.error(e.message ?? 'Gagal menghapus'),
        });
    }

    function goToPrintQr() {
        router.visit(printQrSelect().url);
    }
</script>

<svelte:head>
    <title>List Device</title>
</svelte:head>

<div class="min-h-screen bg-mesh-pink">
    <!-- Header -->
    <div class="sticky top-0 z-40 border-b border-border/60 bg-card/80 backdrop-blur-xl">
        <div class="px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" onclick={goBack}><ArrowLeft class="size-5" /></Button>
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="size-9 rounded-xl bg-pink-500/20 flex items-center justify-center"><Smartphone class="size-5 text-pink-400" /></div>
                        <h1 class="text-xl font-bold tracking-tight">List Device</h1>
                    </div>
                    <p class="text-xs text-muted-foreground ml-0.5 mt-0.5">{brands.length} brand &middot; {phoneLists.length} device</p>
                </div>
            </div>
            <Button variant="outline" size="sm" onclick={goToPrintQr} class="gap-1.5">
                <Printer class="size-4" /> Print QR
            </Button>
        </div>
    </div>

    <div class="px-6 py-6 space-y-6">
        <!-- Tabs -->
        <div class="flex items-center gap-1 p-1 rounded-xl bg-card/60 border border-border/50 w-fit shadow-sm">
            {#each tabs as tab}
                <button onclick={() => activeTab = tab.id} class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {activeTab === tab.id ? 'bg-pink-500/15 text-pink-500 shadow-sm' : 'text-muted-foreground hover:text-foreground hover:bg-muted/40'}">
                    <tab.icon class="size-4" />{tab.label}
                </button>
            {/each}
        </div>

        <!-- Summary Cards -->
        <div class="flex items-center gap-3 flex-wrap">
            {#each departemenOptions as dept}
                <div class="flex items-center gap-3 rounded-xl bg-gradient-to-r border px-5 py-2.5 shadow-sm w-fit" style="background: linear-gradient(135deg, {dept.color}18, {dept.color}08); border-color: {dept.color}33;">
                    <div class="size-9 rounded-lg flex items-center justify-center" style="background: {dept.color}33;"><Banknote class="size-4" style="color: {dept.color}" /></div>
                    <div class="text-right">
                        <p class="text-xs font-medium uppercase tracking-wider" style="color: {dept.color};">{dept.name} Budget</p>
                        <p class="text-base font-bold" style="color: {dept.color};">{new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(budgetPerDept.get(dept.id) ?? 0)}</p>
                    </div>
                </div>
            {/each}
            <div class="flex items-center gap-3 rounded-xl bg-gradient-to-r from-red-500/10 to-rose-600/5 border border-red-500/20 px-5 py-2.5 shadow-sm w-fit">
                <div class="size-9 rounded-lg bg-red-500/20 flex items-center justify-center"><Trash2 class="size-4 text-red-400" /></div>
                <div class="text-right"><p class="text-xs text-red-500/70 font-medium uppercase tracking-wider">Removed Device</p><p class="text-base font-bold text-red-600 dark:text-red-300">{removedDevices}</p></div>
            </div>
        </div>

        <!-- Tab Content -->
        {#if activeTab === 'brands'}
            <BrandsTab bind:search={brandSearch} table={brandsTable} onadd={() => { addBrandForm.reset(); showAddBrandModal = true; }} onedit={(b: Brand) => { editBrandForm.defaults({ name: b.name }).reset(); editBrandTarget = b; }} ondelete={(b: Brand) => { deleteBrandTarget = b; }} />
        {:else if activeTab === 'phones'}
            <PhonesTab allPhones={phoneLists} bind:search={phoneSearch} bind:departemenFilter {departemenOptions} table={phonesTable}
                onadd={openAddPhone} onedit={openEditPhone} ondelete={(p: PhoneList) => { deletePhoneTarget = p; }} onqr={(p: PhoneList) => { qrTarget = p; showQrModal = true; }} />
        {:else if activeTab === 'departemen'}
            <DepartemenTab {departemenOptions} search={deptSearch} phoneCountByDept={(id: string) => phoneLists.filter(p => p.departemen === id).length}
                onadd={() => { addDeptForm.reset(); showAddDeptModal = true; }}
                onedit={(d: DepartemenOption) => { editDeptForm.defaults({ name: d.name, color: d.color }).reset(); editDeptTarget = d; }}
                ondelete={(d: DepartemenOption) => { deleteDeptTarget = d; }} />
        {/if}
    </div>
</div>

<!-- Modals -->
<BrandFormModal mode="add" form={addBrandForm} show={showAddBrandModal} target={null} onclose={() => { showAddBrandModal = false; addBrandForm.reset(); }} onsave={handleAddBrand} />
<BrandFormModal mode="edit" form={editBrandForm} target={editBrandTarget} show={false} onclose={() => { editBrandTarget = null; editBrandForm.reset(); }} onsave={handleEditBrand} />
<ConfirmDeleteModal target={deleteBrandTarget} title="Hapus Brand" description={`Apakah Anda yakin ingin menghapus <span class="font-medium text-foreground">${deleteBrandTarget?.name ?? ''}</span>?`} form={deleteBrandForm} onclose={() => { deleteBrandTarget = null; }} ondelete={handleDeleteBrand} />
<AddPhoneModal show={showAddPhoneModal} form={addPhoneForm} {brands} {departemenOptions} bind:photos={addPhotos} bind:photosPreview={addPhotosPreview} bind:thumbnailIdx={addThumbnailIdx} onclose={() => { showAddPhoneModal = false; addPhoneForm.reset(); addPhotos = []; addPhotosPreview = []; addThumbnailIdx = 0; }} onsave={handleAddPhone} />
<EditPhoneModal target={editPhoneTarget} form={editPhoneForm} {brands} {departemenOptions} bind:photos={editPhotos} bind:photosPreview={editPhotosPreview} bind:thumbnailIdx={editThumbnailIdx} bind:existingThumbnailIdx={editExistingThumbnailIdx} bind:deletingPhotoUrl onclose={() => { editPhoneTarget = null; editPhoneForm.reset(); editPhotos = []; editPhotosPreview = []; editThumbnailIdx = 0; }} onsave={handleEditPhone} ondeletephoto={handleDeletePhoto} />
<ConfirmDeleteModal target={deletePhoneTarget} title="Hapus Phone" description={`Apakah Anda yakin ingin menghapus <span class="font-medium text-foreground">${deletePhoneTarget?.model_name ?? ''}</span> (<span class="font-mono text-xs">${deletePhoneTarget?.model_id ?? ''}</span>)?`} form={deletePhoneForm} onclose={() => { deletePhoneTarget = null; }} ondelete={handleDeletePhone} />
<QrModal target={qrTarget} show={showQrModal} onclose={() => { showQrModal = false; qrTarget = null; }} />
<DepartemenFormModal mode="add" form={addDeptForm} show={showAddDeptModal} target={null} onclose={() => { showAddDeptModal = false; addDeptForm.reset(); }} onsave={handleDeptAdd} />
<DepartemenFormModal mode="edit" form={editDeptForm} target={editDeptTarget} show={false} onclose={() => { editDeptTarget = null; editDeptForm.reset(); }} onsave={handleDeptEdit} />
<ConfirmDeleteModal target={deleteDeptTarget} title="Hapus Departemen" description={`Apakah Anda yakin ingin menghapus <span class="font-medium text-foreground">${deleteDeptTarget?.name ?? ''}</span>?`} form={deleteDeptForm} onclose={() => { deleteDeptTarget = null; }} ondelete={handleDeptDelete} />
