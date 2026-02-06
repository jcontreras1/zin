<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="btn btn-danger fw-semibold text-uppercase">
        <slot />
    </button>
</template>
