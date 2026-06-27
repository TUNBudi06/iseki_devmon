<script lang="ts">
    import { Button } from '$shadcn/components/ui/button';
    import * as Card from '$shadcn/components/ui/card';
    import { Badge } from '$shadcn/components/ui/badge';
    import { Separator } from '$shadcn/components/ui/separator';
    import { QRCode } from '$shadcn/components/spell/qrcode';
    import { ScanQrCode, X } from '@lucide/svelte';
    import type { PhoneList } from './types';

    let {
        target,
        show,
        onclose,
    }: {
        target: PhoneList | null;
        show: boolean;
        onclose: () => void;
    } = $props();

    function getQrValue(phone: PhoneList): string {
        return phone.hash_token ?? phone.model_id;
    }
</script>

{#if show && target}
    <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <Card.Root class="w-full max-w-sm border-emerald-500/30 bg-card shadow-2xl animate-in zoom-in-95 duration-200">
            <Card.Header class="text-center">
                <div class="flex items-center justify-center gap-3 mb-1">
                    <div class="size-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <ScanQrCode class="size-5 text-emerald-400" />
                    </div>
                    <div class="text-left">
                        <Card.Title class="text-lg">QR Code</Card.Title>
                        <Card.Description>
                            Scan untuk mendaftarkan <span class="font-medium">{target.model_name}</span>
                        </Card.Description>
                    </div>
                </div>
            </Card.Header>
            <Separator class="opacity-50" />
            <Card.Content class="pt-6 pb-2 flex flex-col items-center gap-4">
                <div class="rounded-2xl bg-white p-4 shadow-lg ring-1 ring-emerald-500/20">
                    <QRCode value={getQrValue(target)} size={220} fgColor="#000000" bgColor="#ffffff" />
                </div>
                <div class="text-center space-y-1">
                    <Badge variant="outline" class="font-mono text-xs bg-emerald-500/10 border-emerald-300/30 text-emerald-600 dark:text-emerald-300">
                        {target.model_id}
                    </Badge>
                    <p class="text-xs text-muted-foreground mt-2">
                        Buka aplikasi di HP, pilih "Daftarkan Perangkat" lalu scan QR ini
                    </p>
                </div>
            </Card.Content>
            <Card.Footer class="flex justify-center pt-2">
                <Button variant="outline" onclick={onclose} class="gap-2">
                    <X class="size-4" /> Tutup
                </Button>
            </Card.Footer>
        </Card.Root>
    </div>
{/if}
