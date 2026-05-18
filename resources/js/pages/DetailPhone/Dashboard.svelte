<script lang="ts">
    import LayoutBG from '$/components/LayoutBG.svelte';
    import Navbar from '$/components/wrapper/Navbar.svelte';
</script>

<LayoutBG>
    <Navbar
        class="rounded-2xl w-[97%] mx-auto sticky top-0 border-b-2 border-black bg-pink-400"
    >
        <span
            class="text-4xl font-semibold tracking-tight text-white"
            >DevControl</span
        >
        <!-- tambah items lain per page -->
    </Navbar>
</LayoutBG>
