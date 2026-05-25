<script lang="ts">
    import { onMount, onDestroy } from 'svelte';
    import { router } from '@inertiajs/svelte';
    import { TableHandler, RowsPerPage, RowCount, Pagination } from '@vincjo/datatables';
    import { Search as DtSearch } from '@vincjo/datatables';

    let pollInterval: ReturnType<typeof setInterval> | null = null;

    onMount(() => {
        pollInterval = setInterval(() => {
            router.reload({ only: ['deviceLatest', 'hasAbsence', 'noAbsence'] });
        }, 10000);
    });

    onDestroy(() => {
        if (pollInterval) clearInterval(pollInterval);
    });
    import {
        Smartphone,
        CheckCircle2,
        Clock,
        User,
        IdCard,
        CalendarDays,
        ArrowLeft,
        ArrowRight,
        FileText,
        Save,
        ScanQrCode,
    } from '@lucide/svelte';
    import Particles from '$shadcn/components/Particles.svelte';
    import { QRCode } from '$shadcn/components/spell/qrcode';
    import { toast } from 'svelte-sonner';

    import LayoutBG from '$/components/LayoutBG.svelte';
    import * as Card from '$shadcn/components/ui/card';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Button } from '$shadcn/components/ui/button';
    import { Textarea } from '$shadcn/components/ui/textarea';
    import { home } from '$routes';
    import { loginMember, dashboard } from '$routes/user';

    type DeviceItem = {
        model_id: string;
        model_name: string;
        brand_name: string | null;
        latest_user_name?: string | null;
        latest_user_nik?: string | null;
        latest_time?: string | null;
    };

    type UserAbsence = {
        id?: number;
        nik: string;
        name: string;
        time_absence: string;
        date_absence: string;
        catatan?: string | null;
    };

    let { deviceLatest, currentDevice, hasAbsence, noAbsence }:
        { deviceLatest: UserAbsence | null; currentDevice: { model_id: string; model_name: string; hash_token?: string | null }; hasAbsence: DeviceItem[]; noAbsence: DeviceItem[] } = $props();

    const tableHasAbsence = $derived(hasAbsence.length > 0 ? new TableHandler(hasAbsence, { rowsPerPage: 10 }) : null);
    const tableNoAbsence = $derived(noAbsence.length > 0 ? new TableHandler(noAbsence, { rowsPerPage: 10 }) : null);

    let today = $derived(new Date().toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    }));

    let deviceId = $derived(currentDevice.model_id);

    // ─── Catatan ───────────────────────────────────────────────────
    let catatan = $state('');
    let savingCatatan = $state(false);

    // ─── QR Modal ──────────────────────────────────────────────────
    let showQrModal = $state(false);

    function initCatatan() {
        catatan = deviceLatest?.catatan ?? '';
    }
    $effect(() => { initCatatan(); });

    async function saveCatatan() {
        if (!deviceLatest?.id) return;
        savingCatatan = true;
        router.post(dashboard.catatan(deviceId).url, {
            absence_id: deviceLatest.id,
            catatan: catatan,
        }, {
            onSuccess: () => {
                toast.success('Catatan berhasil disimpan');
                savingCatatan = false;
            },
            onError: () => {
                toast.error('Gagal menyimpan catatan');
                savingCatatan = false;
            },
            onFinish: () => {
                savingCatatan = false;
            },
        });
    }
</script>

