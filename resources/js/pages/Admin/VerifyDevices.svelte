<script lang="ts">
    import SidebarProvider from '$/components/SidebarProvider.svelte';
    import AppHead from '$/components/AppHead.svelte';
    import { resource } from 'runed';
    import { tick } from 'svelte';
    import {
        XIcon,
        ScanQrCode,
        ShieldCheck,
        ShieldX,
        RefreshCw,
        CheckCircle2,
        Camera,
    } from '@lucide/svelte';
    import { Label } from '$shadcn/components/ui/label';
    import * as Field from '$shadcn/components/ui/field';
    import { useForm } from '@inertiajs/svelte';
    import { Textarea } from '$shadcn/components/ui/textarea';
    import { Button } from '$shadcn/components/ui/button';
    import { Input } from '$shadcn/components/ui/input';
    import { SvelteDate } from 'svelte/reactivity';
    import { onDestroy } from 'svelte';
    import { deviceInfoId } from '$routes/api';
    import * as FileDropZone from '$shadcn/components/ui/file-drop-zone';
    import * as Separator from '$shadcn/components/ui/separator';
    import * as Alert from '$shadcn/components/ui/alert';
    import * as Card from '$shadcn/components/ui/card';
    import * as Badge from '$shadcn/components/ui/badge';
    import { Spinner } from '$shadcn/components/ui/spinner';
    import QrScanner from 'qr-scanner';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { verifyDevicePost } from '$routes/admin';
    import { toast } from 'svelte-sonner';

    const form = useForm<{ comment: string; image: File[]; id: number | null }>(
        {
            id: null,
            comment: '',
            image: [],
        },
    );

    let qrValue = $state('');
    let video = $state<HTMLVideoElement>();
    let scanner = $state<QrScanner>();
    let openScanner = $state(false);

    const valueResult = resource(
        () => qrValue,
        async (id, _prev, { signal }) => {
            if (!id) return null;
            const response = await fetch(routeUrl(deviceInfoId()), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ deviceId: id }),
                signal,
            });
            return await response.json();
        },
        { debounce: 500 },
    );

    let deviceStatus = $derived(
        valueResult.current
            ? valueResult.current.error
                ? 'error'
                : valueResult.current.approved
                  ? 'approved'
                  : 'pending'
            : 'idle',
    );

    async function startScanner() {
        openScanner = true;
        await tick();
        scanner = new QrScanner(
            video!,
            async (result) => {
                qrValue = JSON.parse(result.data).deviceId;
                await stopScanner();
            },
            {
                preferredCamera: 'environment',
                highlightScanRegion: true,
                highlightCodeOutline: true,
            },
        );
        await scanner.start();
    }

    async function stopScanner() {
        if (scanner) {
            await scanner.stop();
            scanner.destroy();
        }
        openScanner = false;
    }

    $effect(() => () => stopScanner());

    const onUpload: FileDropZone.FileDropZoneRootProps['onUpload'] = async (
        fs,
    ) => {
        await Promise.allSettled(fs.map(uploadFile));
    };

    const onFileRejected: FileDropZone.FileDropZoneRootProps['onFileRejected'] =
        async ({ reason, file }) => {
            toast.error(`${file.name} gagal diupload`, { description: reason });
        };

    type UploadedFile = {
        name: string;
        type: string;
        size: number;
        file: File;
        uploadedAt: number;
        url: string;
    };

    let files = $state<UploadedFile[]>([]);

    const uploadFile = async (file: File) => {
        if (files.find((f) => f.name === file.name)) return;
        files.push({
            name: file.name,
            type: file.type,
            size: file.size,
            uploadedAt: Date.now(),
            file,
            url: URL.createObjectURL(file),
        });
    };

    $effect(() => {
        form.image = files.map((f) => f.file);
        form.id = valueResult.current?.id ?? null;
    });

    onDestroy(() => {
        for (const file of files) URL.revokeObjectURL(file.url);
    });

    async function handleCancel() {
        form.reset();
        qrValue = '';
        for (const file of files) URL.revokeObjectURL(file.url);
        files = [];
    }

    function handleSubmit(e: Event) {
        e.preventDefault();
        if (form.image.length === 0) {
            toast.error('Upload minimal 1 foto device');
            return;
        }
        form.post(routeUrl(verifyDevicePost()), {
            onSuccess: () => {
                toast.success('Device berhasil diaktivasi!');
                handleCancel();
            },
        });
    }
