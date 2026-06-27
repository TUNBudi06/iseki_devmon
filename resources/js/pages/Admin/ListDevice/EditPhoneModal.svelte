<script lang="ts">
    import storage from '$routes/storage';
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Input } from '$shadcn/components/ui/input';
    import { Label } from '$shadcn/components/ui/label';
    import { Separator } from '$shadcn/components/ui/separator';
    import * as FileDropZone from '$shadcn/components/ui/file-drop-zone';
    import { Edit, Check, X } from '@lucide/svelte';
    import type { Brand, PhoneList, DepartemenOption } from './types';

    let {
        target,
        form,
        brands,
        departemenOptions,
        photos = $bindable([] as File[]),
        photosPreview = $bindable([] as string[]),
        thumbnailIdx = $bindable(0),
        existingThumbnailIdx = $bindable(0),
        deletingPhotoUrl = $bindable(null as string | null),
        onclose,
        onsave,
        ondeletephoto,
    }: {
        target: PhoneList | null;
        form: {
            brand_id: string; model_id: string; model_name: string; model_type: string;
            buy_date: string; price: string; ram: string; storage: string;
            departemen: string; thumbnail: string; imei: string; mac_address: string; list_photos: unknown;
            errors: Record<string, string>; processing: boolean;
            defaults: (data: Record<string, unknown>) => { reset: () => void };
            reset: () => void;
            transform: (fn: (data: Record<string, unknown>) => Record<string, unknown>) => {
                post: (url: string, opts?: Record<string, unknown>) => void;
            };
            setStore: (key: string, value: unknown) => void;
        };
        brands: Brand[];
        departemenOptions: DepartemenOption[];
        photos?: File[];
        photosPreview?: string[];
        thumbnailIdx?: number;
        existingThumbnailIdx?: number;
        deletingPhotoUrl?: string | null;
        onclose: () => void;
        onsave: () => void;
        ondeletephoto: (url: string) => void;
    } = $props();

    function storageUrl(path: string | null): string | null {
        if (!path) return null;
        const clean = path.replace(/^storage\//, '');
        return storage.local({ path: clean }).url;
    }
</script>

{#if target}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-lg border-border/60 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header>
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center"><Edit class="size-5 text-emerald-400" /></div>
                    <div>
                        <Card.Title class="text-lg">Edit Phone</Card.Title>
                        <Card.Description><span class="font-mono text-xs">{target.model_id}</span></Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-5 space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                <div class="space-y-2">
                    <Label for="edit-phone-brand" class="text-sm font-medium">Brand</Label>
                    <select id="edit-phone-brand" bind:value={form.brand_id} class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        {#each brands as brand}<option value={brand.id}>{brand.name}</option>{/each}
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-phone-model-id" class="text-sm font-medium">Model ID</Label>
                        <Input id="edit-phone-model-id" bind:value={form.model_id} class="h-10" />
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-model-name" class="text-sm font-medium">Model Name</Label>
                        <Input id="edit-phone-model-name" bind:value={form.model_name} class="h-10" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-phone-type" class="text-sm font-medium">Type</Label>
                        <select id="edit-phone-type" bind:value={form.model_type} class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            <option value="Phone">Phone</option><option value="Tablet">Tablet</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-buy-date" class="text-sm font-medium">Buy Date</Label>
                        <Input id="edit-phone-buy-date" bind:value={form.buy_date} class="h-10" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-2"><Label for="edit-phone-price" class="text-sm font-medium">Price</Label><Input id="edit-phone-price" bind:value={form.price} class="h-10" /></div>
                    <div class="space-y-2"><Label for="edit-phone-ram" class="text-sm font-medium">RAM</Label><Input id="edit-phone-ram" bind:value={form.ram} class="h-10" /></div>
                    <div class="space-y-2"><Label for="edit-phone-storage" class="text-sm font-medium">Storage</Label><Input id="edit-phone-storage" bind:value={form.storage} class="h-10" /></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="edit-phone-imei" class="text-sm font-medium">IMEI <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                        <Input id="edit-phone-imei" bind:value={form.imei} placeholder="15 digit IMEI" maxlength={15} class="h-10 font-mono text-sm" />
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-phone-mac" class="text-sm font-medium">MAC Address <span class="text-xs text-muted-foreground">(opsional)</span></Label>
                        <Input id="edit-phone-mac" bind:value={form.mac_address} placeholder="00:1A:2B:3C:4D:5E" maxlength={17} class="h-10 font-mono text-sm" />
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="edit-phone-departemen" class="text-sm font-medium">Departemen</Label>
                    <select id="edit-phone-departemen" bind:value={form.departemen} class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        {#each departemenOptions as dept}<option value={dept.id}>{dept.name}</option>{/each}
                    </select>
                </div>
                <div class="space-y-2">
                    <Label class="text-sm font-medium">Foto Perangkat</Label>
                    <p class="text-xs text-muted-foreground">Upload foto tambahan (foto pertama jadi thumbnail)</p>
                    {#if target.list_photos && target.list_photos.length > 0 && photosPreview.length === 0}
                        <div class="flex flex-wrap gap-2 mb-2">
                            {#each target.list_photos as photo, i}
                                <div class="relative group">
                                    <div onclick={() => existingThumbnailIdx = i} class="cursor-pointer">
                                        <img src={storageUrl(photo)} alt="Gallery {i + 1}" class="size-16 rounded-lg object-cover transition-all duration-200 {i === existingThumbnailIdx ? 'ring-2 ring-emerald-500 ring-offset-2 ring-offset-background scale-110' : 'ring-1 ring-emerald-300/30 hover:ring-emerald-400/60'}" />
                                        {#if i === existingThumbnailIdx}<span class="absolute -top-2 -left-2 size-5 rounded-full bg-emerald-500 text-[9px] font-bold text-white flex items-center justify-center shadow-lg">T</span>{/if}
                                    </div>
                                    <button onclick={() => ondeletephoto(photo)} disabled={deletingPhotoUrl === photo} class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-500 shadow-sm" title="Hapus foto">
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
                    <FileDropZone.Root accept="image/*" capture="environment" maxFileSize={FileDropZone.MEGABYTE * 5} fileCount={photos.length}
                        onUpload={async (files: File[]) => { for (const file of files) { photos = [...photos, file]; photosPreview = [...photosPreview, URL.createObjectURL(file)]; } }}>
                        <FileDropZone.Trigger />
                    </FileDropZone.Root>
                    {#if photosPreview.length > 0}
                        <div class="flex flex-wrap gap-2 mt-2">
                            {#each photosPreview as preview, i}
                                <div class="relative group" onclick={() => thumbnailIdx = i}>
                                    <img src={preview} alt="Photo {i + 1}" class="size-16 rounded-lg object-cover transition-all duration-200 {i === thumbnailIdx ? 'ring-2 ring-emerald-500 ring-offset-2 ring-offset-background scale-110' : 'ring-1 ring-emerald-300/30 hover:ring-emerald-400/60'}" />
                                    {#if i === thumbnailIdx}<span class="absolute -top-2 -left-2 size-5 rounded-full bg-emerald-500 text-[9px] font-bold text-white flex items-center justify-center shadow-lg">T</span>{/if}
                                    <button type="button" onclick={(e: MouseEvent) => { e.stopPropagation(); photos = photos.filter((_, idx) => idx !== i); photosPreview = photosPreview.filter((_, idx) => idx !== i); if (thumbnailIdx >= photos.length) thumbnailIdx = Math.max(0, photos.length - 1); }} class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
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
                <Button variant="outline" onclick={onclose} disabled={form.processing}>Batal</Button>
                <Button onclick={onsave} disabled={form.processing} class="gap-2">
                    {#if form.processing}
                        <div class="size-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menyimpan...
                    {:else}
                        <Check class="size-4" /> Simpan
                    {/if}
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}
