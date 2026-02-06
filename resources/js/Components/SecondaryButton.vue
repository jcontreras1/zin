<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="btn btn-outline-secondary fw-semibold text-uppercase"
    >
        <slot />
    </button>
</template>
