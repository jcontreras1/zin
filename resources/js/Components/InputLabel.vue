<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="form-label text-uppercase small text-secondary fw-semibold">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
