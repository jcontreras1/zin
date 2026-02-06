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
        'list-group-item list-group-item-action bg-transparent border-0 px-0 text-uppercase small',
        props.active ? 'fw-semibold text-body' : 'text-secondary',
    ].join(' '),
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
