<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import { Button } from '$shadcn/components/ui/button';
    import { CircleArrowLeft, X, ChevronDown } from '@lucide/svelte';
    import { router } from '@inertiajs/svelte';
    import { dashboard } from '$routes/admin';
    import * as Card from '$shadcn/components/ui/card';
    import { AspectRatio } from '$shadcn/components/ui/aspect-ratio';
    import { onMount, onDestroy, tick } from 'svelte';
    import QrScanner from 'qr-scanner';
    import { useHttp, useForm } from '@inertiajs/svelte';
    import maintenance, {
        store as storeRoute,
    } from '$routes/admin/maintenance';
    import { goBack } from '$lib/navigation';
    import { toast } from 'svelte-sonner';
    import { page } from '@inertiajs/svelte';
    import * as Carousel from '$shadcn/components/ui/carousel';
    import Autoplay from 'embla-carousel-autoplay';
    import * as Field from '$shadcn/components/ui/field';
    import { Label } from '$shadcn/components/ui/label';
    import { Textarea } from '$shadcn/components/ui/textarea';
    import { Separator } from '$shadcn/components/ui/separator';
    import * as FileDropZone from '$shadcn/components/ui/file-drop-zone';
    import { detailPhone } from '$routes/phone';

    let isScannning = $state(true);
    let isLoading = $state(false);
    let deviceData = $state(false);
    let qrScanner: QrScanner | null = $state(null);
    let bindHtml = $state<HTMLVideoElement>();
    let scanError = $state('');
    let fotoPreview = $state<string[]>([]);
    let expandedGuide = $state(false);

    let { recentChecks = [] } = $props<{
        recentChecks?: {
            id: number;
            phone_list_id: number;
            checked_by_name: string;
            checked_by_username: string;
            imei_ok: boolean;
            mac_ok: boolean;
            keterangan: string | null;
            created_at: string;
            phone_list: {
                id: number;
                brand_id: string;
                model_id: string;
                model_name: string;
            } | null;
        }[];
    }>();

    const formSearchDevice = useHttp({
        model_id: '',
    });

    let checkPhotos = $state<File[]>([]);

    const formCheckDevice = useForm({
        id: '',
        keterangan: '',
        foto: [] as unknown,
        imei_ok: false,
        mac_ok: false,
    });

    function resetFoto() {
        checkPhotos = [];
        fotoPreview = [];
    }

    function removeFoto(index: number) {
        checkPhotos = checkPhotos.filter((_, i) => i !== index);
        fotoPreview = fotoPreview.filter((_, i) => i !== index);
    }

    async function handleSubmit() {
        formCheckDevice.foto = checkPhotos as unknown;
        formCheckDevice.post(storeRoute().url, {
            forceFormData: true,
            onSuccess: () => {
                toast.success('Pengecekan berhasil dicatat');
                deviceData = false;
                formCheckDevice.keterangan = '';
                resetFoto();
                formCheckDevice.imei_ok = false;
                formCheckDevice.mac_ok = false;
            },
            onError: (errors: Record<string, string>) => {
                const msg = Object.values(errors)[0] ?? 'Gagal menyimpan';
                toast.error(msg);
            },
        });
    }

    async function startScanner() {
        scanError = '';
        isScannning = true;
        deviceData = false;
        formCheckDevice.keterangan = '';
        resetFoto();
        await tick();
        await tick();
        try {
            qrScanner = new QrScanner(
                bindHtml!,
                (result) => {
                    if (result?.data) {
                        qrScanner?.stop();
                        qrScanner?.destroy();
                        qrScanner = null;
                        verify(result.data);
                    }
                },
                {
                    highlightCodeOutline: true,
                    highlightScanRegion: true,
                },
            );
            await qrScanner.start();
        } catch (e: any) {
            scanError = e?.message || 'Gagal mengakses kamera';
            isScannning = false;
        }
    }

    function stopScanner() {
        if (qrScanner) {
            qrScanner.stop();
            qrScanner.destroy();
            qrScanner = null;
        }
        isScannning = false;
    }

    async function verify(data: string) {
        formSearchDevice.model_id = data.split(';')[0];

        isLoading = true;
        scanError = '';

        await formSearchDevice.post(maintenance.search().url, {
            onError: (errors) => {
                toast.error('Device Id Tidak ditemukan');
            },
            onSuccess: (response) => {
                deviceData = response;
                formCheckDevice.id = response.model_id;
                formCheckDevice.imei_ok = false;
                formCheckDevice.mac_ok = false;
                resetFoto();
                isScannning = false;
            },
        });
    }

    onMount(async () => {
        await startScanner();
    });

    onDestroy(() => {
        if (qrScanner) {
            qrScanner.stop();
            qrScanner.destroy();
        }
    });