</script>

<AppHead title="Aktivasi Device" />
<SidebarProvider>
    <div class="container max-w-7xl mx-auto py-8 px-4 space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Aktivasi Device
            </h1>
            <p class="text-muted-foreground text-sm mt-1">
                Verifikasi dan aktifkan perangkat lapangan menggunakan QR token
            </p>
        </div>

        <Separator.Root />

        <!-- QR Input Card -->
        <Card.Root>
            <Card.Header>
                <Card.Title class="text-base"
                    >Scan atau Input QR Token</Card.Title
                >
                <Card.Description>
                    Arahkan kamera ke QR code device, atau ketik token secara
                    manual
                </Card.Description>
            </Card.Header>
            <Card.Content>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <ScanQrCode
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            bind:value={qrValue}
                            placeholder="QR Token..."
                            class="pl-9"
                        />
                    </div>
                    <Button
                        variant="outline"
                        onclick={startScanner}
                        class="gap-2 shrink-0"
                    >
                        <Camera class="size-4" />
                        Scan
                    </Button>
                    <Button
                        variant="outline"
                        onclick={valueResult.refetch}
                        class="gap-2 shrink-0"
                    >
                        <RefreshCw
                            class="size-4 {valueResult.loading
                                ? 'animate-spin'
                                : ''}"
                        />
                        Cek
                    </Button>
                </div>
            </Card.Content>
        </Card.Root>

        <!-- Status Result -->
        {#if valueResult.loading}
            <Card.Root>
                <Card.Content class="py-8">
                    <div
                        class="flex items-center justify-center gap-3 text-muted-foreground"
                    >
                        <Spinner class="size-5" />
                        <span class="text-sm">Mengecek device...</span>
                    </div>
                </Card.Content>
            </Card.Root>
        {:else if deviceStatus === 'approved'}
            <Alert.Root
                class="border-green-200 bg-green-50 dark:border-green-900 dark:bg-green-950/30"
            >
                <ShieldCheck
                    class="size-4 text-green-600 dark:text-green-400"
                />
                <Alert.Title class="text-green-800 dark:text-green-300"
                    >Device Sudah Aktif</Alert.Title
                >
                <Alert.Description>
                    <div
                        class="mt-2 space-y-1 text-green-700 dark:text-green-400"
                    >
                        <p class="text-sm">
                            <span class="font-medium">ID:</span>
                            {valueResult.current.deviceId}
                        </p>
                        <p class="text-sm">
                            <span class="font-medium">Nama:</span>
                            {valueResult.current.deviceName}
                        </p>
                    </div>
                </Alert.Description>
            </Alert.Root>
        {:else if deviceStatus === 'error'}
            <Alert.Root variant="destructive">
                <ShieldX class="size-4" />
                <Alert.Title>Device Tidak Ditemukan</Alert.Title>
                <Alert.Description
                    >{valueResult.current.error}</Alert.Description
                >
            </Alert.Root>
        {:else if deviceStatus === 'pending'}
            <!-- Activation Form -->
            <Card.Root>
                <Card.Header>
                    <div class="flex items-start justify-between">
                        <div>
                            <Card.Title class="text-base"
                                >Form Aktivasi</Card.Title
                            >
                            <Card.Description>
                                Device ditemukan tapi belum aktif. Lengkapi form
                                berikut.
                            </Card.Description>
                        </div>
                        <Badge.Root
                            variant="outline"
                            class="text-amber-600 border-amber-300 bg-amber-50 dark:bg-amber-950/30 dark:border-amber-800 dark:text-amber-400 gap-1.5"
                        >
                            <ShieldX class="size-3" />
                            Belum Aktif
                        </Badge.Root>
                    </div>
                </Card.Header>

                <Separator.Root />

                <Card.Content class="pt-6">
                    <form onsubmit={handleSubmit} class="space-y-6">
                        <!-- Upload -->
                        <Field.Set>
                            <Field.Group>
                                <Field.Field>
                                    <Field.Label>
                                        Foto Device
                                        <span class="text-destructive ml-0.5"
                                            >*</span
                                        >
                                    </Field.Label>
                                    <FileDropZone.Root
                                        id="DropZoneImage"
                                        {onUpload}
                                        {onFileRejected}
                                        maxFileSize={5 * FileDropZone.MEGABYTE}
                                        accept="image/*"
                                        maxFiles={4}
                                        fileCount={files.length}
                                    >
                                        <FileDropZone.Trigger
                                            title="Upload foto device (maks. 4 foto)"
                                        />
                                    </FileDropZone.Root>
                                    <Field.Description>
                                        Upload foto fisik device sebagai bukti
                                        verifikasi. Maks. 4 foto, 5MB per foto.
                                    </Field.Description>
                                </Field.Field>
                            </Field.Group>
                        </Field.Set>

                        <!-- File Previews -->
                        {#if files.length > 0}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                {#each files as file, i (file.name)}
                                    <div
                                        class="relative group rounded-lg overflow-hidden border bg-muted aspect-square"
                                    >
                                        <img
                                            src={file.url}
                                            alt={file.name}
                                            class="w-full h-full object-cover"
                                        />
                                        <!-- Overlay on hover -->
                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center"
                                        >
                                            <Button
                                                variant="destructive"
                                                size="icon"
                                                class="size-7 opacity-0 group-hover:opacity-100 transition-opacity"
                                                onclick={() => {
                                                    URL.revokeObjectURL(
                                                        file.url,
                                                    );
                                                    files = [
                                                        ...files.slice(0, i),
                                                        ...files.slice(i + 1),
                                                    ];
                                                }}
                                            >
                                                <XIcon class="size-3.5" />
                                            </Button>
                                        </div>
                                        <!-- File name tooltip at bottom -->
                                        <div
                                            class="absolute bottom-0 inset-x-0 bg-black/60 px-2 py-1 translate-y-full group-hover:translate-y-0 transition-transform"
                                        >
                                            <p
                                                class="text-white text-xs truncate"
                                            >
                                                {file.name}
                                            </p>
                                            <p class="text-white/60 text-xs">
                                                {FileDropZone.displaySize(
                                                    file.size,
                                                )}
                                            </p>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        {/if}

                        <Separator.Root />

                        <!-- Comment -->
                        <Field.Set>
                            <Field.Group>
                                <Field.Field>
                                    <Field.Label for="comment"
                                        >Komentar</Field.Label
                                    >
                                    <Textarea
                                        id="comment"
                                        bind:value={form.comment}
                                        placeholder="Contoh: 'HP untuk DST Transmisi'"
                                        rows={3}
                                        class="resize-none"
                                    />
                                    <Field.Description>
                                        Keterangan tambahan tentang device ini
                                        (opsional)
                                    </Field.Description>
                                </Field.Field>
                            </Field.Group>
                        </Field.Set>

                        <!-- Actions -->
                        <div class="flex justify-between pt-2">
                            <Button
                                type="button"
                                variant="ghost"
                                onclick={handleCancel}
                            >
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                disabled={form.processing}
                                class="gap-2"
                            >
                                {#if form.processing}
                                    <Spinner class="size-4" />
                                    Memproses...
                                {:else}
                                    <CheckCircle2 class="size-4" />
                                    Aktifkan Device
                                {/if}
                            </Button>
                        </div>
                    </form>
                </Card.Content>
            </Card.Root>
        {/if}
    </div>

    <!-- QR Scanner Modal -->
    {#if openScanner}
        <div
            class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm"
        >
            <Card.Root class="w-full max-w-sm mx-4">
                <Card.Header>
                    <Card.Title class="text-base">Scan QR Code</Card.Title>
                    <Card.Description
                        >Arahkan kamera ke QR code pada device</Card.Description
                    >
                </Card.Header>
                <Card.Content class="space-y-4">
                    <div
                        class="rounded-lg overflow-hidden bg-black aspect-square"
                    >
                        <video
                            bind:this={video}
                            class="w-full h-full object-cover"
                            playsinline
                        ></video>
                    </div>
                    <Button
                        variant="outline"
                        class="w-full"
                        onclick={stopScanner}
                    >
                        Tutup Scanner
                    </Button>
                </Card.Content>
            </Card.Root>
        </div>
    {/if}
</SidebarProvider>
