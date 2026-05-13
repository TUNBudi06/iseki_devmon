<script lang="ts">
import {Toaster} from "$shadcn/components/ui/sonner"
let {children} = $props()
</script>
<Toaster position="top-right" richColors />
{@render children?.()}