<LayoutBG class="min-h-screen bg-background p-4">
    <Particles particleCount={200} particleColors={['#000000', '#ff00ae', '#ffffff']} particleBaseSize={100} speed={0.05} class="absolute inset-0 z-0 opacity-20" />

    <div class="w-full max-w-3xl mx-auto relative z-10 space-y-4">
        <!-- Back -->
        <button onclick={() => router.visit(home().url)} class="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors">
            <ArrowLeft class="size-4" /> Kembali
        </button>

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight">
                    <span class="text-sm font-normal text-muted-foreground">{currentDevice.model_name}</span>
                    <br />Dashboard Perangkat
                </h1>
                <p class="text-sm text-muted-foreground">{today}</p>
            </div>
            <div class="text-right">
                <Badge variant="secondary" class="text-xs">{hasAbsence.length + noAbsence.length} total device</Badge>
                <div class="text-xs text-muted-foreground font-mono mt-1">{currentDevice.model_id}</div>
            </div>
        </div>

        <!-- User Info Card — who last logged in on THIS device -->
        {#if deviceLatest}
            <Card.Root class="border-emerald-500/30 bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 backdrop-blur-xl overflow-hidden">
                <div class="absolute top-0 right-0 size-32 bg-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>

                <Card.Content class="p-4 relative">
                    <div class="flex items-center gap-2 mb-3">
                        <CheckCircle2 class="size-5 text-emerald-500" />
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400 text-sm">Terakhir Absen</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                        <div class="flex items-center gap-2">
                            <User class="size-4 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground shrink-0">Nama:</span>
                            <span class="font-medium truncate">{deviceLatest.name}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <IdCard class="size-4 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground shrink-0">NIK:</span>
                            <span class="font-medium font-mono">{deviceLatest.nik}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Clock class="size-4 text-muted-foreground shrink-0" />
                            <span class="text-muted-foreground shrink-0">Jam:</span>
                            <span class="font-medium font-mono">{deviceLatest.time_absence}</span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="mt-4 pt-4 border-t border-emerald-500/20 space-y-2">
                        <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                            <FileText class="size-4" />
                            <span class="font-medium text-emerald-600 dark:text-emerald-400 text-xs uppercase tracking-wider">Catatan</span>
                        </div>
                        <Textarea
                            bind:value={catatan}
                            placeholder="Tulis catatan kegiatan..."
                            class="min-h-[80px] text-sm bg-background/50 border-emerald-500/30 focus:border-emerald-500/50"
                        />
                        <div class="flex justify-end">
                            <Button
                                size="sm"
                                onclick={saveCatatan}
                                disabled={savingCatatan}
                                class="gap-1.5 text-xs h-8"
                            >
                                {#if savingCatatan}
                                    <div class="size-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                    Menyimpan...
                                {:else}
                                    <Save class="size-3.5" />
                                    Simpan Catatan
                                {/if}
                            </Button>
                        </div>
                    </div>
                </Card.Content>
            </Card.Root>
        {:else}
            <Card.Root class="border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-amber-500/5 backdrop-blur-xl overflow-hidden">
                <Card.Content class="p-4 relative">
                    <div class="flex items-center gap-2">
                        <Clock class="size-5 text-amber-500" />
                        <span class="font-semibold text-amber-600 dark:text-amber-400 text-sm">Belum Ada Absen Hari Ini</span>
                    </div>
                    <p class="text-sm text-muted-foreground mt-1">Gunakan perangkat ini untuk melakukan absensi</p>
                </Card.Content>
            </Card.Root>
        {/if}

        <!-- Action Buttons -->
        <div class="flex justify-center gap-3 py-2 flex-wrap">
            {#if currentDevice.hash_token}
                <Button onclick={() => showQrModal = true} variant="outline" class="gap-2" size="lg">
                    <ScanQrCode class="size-4" />
                    Tampilkan QR
                </Button>
            {/if}
            <Button onclick={() => router.visit(loginMember.input(deviceId).url)} class="gap-2" size="lg">
                <ArrowRight class="size-4" />
                Input Absence Lagi
            </Button>
        </div>

        <!-- Table: Sudah Absen (all devices, read-only) -->
        <Card.Root class="border-border/60 bg-card/80 backdrop-blur-xl">
            <Card.Header class="pb-3">
                <Card.Title class="text-base flex items-center gap-2">
                    <CheckCircle2 class="size-4 text-emerald-500" />
                    Sudah Absen
                    <Badge variant="secondary" class="ml-auto text-xs">{hasAbsence.length} device</Badge>
                </Card.Title>
            </Card.Header>

            <Card.Content>
                {#if hasAbsence.length === 0}
                    <div class="text-center py-6 text-muted-foreground text-sm">
                        Belum ada perangkat yang digunakan hari ini
                    </div>
                {:else if tableHasAbsence}
                    <div class="flex items-center gap-3 px-4 py-2 border-b border-border/30 bg-muted/10 flex-wrap">
                        <DtSearch table={tableHasAbsence} />
                        <RowsPerPage table={tableHasAbsence} />
                    </div>
                    <div class="p-3 space-y-2">
                        {#each tableHasAbsence.rows as row}
                            <div class="rounded-xl border border-border/60 bg-card p-3 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-sm">{row.model_name}</div>
                                    <div class="text-xs text-muted-foreground font-mono">{row.model_id}</div>
                                    <div class="text-xs text-muted-foreground mt-0.5">{row.brand_name ?? '-'}</div>
                                    {#if row.latest_user_name}
                                        <div class="mt-1 flex items-center gap-1.5 text-xs">
                                            <User class="size-3 text-emerald-500 shrink-0" />
                                            <span class="font-medium">{row.latest_user_name}</span>
                                            <span class="text-muted-foreground font-mono">· {row.latest_user_nik}</span>
                                            <span class="text-muted-foreground">· {row.latest_time}</span>
                                        </div>
                                    {:else}
                                        <div class="text-xs text-muted-foreground mt-1">Tidak ada pengguna</div>
                                    {/if}
                                </div>
                                <Badge class="bg-emerald-500/15 text-emerald-600 border-emerald-300/30 text-xs whitespace-nowrap shrink-0">
                                    <CheckCircle2 class="size-3 mr-1 inline" /> Sudah
                                </Badge>
                            </div>
                        {/each}
                    </div>
                    <div class="flex items-center justify-between px-4 py-2 border-t border-border/30 bg-muted/10">
                        <RowCount table={tableHasAbsence} />
                        <Pagination table={tableHasAbsence} />
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>

        <!-- Table: Belum Absen (all devices, read-only) -->
        <Card.Root class="border-border/60 bg-card/80 backdrop-blur-xl">
            <Card.Header class="pb-3">
                <Card.Title class="text-base flex items-center gap-2">
                    <Clock class="size-4 text-muted-foreground" />
                    Belum Absen
                    <Badge variant="secondary" class="ml-auto text-xs">{noAbsence.length} device</Badge>
                </Card.Title>
            </Card.Header>

            <Card.Content>
                {#if noAbsence.length === 0}
                    <div class="text-center py-6 text-muted-foreground text-sm">
                        Semua perangkat sudah digunakan hari ini
                    </div>
                {:else if tableNoAbsence}
                    <div class="flex items-center gap-3 px-4 py-2 border-b border-border/30 bg-muted/10 flex-wrap">
                        <DtSearch table={tableNoAbsence} />
                        <RowsPerPage table={tableNoAbsence} />
                    </div>
                    <div class="p-3 space-y-2">
                        {#each tableNoAbsence.rows as row}
                            <div class="rounded-xl border border-border/60 bg-card p-3 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-sm">{row.model_name}</div>
                                    <div class="text-xs text-muted-foreground font-mono">{row.model_id}</div>
                                    <div class="text-xs text-muted-foreground mt-0.5">{row.brand_name ?? '-'}</div>
                                </div>
                                <Badge variant="outline" class="text-xs text-muted-foreground border-dashed whitespace-nowrap shrink-0">
                                    <Clock class="size-3 mr-1 inline" /> Belum
                                </Badge>
                            </div>
                        {/each}
                    </div>
                    <div class="flex items-center justify-between px-4 py-2 border-t border-border/30 bg-muted/10">
                        <RowCount table={tableNoAbsence} />
                        <Pagination table={tableNoAbsence} />
                    </div>
                {/if}
            </Card.Content>
        </Card.Root>
    </div>
</LayoutBG>

<!-- QR Modal -->
{#if showQrModal && currentDevice.hash_token}
    <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm" role="dialog" aria-modal="true" onclick={() => showQrModal = false}>
        <Card.Root class="w-full max-w-sm border-violet-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200" onclick={(e) => e.stopPropagation()}>
            <Card.Header class="text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div class="size-10 rounded-xl bg-violet-500/20 flex items-center justify-center">
                        <ScanQrCode class="size-5 text-violet-400" />
                    </div>
                    <div class="text-left">
                        <Card.Title class="text-lg">Verifikasi Perangkat</Card.Title>
                        <Card.Description>
                            Tunjukkan QR ini ke admin
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Card.Content class="pt-4 pb-2 flex flex-col items-center gap-4">
                <div class="rounded-2xl bg-white p-4 shadow-lg ring-1 ring-violet-500/20">
                    <QRCode value={currentDevice.hash_token} size={220} fgColor="#000000" bgColor="#ffffff" />
                </div>
                <div class="text-center space-y-1">
                    <code class="text-xs font-mono break-all text-violet-600 dark:text-violet-400">{currentDevice.hash_token}</code>
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-center pt-2">
                <Button variant="outline" onclick={() => showQrModal = false} class="gap-2">
                    Tutup
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}

<style>
</style>
