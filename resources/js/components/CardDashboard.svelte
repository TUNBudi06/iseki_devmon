<script lang="ts">
    import * as Card from '$shadcn/components/ui/card/index.js';
    import CountUp from '$/components/CountUp.svelte';
    let { title, value, comment = '' } = $props();
</script>

<Card.Root class="w-full h-full">
    <Card.Header>
        <Card.Title class="title">{title}</Card.Title>
    </Card.Header>
    <Card.Content>
        <CountUp class="text-4xl font-bold" to={value} delay={0.5} />
    </Card.Content>
    <Card.Footer class="h-full">
        <Card.Description>{comment}</Card.Description>
    </Card.Footer>
</Card.Root>
