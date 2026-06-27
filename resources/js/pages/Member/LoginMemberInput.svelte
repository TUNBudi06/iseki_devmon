<script lang="ts">
    import LoginMember from './LoginMember.svelte';

    type DeviceInfo = {
        model_id: string;
        model_name: string;
        imei: string | null;
        mac_address: string | null;
        approved: boolean;
        registered: boolean;
    };

    let {
        device,
        error: serverError,
    }: { device: DeviceInfo | null; error: string | null } = $props();
</script>

<LoginMember {device} error={serverError} mode="additional" />
