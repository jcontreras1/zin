<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="small text-danger mb-0">
            {{ message }}
        </p>
    </div>
</template>
