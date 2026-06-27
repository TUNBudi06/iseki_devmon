<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import { goBack } from '$lib/navigation.ts';
    import {
        CircleArrowLeft,
        Printer,
        FileDown,
        LoaderCircle,
    } from '@lucide/svelte';
    import { Button } from '$shadcn/components/ui/button';
    import TemplateA4 from '$lib/component/wrapper/TemplateA4.svelte';
    import IdCardTemplate from '$lib/component/wrapper/IdCardTemplate.svelte';
    import html2canvas from 'html2canvas';
    import jsPDF from 'jspdf';
    import { toast } from 'svelte-sonner';

    let { data } = $props();

    // ─── Pagination ──────────────────────────────────────────────
    const PER_PAGE = 16; // 4 cols × 4 rows

    let pages = $derived.by(() => {
        const chunks: typeof data[] = [];
        for (let i = 0; i < data.length; i += PER_PAGE) {
            chunks.push(data.slice(i, i + PER_PAGE));
        }
        return chunks;
    });

    let pageCount = $derived(pages.length);

    // ─── Refs ────────────────────────────────────────────────────
    let pageRefs: HTMLDivElement[] = $state([]);
    let isCapturing = $state(false);

    // ─── Browser Print ───────────────────────────────────────────
    function handlePrint() {
        window.print();
    }

    // ─── Capture Multi-Page A4 PDF ───────────────────────────────
    async function handleCapturePdf() {
        if (isCapturing) return;

        isCapturing = true;

        try {
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
            });

            for (let i = 0; i < pageRefs.length; i++) {
                const el = pageRefs[i];
                if (!el) continue;

                const canvas = await html2canvas(el, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                });

                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 210;
                const imgHeight = 297;

                if (i > 0) {
                    pdf.addPage();
                }
                pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            }

            pdf.save('device-qr-codes.pdf');
            toast.success('PDF berhasil diunduh');
        } catch (err) {
            console.error('PDF capture failed:', err);
            toast.error('Gagal membuat PDF');
        } finally {
            isCapturing = false;
        }
    }
</script>

<LayoutBG class="md:p-4 bg-linear-to-br from-pink-50 via-pink-100 to-pink-50">
    <div class="w-full mx-auto relative z-10">
        <!-- ─── Header ──────────────────────────────────────────── -->
        <div
            class="no-print mx-auto p-4 md:max-w-[98%] rounded-xl bg-linear-to-r from-pink-500 to-pink-800 shadow-lg shadow-pink-500/20"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <Printer class="size-5 text-white" />
                    </div>
                    <div>
                        <h2 class="font-bold text-white text-lg">Print QR Device</h2>
                        <p class="text-pink-200 text-xs">{data.length} device &middot; {pageCount} halaman</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Print Button -->
                    <Button
                        variant="secondary"
                        onclick={handlePrint}
                        class="bg-white/15 hover:bg-white/25 text-white border-white/20 gap-2 transition-all"
                    >
                        <Printer class="size-4" />
                        <span class="hidden sm:inline">Print</span>
                    </Button>

                    <!-- Capture PDF Button -->
                    <Button
                        onclick={handleCapturePdf}
                        disabled={isCapturing}
                        class="bg-white hover:bg-pink-50 text-pink-700 gap-2 shadow-md shadow-pink-900/20 transition-all"
                    >
                        {#if isCapturing}
                            <LoaderCircle class="size-4 animate-spin" />
                            <span class="hidden sm:inline">Membuat PDF...</span>
                        {:else}
                            <FileDown class="size-4" />
                            <span class="hidden sm:inline">Download PDF</span>
                        {/if}
                    </Button>

                    <!-- Back Button -->
                    <Button
                        variant="ghost"
                        onclick={goBack}
                        class="text-pink-200 hover:text-white hover:bg-white/10 transition-all"
                    >
                        <CircleArrowLeft class="size-4" />
                        <span class="hidden sm:inline">Back</span>
                    </Button>
                </div>
            </div>
        </div>

        <!-- ─── Info Banner ─────────────────────────────────────── -->
        <div class="no-print mx-auto md:max-w-[98%] mt-3 px-1">
            <div
                class="flex items-center gap-2 text-xs text-muted-foreground bg-white/80 rounded-lg px-4 py-2 border border-pink-200/50"
            >
                <div class="size-1.5 rounded-full bg-pink-400 animate-pulse"></div>
                <span>
                    Klik <strong>Print</strong> untuk mencetak via browser, atau
                    <strong>Download PDF</strong> untuk menyimpan sebagai file PDF.
                    {#if pageCount > 1}
                        <span class="text-pink-500 font-medium"> ({pageCount} halaman)</span>
                    {/if}
                </span>
            </div>
        </div>

        <!-- ─── A4 Print Area (multi-page) ──────────────────────── -->
        <div class="mx-auto md:max-w-[98%] mt-4 flex flex-col items-center gap-8">
            {#each pages as pageDevices, pageIdx (pageIdx)}
                {@const isLast = pageIdx === pages.length - 1}
                <div
                    bind:this={pageRefs[pageIdx]}
                    class="print-page inline-block"
                    class:page-break={!isLast}
                >
                    <TemplateA4
                        orientation="portrait"
                        class="p-[10mm] grid grid-cols-4 grid-rows-4 gap-x-[10mm] gap-y-[15mm] place-items-center shadow-2xl rounded-md"
                    >
                        {#each pageDevices as device, id (`${pageIdx}-${id}`)}
                            <IdCardTemplate
                                model={device.model_id}
                                name={device.brand_id.toUpperCase() + ' ' + device.model_name}
                                hash={device.hash_token}
                            />
                        {/each}
                    </TemplateA4>
                </div>
            {/each}
        </div>

        <!-- ─── Footer Info ─────────────────────────────────────── -->
        <div class="no-print mx-auto md:max-w-[98%] mt-4 text-center">
            <p class="text-[11px] text-muted-foreground">
                {data.length} kartu QR &middot; Format A4 Portrait &middot; {pageCount} halaman
            </p>
        </div>
    </div>
</LayoutBG>

<style>
    @media print {
        :global(.bg-linear-to-br),
        :global(.from-pink-50),
        :global(.to-pink-50) {
            background: white !important;
        }

        :global(.shadow-2xl) {
            box-shadow: none !important;
        }

        /* Hide header, buttons, and footer during print */
        :global(.no-print) {
            display: none !important;
        }

        /* Hide LayoutBG background blobs */
        :global(.absolute.inset-0.overflow-hidden.pointer-events-none) {
            display: none !important;
        }

        /* Page break between A4 pages */
        :global(.page-break) {
            page-break-after: always;
        }

        /* Remove gap between pages in print */
        :global(.print-page) {
            margin: 0;
            padding: 0;
        }
    }
</style>
