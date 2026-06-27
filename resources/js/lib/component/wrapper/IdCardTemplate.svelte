<script lang="ts">
    import {QRCode} from "$shadcn/components/spell/qrcode";

    let {
        model = 'dev-p112-011',
        name = 'Redmi note 13',
        hash = 'sjadn29u3ndoaismc092qimw9duh98j32ifmw0idjsc0',
    } = $props();
</script>

<div class="border-2 border-black w-[35mm] h-[55mm]">
    <div class="w-full h-[10mm] border-b-2 border-black items-center content-center text-center">
        <div class="text-sm">{name}</div>
    </div>
    <div class="w-full text-center space-y-4 items-center content-center pt-5">
        <QRCode value={model + ';'+ hash} errorCorrectionLevel="L" size="110" class="mx-auto" />
        <div class="text-gray-400 text-[8px]">{model}</div>
    </div>
</div>
