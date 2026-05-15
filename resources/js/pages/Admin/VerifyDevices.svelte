<script lang="ts">
    import SidebarProvider from '$/components/SidebarProvider.svelte';
    import AppHead from '$/components/AppHead.svelte';
    import { resource } from 'runed';
    import { tick } from 'svelte';
    import {XIcon} from '@lucide/svelte';
    import * as Item from '$shadcn/components/ui/item';
    import { Progress } from '$shadcn/components/ui/progress';
    import { Button } from '$shadcn/components/ui/button';
    import { SvelteDate } from 'svelte/reactivity';
    import {onDestroy} from "svelte";
    import * as FileDropZone from '$shadcn/components/ui/file-drop-zone';

    import QrScanner from 'qr-scanner';
    import { routeUrl } from '@tunbudi06/inertia-route-helper';
    import { deviceInfoId } from '$routes/api';
    import * as Card from '$shadcn/components/ui/card';
    import * as InputGroup from '$shadcn/components/ui/input-group';
    import { Spinner } from '$shadcn/components/ui/spinner';

    let qrValue = $state('');
    let video = $state<HTMLVideoElement>();
    let scanner = $state<QrScanner>();
    let openScanner = $state(false);

    const valueResult = resource(
        () => qrValue,
        async (id, previousValue, { data, signal, refetching, onCleanup }) => {
            const response = await fetch(routeUrl(deviceInfoId()), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ deviceId: id }),
                signal,
            });
            const result = await response.json();
            if (result.hasOwnProperty('error')) {
                console.error(result.error);
            } else {
                console.log('Device Info:', result);
            }
            return result;
        },
        {
            debounce: 500,
        },
    );
    $inspect(valueResult);

    async function startScanner() {
        openScanner = true;

        await tick();

        scanner = new QrScanner(
            video,

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

    // Cleanup effect
    $effect(() => {
        return () => {
            stopScanner();
        };
    });

    const onUpload: FileDropZone.FileDropZoneRootProps['onUpload'] = async (
        files,
    ) => {
        await Promise.allSettled(files.map((file) => uploadFile(file)));
    };
    const onFileRejected: FileDropZone.FileDropZoneRootProps['onFileRejected'] =
        async ({ reason, file }) => {
            toast.error(`${file.name} failed to upload!`, {
                description: reason,
            });
        };
    const uploadFile = async (file: File) => {
        // don't upload duplicate files
        if (files.find((f) => f.name === file.name)) return;
        const urlPromise = new Promise<string>((resolve) => {
            // add some fake loading time
            sleep(1000).then(() => resolve(URL.createObjectURL(file)));
        });
        files.push({
            name: file.name,
            type: file.type,
            size: file.size,
            uploadedAt: Date.now(),
            file: file,
            url: urlPromise,
        });
        // we await since we don't want the onUpload to be complete until the files are actually uploaded
        await urlPromise;
    };
    type UploadedFile = {
        name: string;
        type: string;
        size: number;
        file: File;
        uploadedAt: number;
        url: Promise<string>;
    };
    let files = $state<UploadedFile[]>([]);
    let date = new SvelteDate();
    onDestroy(async () => {
        for (const file of files) {
            URL.revokeObjectURL(await file.url);
        }
    });
    $effect(() => {
        const interval = setInterval(() => {
            date.setTime(Date.now());
        }, 10);
        return () => {
            clearInterval(interval);
        };
    });
</script>

<AppHead title="Verify Devices" />

<SidebarProvider>
    <Card.Root>
        <Card.Header>
            <div class="w-full text-2xl text-center font-bold">
                Activation Device
            </div>

            <div class="w-full text-md text-center font-light">
                digunakan untuk memverifikasi device
            </div>
        </Card.Header>

        <Card.Content>
            <InputGroup.Root class="h-10">
                <InputGroup.Addon align="inline-start">
                    <InputGroup.Button
                        class="bg-green-200"
                        onclick={startScanner}
                    >
                        Scan QR Here
                    </InputGroup.Button>
                </InputGroup.Addon>

                <InputGroup.Input bind:value={qrValue} placeholder="QR Token" />

                <InputGroup.Addon
                    align="inline-end"
                    class="bg-blend-luminosity"
                >
                    <InputGroup.Button onclick={valueResult.refetch}
                        >Verify</InputGroup.Button
                    >
                </InputGroup.Addon>
            </InputGroup.Root>
            {#if valueResult.loading}
                <Item.Root>
                    <Item.Media>
                        <Spinner />
                    </Item.Media>
                    <Item.Description>Fetching list Device....</Item.Description
                    >
                </Item.Root>
            {/if}
            {#if valueResult?.current?.approved}
                <div class="mt-4 p-4 bg-green-100 rounded-lg">
                    <h3 class="text-lg font-bold">Device is Approved</h3>
                    <p>Device ID: {valueResult.current.deviceId}</p>
                    <p>Device Name: {valueResult.current.deviceName}</p>
                </div>
            {/if}
            {#if valueResult?.current?.error}
                <div class="mt-4 p-4 bg-red-100 rounded-lg">
                    <h3 class="text-lg font-bold">Error</h3>
                    <p>{valueResult.current.error}</p>
                </div>
            {/if}
            {#if !valueResult?.current?.approved && !valueResult.current?.error}
                <div class="w-full p-4 mt-4 rounded-lg bg-yellow-100">
                    <h3 class="text-lg font-bold text-center w-full">
                        Device is not Approved
                    </h3>
                    <h3 class="text-sm font-light text-center w-full">
                        tolong isi form dibawah ini untuk aktivasi device
                    </h3>
                </div>
                <FileDropZone.Root
                    {onUpload}
                    {onFileRejected}
                    maxFileSize={3 * FileDropZone.MEGABYTE}
                    accept="image/*"
                    maxFiles={4}
                    fileCount={files.length}
                >
                    <FileDropZone.Trigger />
                </FileDropZone.Root>
                <div class="flex flex-col gap-2">
                    {#each files as file, i (file.name)}
                        <div
                            class="flex place-items-center justify-between gap-2"
                        >
                            <div class="flex place-items-center gap-2">
                                {#await file.url then src}
                                    <div class="relative size-9 overflow-clip">
                                        <img
                                            {src}
                                            alt={file.name}
                                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 overflow-clip"
                                        />
                                    </div>
                                {/await}
                                <div class="flex flex-col">
                                    <span class="text-nowrap">{file.name}</span>
                                    <span class="text-muted-foreground text-xs"
                                        >{FileDropZone.displaySize(
                                            file.size,
                                        )}</span
                                    >
                                </div>
                            </div>
                            {#await file.url}
                                <Progress
                                    class="h-2 w-full grow"
                                    value={((date.getTime() - file.uploadedAt) /
                                        1000) *
                                        100}
                                    max={100}
                                />
                            {:then url}
                                <Button
                                    variant="outline"
                                    size="icon"
                                    onclick={() => {
                                        URL.revokeObjectURL(url);
                                        files = [
                                            ...files.slice(0, i),
                                            ...files.slice(i + 1),
                                        ];
                                    }}
                                >
                                    <XIcon />
                                </Button>
                            {/await}
                        </div>
                    {/each}
                </div>
            {/if}
        </Card.Content>
    </Card.Root>

    {#if openScanner}
        <div
            class="fixed inset-0 bg-black/70 flex items-center justify-center z-50"
        >
            <div class="bg-white p-4 rounded-xl w-full max-w-xl">
                <video bind:this={video} class="w-full rounded-lg" playsinline
                ></video>

                <button
                    class="w-full mt-4 border rounded-lg p-2"
                    onclick={stopScanner}
                >
                    Close Scanner
                </button>
            </div>
        </div>
    {/if}
</SidebarProvider>
