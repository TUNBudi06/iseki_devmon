<script lang="ts">
    import LayoutBG from "$/components/LayoutBG.svelte";
    import {Button} from "$shadcn/components/ui/button";
    import {CircleArrowLeft, X} from "@lucide/svelte";
    import { router } from "@inertiajs/svelte";
    import {dashboard} from "$routes/admin";
    import * as Card from "$shadcn/components/ui/card";
    import {AspectRatio} from "$shadcn/components/ui/aspect-ratio";
    import {onMount, onDestroy, tick} from "svelte";
    import QrScanner from "qr-scanner";
    import {useHttp} from "@inertiajs/svelte";
    import maintenance, { store as storeRoute } from "$routes/admin/maintenance";
    import { goBack } from '$lib/navigation';
    import {toast} from "svelte-sonner";
    import {page} from "@inertiajs/svelte"
    import * as Carousel from "$shadcn/components/ui/carousel"
    import Autoplay from "embla-carousel-autoplay";
    import * as Field from "$shadcn/components/ui/field"
    import { Label } from "$shadcn/components/ui/label";
    import { Textarea } from "$shadcn/components/ui/textarea";
    import { Separator } from "$shadcn/components/ui/separator";
    import * as FileDropZone from "$shadcn/components/ui/file-drop-zone";

    let isScannning = $state(true)
    let isLoading = $state(false)
    let deviceData = $state(false)
    let qrScanner: QrScanner | null = $state(null)
    let bindHtml = $state<HTMLVideoElement>()
    let scanError = $state('')
    let fotoPreview = $state<string[]>([])

    const formSearchDevice = useHttp({
        model_id: ''
    })

    const formCheckDevice = useHttp<{id:string, foto: File[], keterangan: string, imei_ok: boolean, mac_ok: boolean}>({
        id: '',
        keterangan:'',
        foto:[],
        imei_ok: false,
        mac_ok: false,
    });

    function resetFoto() {
        formCheckDevice.foto = [];
        fotoPreview = [];
    }

    function removeFoto(index: number) {
        formCheckDevice.foto = formCheckDevice.foto.filter((_, i) => i !== index);
        fotoPreview = fotoPreview.filter((_, i) => i !== index);
    }

    async function handleSubmit() {
        formCheckDevice.post(storeRoute().url, {
            forceFormData: true,
            onSuccess: () => {
                toast.success('Pengecekan berhasil dicatat');
                // Reset form for next scan
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
            qrScanner = new QrScanner(bindHtml!, result => {
                if (result?.data) {
                    qrScanner?.stop();
                    qrScanner?.destroy();
                    qrScanner = null;
                    verify(result.data);
                }
            }, {
                highlightCodeOutline: true,
                highlightScanRegion: true,
            });
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

    async function verify(data:string){
        formSearchDevice.model_id = data.split(';')[0]

        isLoading =true
        scanError = '';

        await formSearchDevice.post(maintenance.search().url,{
            onError:errors => {
                toast.error('Device Id Tidak ditemukan');
            },
            onSuccess: response => {
                deviceData = response;
                formCheckDevice.id = response.model_id;
                formCheckDevice.imei_ok = false;
                formCheckDevice.mac_ok = false;
                resetFoto();
                isScannning = false;
            }
        })
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
        <div class="mx-auto p-4 h-14 md:max-w-[98%] rounded items-center flex justify-between bg-linear-to-r from-pink-500 to-pink-800">
            <div class=" items-center text-center">
                <h2 class="font-bold text-white">Check Device</h2>
                <div class="text-muted text-[8px]">Check All device</div>
            </div>
            <Button variant="link" onclick={goBack} class="text-white hover:scale-110 transition border-white m-1">
                <CircleArrowLeft class="rotate-180" /> Back
            </Button>
        </div>

        <div class="md:max-w-[98%] mx-auto pt-2 pb-5">
            <Card.Root>
                {#if isScannning}
                    <Card.Header class="flex flex-row items-center justify-between">
                        <div>
                            <Card.Title>Scan QR Perangkat</Card.Title>
                            <Card.Description class="text-xs">Arahkan kamera ke QR code di dashboard perangkat</Card.Description>
                        </div>
                        {#if isLoading}
                            <span class="text-xs text-muted-foreground animate-pulse">Memproses...</span>
                        {/if}
                    </Card.Header>
                {/if}
                <Card.Content>
                    {#if isScannning}
                        <AspectRatio ratio={16/9} class="my-5 relative">
                            <video bind:this={bindHtml} class="w-full h-full rounded-lg object-cover"></video>
                            {#if scanError}
                                <div class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-lg">
                                    <div class="text-center p-4">
                                        <p class="text-red-400 text-sm mb-2">{scanError}</p>
                                        <Button variant="secondary" size="sm" onclick={startScanner}>
                                            Coba Lagi
                                        </Button>
                                    </div>
                                </div>
                            {/if}
                        </AspectRatio>
                    {:else if !deviceData}
                        <div class="flex flex-col items-center justify-center py-10 gap-4">
                            <p class="text-muted-foreground text-sm">Silakan scan QR code perangkat</p>
                            <Button onclick={startScanner} variant="outline" class="gap-2">
                                <CircleArrowLeft class="rotate-90 size-4" />
                                Buka Kamera
                            </Button>
                        </div>
                    {/if}

                    {#if deviceData}
                        <div class="w-full h-full flex flex-col md:flex-row gap-4">
                            <div class="w-full md:w-1/3">
                                <div class="md:sticky md:top-4">
                                    <Carousel.Root
                                        class="w-full"
                                        plugins={[
                                            Autoplay({ delay: 3500, stopOnInteraction: false }),
                                        ]}
                                    >
                                        {#if deviceData.list_photos.length > 0}
                                            <Carousel.Content>
                                                {#each deviceData.list_photos as photo, i (i)}
                                                    <Carousel.Item>
                                                        <img
                                                            src={page.props.baseUrl + '/' + photo}
                                                            alt="foto {i + 1}"
                                                            class="w-full aspect-square object-contain p-2"
                                                        />
                                                    </Carousel.Item>
                                                {/each}
                                            </Carousel.Content>
                                        {:else}
                                            <div class="w-full aspect-square flex items-center justify-center bg-muted/30 rounded-lg text-muted-foreground text-sm">
                                                Tidak ada foto
                                            </div>
                                        {/if}
                                    </Carousel.Root>
                                </div>
                            </div>
                            <div class="w-full md:w-2/3 space-y-5 md:pl-2">
                                <!-- Device Identity -->
                                <div class="flex items-start justify-between gap-2">
                                    <div class="space-y-1">
                                        <h2 class="font-bold text-xl text-pink-800">
                                            {deviceData.brand_id} {deviceData.model_name}
                                        </h2>
                                        <p class="text-sm text-muted-foreground">
                                            {parseInt(deviceData.ram).toString()}/{deviceData.storage} &middot; {deviceData.model_id}
                                        </p>
                                    </div>
                                    <Button variant="ghost" size="sm" onclick={startScanner} class="shrink-0 gap-1.5 text-xs" title="Scan ulang perangkat lain">
                                        <CircleArrowLeft class="rotate-90 size-3.5" />
                                        Scan Ulang
                                    </Button>
                                </div>

                                <Separator class="my-2" />

                                <!-- IMEI Check -->
                                {#if deviceData.imei}
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            bind:checked={formCheckDevice.imei_ok}
                                            class="size-4 accent-pink-600 rounded border-muted-foreground/30 cursor-pointer"
                                        />
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium group-hover:text-pink-700 transition-colors">
                                                IMEI Sesuai
                                            </span>
                                            <span class="text-xs text-muted-foreground font-mono">
                                                {deviceData.imei}
                                            </span>
                                        </div>
                                    </label>
                                {/if}

                                <!-- MAC Check -->
                                {#if deviceData.mac_address}
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            bind:checked={formCheckDevice.mac_ok}
                                            class="size-4 accent-pink-600 rounded border-muted-foreground/30 cursor-pointer"
                                        />
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium group-hover:text-pink-700 transition-colors">
                                                MAC Address Sesuai
                                            </span>
                                            <span class="text-xs text-muted-foreground font-mono">
                                                {deviceData.mac_address}
                                            </span>
                                        </div>
                                    </label>
                                {/if}

                                <!-- Petunjuk Cek IMEI/MAC -->
                                <div class="rounded-lg border border-muted bg-muted/20 p-3 space-y-1.5">
                                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Cara Cek IMEI / MAC</p>
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

                                <!-- Keterangan -->
                                <div class="space-y-1.5">
                                    <Label for="check-keterangan" class="text-sm font-medium">
                                        Keterangan <span class="text-xs text-muted-foreground">(opsional)</span>
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
                                    <Label class="text-sm font-medium">Foto Kondisi Fisik</Label>
                                    <p class="text-xs text-muted-foreground">Upload foto kondisi fisik perangkat saat ini</p>
                                    <FileDropZone.Root
                                        accept="image/*"
                                        maxFileSize={FileDropZone.MEGABYTE * 5}
                                        fileCount={formCheckDevice.foto.length}
                                        onUpload={async (files) => {
                                            for (const file of files) {
                                                formCheckDevice.foto = [...formCheckDevice.foto, file];
                                                fotoPreview = [...fotoPreview, URL.createObjectURL(file)];
                                            }
                                        }}
                                    >
                                        <FileDropZone.Trigger />
                                    </FileDropZone.Root>
                                    {#if fotoPreview.length > 0}
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-3 lg:grid-cols-4 gap-2 mt-2">
                                            {#each fotoPreview as preview, i}
                                                <div class="relative group aspect-square">
                                                    <img
                                                        src={preview}
                                                        alt="Kondisi fisik {i + 1}"
                                                        class="size-full rounded-lg object-cover ring-1 ring-pink-300/30"
                                                    />
                                                    <button
                                                        type="button"
                                                        onclick={(e) => { e.stopPropagation(); removeFoto(i); }}
                                                        class="absolute -top-1.5 -right-1.5 size-5 rounded-full bg-red-500/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
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
                                            <span class="animate-spin size-4 border-2 border-white border-t-transparent rounded-full" />
                                        {/if}
                                        {formCheckDevice.processing ? 'Menyimpan...' : 'Simpan Maintenance'}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    {/if}
                </Card.Content>
            </Card.Root>
        </div>
    </div>
</LayoutBG>