</script>

<LayoutBG class="bg-linear-to-br md:p-4 from-pink-50 via-pink-100 to-pink-50">
    <div class="w-full mx-auto relative z-10">
        <div
            class="mx-auto p-3 md:p-4 md:max-w-[98%] rounded-xl bg-linear-to-r from-pink-500 to-pink-800 shadow-md"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                        <svg class="size-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 7v2a3 3 0 003 3h2a3 3 0 003-3V7a3 3 0 00-3-3H6a3 3 0 00-3 3z" /><path d="M13 7v2a3 3 0 003 3h2a3 3 0 003-3V7a3 3 0 00-3-3h-2a3 3 0 00-3 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-white text-sm md:text-base">Check Device</h2>
                        <p class="text-pink-200 text-[10px] md:text-xs">Scan QR untuk maintenance</p>
                    </div>
                </div>
                <Button variant="ghost" size="sm" onclick={goBack} class="text-pink-200 hover:text-white hover:bg-white/10">
                    <CircleArrowLeft class="size-4" />
                </Button>
            </div>
        </div>

        <div class="mx-auto pt-2 pb-5">
            {#if isScannning}
                <!-- Scanner full-width on mobile, card on desktop -->
                <div class="md:max-w-[98%] mx-auto mb-3 px-1 md:px-0">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="font-semibold text-sm text-pink-800">Scan QR Perangkat</p>
                            <p class="text-[11px] text-muted-foreground">Arahkan kamera ke QR code perangkat</p>
                        </div>
                        {#if isLoading}
                            <span class="text-xs text-muted-foreground animate-pulse">Memproses...</span>
                        {/if}
                    </div>
                    <div class="relative rounded-xl overflow-hidden border-2 border-pink-500/40 shadow-lg shadow-pink-500/10">
                        <AspectRatio ratio={3 / 4} class="sm:aspect-[16/9] bg-black">
                            <video bind:this={bindHtml} class="w-full h-full object-cover"></video>
                        </AspectRatio>
                        {#if scanError}
                            <div class="absolute inset-0 flex items-center justify-center bg-black/70">
                                <div class="text-center p-6">
                                    <X class="size-10 text-red-400 mx-auto mb-3" />
                                    <p class="text-red-400 text-sm mb-3">{scanError}</p>
                                    <Button variant="secondary" size="sm" onclick={startScanner}>Coba Lagi</Button>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>

                <!-- Quick tip -->
                <p class="text-center text-[10px] text-muted-foreground px-4">
                    Pastikan QR code terlihat jelas dan tidak terhalang
                </p>
            {/if}

            {#if !isScannning && !deviceData}
                <div class="flex flex-col items-center justify-center py-16 gap-4">
                    <div class="size-16 rounded-2xl bg-pink-500/10 flex items-center justify-center">
                        <svg class="size-8 text-pink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 7v2a3 3 0 003 3h2a3 3 0 003-3V7a3 3 0 00-3-3H6a3 3 0 00-3 3z" /><path d="M13 7v2a3 3 0 003 3h2a3 3 0 003-3V7a3 3 0 00-3-3h-2a3 3 0 00-3 3z" />
                        </svg>
                    </div>
                    <p class="text-muted-foreground text-sm font-medium">Silakan scan QR code perangkat</p>
                    <Button onclick={startScanner} variant="outline" size="sm" class="gap-2">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>
                        Buka Kamera
                    </Button>
                </div>
            {/if}

            {#if deviceData}
                <Card.Root class="md:max-w-[98%] mx-auto">
                <Card.Content>
                    <div
                            class="w-full h-full flex flex-col md:flex-row gap-4"
                        >
                            <div class="w-full md:w-1/3">
                                <div class="md:sticky md:top-4">
                                    <Carousel.Root
                                        class="w-full"
                                        plugins={[
                                            Autoplay({
                                                delay: 3500,
                                                stopOnInteraction: false,
                                            }),
                                        ]}
                                    >
                                        {#if deviceData.list_photos.length > 0}
                                            <Carousel.Content>
                                                {#each deviceData.list_photos as photo, i (i)}
                                                    <Carousel.Item>
                                                        <img
                                                            src={page.props
                                                                .baseUrl +
                                                                '/' +
                                                                photo}
                                                            alt="foto {i + 1}"
                                                            class="w-full aspect-square object-contain p-2"
                                                        />
                                                    </Carousel.Item>
                                                {/each}
                                            </Carousel.Content>
                                        {:else}
                                            <div
                                                class="w-full aspect-square flex items-center justify-center bg-muted/30 rounded-lg text-muted-foreground text-sm"
                                            >
                                                Tidak ada foto
                                            </div>
                                        {/if}
                                    </Carousel.Root>
                                </div>
                            </div>
                            <div class="w-full md:w-2/3 space-y-5 md:pl-2">
                                <!-- Device Identity -->
                                <div
                                    class="flex items-start justify-between gap-2"
                                >
                                    <div class="space-y-1">
                                        <h2
                                            class="font-bold text-xl text-pink-800"
                                        >
                                            {deviceData.brand_id}
                                            {deviceData.model_name}
                                        </h2>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            {parseInt(
                                                deviceData.ram,
                                            ).toString()}/{deviceData.storage} &middot;
                                            {deviceData.model_id}
                                        </p>
                                    </div>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onclick={startScanner}
                                        class="shrink-0 gap-1.5 text-xs"
                                        title="Scan ulang perangkat lain"
                                    >
                                        <CircleArrowLeft
                                            class="rotate-90 size-3.5"
                                        />
                                        Scan Ulang
                                    </Button>
                                </div>

                                <Separator class="my-2" />

                                <!-- IMEI Check -->
                                {#if deviceData.imei}
                                    <label
                                        class="flex items-center gap-3 cursor-pointer group"
                                    >
                                        <input
                                            type="checkbox"
                                            bind:checked={
                                                formCheckDevice.imei_ok
                                            }
                                            class="size-4 accent-pink-600 rounded border-muted-foreground/30 cursor-pointer"
                                        />
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-medium group-hover:text-pink-700 transition-colors"
                                            >
                                                IMEI Sesuai
                                            </span>
                                            <span
                                                class="text-xs text-muted-foreground font-mono"
                                            >
                                                {deviceData.imei}
                                            </span>
                                        </div>
                                    </label>
                                {/if}

                                <!-- MAC Check -->
                                {#if deviceData.mac_address}
                                    <label
                                        class="flex items-center gap-3 cursor-pointer group"
                                    >
                                        <input
                                            type="checkbox"
                                            bind:checked={
                                                formCheckDevice.mac_ok
                                            }
                                            class="size-4 accent-pink-600 rounded border-muted-foreground/30 cursor-pointer"
                                        />
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-medium group-hover:text-pink-700 transition-colors"
                                            >
                                                MAC Address Sesuai
                                            </span>
                                            <span
                                                class="text-xs text-muted-foreground font-mono"
                                            >
                                                {deviceData.mac_address}
                                            </span>
                                        </div>
                                    </label>
                                {/if}

                                <!-- Petunjuk Cek IMEI/MAC (collapsible on mobile) -->
                                <div class="rounded-lg border border-muted bg-muted/20 overflow-hidden">
                                    <button
                                        onclick={() => expandedGuide = !expandedGuide}
                                        class="w-full flex items-center justify-between p-3 text-left"
                                    >
                                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                            Cara Cek IMEI / MAC
                                        </span>
                                        <ChevronDown class="size-4 text-muted-foreground transition-transform duration-200 {expandedGuide ? 'rotate-180' : ''}" />
                                    </button>
                                    {#if expandedGuide}
                                        <div class="px-3 pb-3 space-y-1.5">
                                            <ol class="text-xs text-muted-foreground space-y-1 list-decimal list-inside">
                                                <li>Buka <span class="font-medium text-foreground/80">Pengaturan</span> (Settings) di perangkat</li>
                                                <li>Masuk ke <span class="font-medium text-foreground/80">Tentang Ponsel</span> (About Phone)</li>
                                                <li>Pilih <span class="font-medium text-foreground/80">Status</span> atau <span class="font-medium text-foreground/80">Informasi Perangkat</span></li>
                                                <li>Cocokkan IMEI / MAC Address yang tertera</li>
                                            </ol>
                                            <p class="text-xs text-muted-foreground/70 italic">
                                                Alternatif: ketik <span class="font-mono not-italic">*#06#</span> di dial pad untuk lihat IMEI
                                            </p>
                                        </div>
                                    {/if}
                                </div>

                                <!-- Keterangan -->
                                <div class="space-y-1.5">
                                    <Label
                                        for="check-keterangan"
                                        class="text-sm font-medium"
                                    >
                                        Keterangan <span
                                            class="text-xs text-muted-foreground"
                                            >(opsional)</span
                                        >
                                    </Label>
                                    <Textarea
                                        id="check-keterangan"
                                        bind:value={formCheckDevice.keterangan}
                                        placeholder="Deskripsi kondisi perangkat, catatan maintenance, dll."
                                        rows={4}
                                        class="resize-none text-sm"
                                    />
                                </div>

                                <!-- Dropzone Foto Kondisi Fisik -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium"
                                        >Foto Kondisi Fisik</Label
                                    >
                                    <p class="text-xs text-muted-foreground">
                                        Upload foto kondisi fisik perangkat saat
                                        ini
                                    </p>
                                    <FileDropZone.Root
                                        accept="image/*"
                                        capture="user"
                                        maxFileSize={FileDropZone.MEGABYTE * 5}
                                        fileCount={checkPhotos.length}
                                        onUpload={async (files) => {
                                            for (const file of files) {
                                                checkPhotos = [...checkPhotos, file];
                                                fotoPreview = [...fotoPreview, URL.createObjectURL(file)];
                                            }
                                        }}
                                    >
                                        <FileDropZone.Trigger />
                                    </FileDropZone.Root>
                                    {#if fotoPreview.length > 0}
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            {#each fotoPreview as preview, i}
                                                <div class="relative group">
                                                    <img
                                                        src={preview}
                                                        alt="Foto {i + 1}"
                                                        class="size-16 rounded-lg object-cover ring-1 ring-pink-300/30 hover:ring-pink-400/60 transition-all duration-200"
                                                    />
                                                    <button
                                                        type="button"
                                                        onclick={(e) => { e.stopPropagation(); removeFoto(i); }}
                                                        class="absolute -top-2 -right-2 size-6 sm:size-5 rounded-full bg-red-500/80 flex items-center justify-center sm:opacity-0 sm:group-hover:opacity-100 transition-opacity"
                                                    >
                                                        <X class="size-3 text-white" />
                                                    </button>
                                                </div>
                                            {/each}
                                        </div>
                                    {/if}
                                </div>

                                <!-- Submit -->
                                <div class="pt-3">
                                    <Button
                                        type="button"
                                        onclick={handleSubmit}
                                        disabled={formCheckDevice.processing}
                                        class="w-full gap-2 bg-pink-600 hover:bg-pink-700"
                                    >
                                        {#if formCheckDevice.processing}
                                            <span
                                                class="animate-spin size-4 border-2 border-white border-t-transparent rounded-full"
                                            />
                                        {/if}
                                        {formCheckDevice.processing
                                            ? 'Menyimpan...'
                                            : 'Simpan Maintenance'}
                                    </Button>
                                </div>
                            </div>
                        </div>
                </Card.Content>
            </Card.Root>
            {/if}
        </div>

        {#if recentChecks.length > 0}
            <!-- ══ RECENT MAINTENANCE THIS MONTH ══ -->
            <div class="md:max-w-[98%] mx-auto mt-6">
                <Card.Root>
                    <Card.Header class="pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="size-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                                <svg class="size-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <Card.Title class="text-base">Maintenance Bulan Ini</Card.Title>
                                <Card.Description class="text-xs">
                                    {recentChecks.length} perangkat sudah dicek bulan {new Date().toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })}
                                </Card.Description>
                            </div>
                        </div>
                    </Card.Header>
                    <Card.Content class="pt-0">
                        <div class="flex flex-col">
                            {#each recentChecks as check (check.id)}
                                {@const phone = check.phone_list}
                                <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
                                <div
                                    class="flex items-center gap-3 py-2.5 border-b border-border/30 last:border-b-0 cursor-pointer hover:bg-muted/30 rounded-lg px-2 -mx-2 transition-colors group"
                                    onclick={() => phone && router.visit(detailPhone({ id: phone.model_id }).url)}
                                >
                                    <!-- Status dot -->
                                    <div class="size-2 rounded-full shrink-0 {check.imei_ok && check.mac_ok ? 'bg-emerald-500' : 'bg-amber-500'}" title={check.imei_ok && check.mac_ok ? 'Semua OK' : 'Perlu perhatian'}></div>

                                    <!-- Device info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-sm truncate">
                                            {phone?.model_name ?? '#' + check.phone_list_id}
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                            {#if phone}
                                                <span class="font-mono">{phone.model_id}</span>
                                                <span>&middot;</span>
                                                <span>{phone.brand_id}</span>
                                            {/if}
                                            <span>&middot;</span>
                                            <span>{check.checked_by_name}</span>
                                        </div>
                                    </div>

                                    <!-- Badges -->
                                    <div class="flex items-center gap-1 shrink-0">
                                        {#if check.imei_ok}
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 font-mono">IMEI</span>
                                        {/if}
                                        {#if check.mac_ok}
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 font-mono">MAC</span>
                                        {/if}
                                    </div>

                                    <!-- Date -->
                                    <span class="text-xs text-muted-foreground shrink-0 w-20 text-right">
                                        {new Date(check.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                                    </span>

                                    <!-- Arrow -->
                                    <svg class="size-3.5 text-muted-foreground/30 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 18l6-6-6-6" />
                                    </svg>
                                </div>
                            {/each}
                        </div>
                    </Card.Content>
                </Card.Root>
            </div>
        {/if}
    </div>
</LayoutBG>
