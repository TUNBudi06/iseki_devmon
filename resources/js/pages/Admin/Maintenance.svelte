<script lang="ts">
    import LayoutBG from "$/components/LayoutBG.svelte";

    function goBack() {
        router.visit(dashboard().url);
    }
</script>
<LayoutBG>
    <div class="min-h-screen w-full">
        <div class="w-full">
            <div class="bg-linear-to-r h-20 mx-10 from-pink-500 to-pink-700"></div>
        </div>
    </div>
</LayoutBG>
