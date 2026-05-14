<script lang="ts">
    import SidebarProvider from '$/components/SidebarProvider.svelte';
    import AppHead from '$/components/AppHead.svelte';

    import * as InputGroup from '$shadcn/components/ui/input-group/index.js';
    import * as Card from '$shadcn/components/ui/card';

    import QrScanner from 'qr-scanner';

    let qrValue = $state('');
    let video = $state<HTMLVideoElement>();
    let scanner = $state<QrScanner>();
    let openScanner = $state(false);

    async function startScanner() {
        openScanner = true;

        // Wait for DOM update
        await new Promise(resolve => setTimeout(resolve, 0));

        scanner = new QrScanner(
            video,

            async (result) => {
                qrValue = result.data;

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

                <InputGroup.Addon align="inline-end">
                    <InputGroup.Button>Verify</InputGroup.Button>
                </InputGroup.Addon>
            </InputGroup.Root>
        </Card.Content>
    </Card.Root>

    {#if openScanner}
        <div
            class="fixed inset-0 bg-black/70 flex items-center justify-center z-50"
        >
            <div class="bg-white p-4 rounded-xl w-full max-w-md">
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
