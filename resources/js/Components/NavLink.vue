<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    [
        'nav-link px-0 me-3 text-uppercase small',
        props.active ? 'active fw-semibold' : 'text-white-50',
    ].join(' '),
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
