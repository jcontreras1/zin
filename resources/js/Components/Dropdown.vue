<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'bg-dark text-white rounded-3 p-2',
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthStyle = computed(() => {
    const preset = {
        48: '12rem',
    }[props.width.toString()];

    return {
        minWidth: preset || props.width,
    };
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'start-0';
    }

    if (props.align === 'right') {
        return 'end-0';
    }

    return 'start-50 translate-middle-x';
});

const open = ref(false);
</script>

<template>
    <div class="position-relative">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open"
            class="position-fixed top-0 start-0 w-100 h-100"
            style="z-index: 1040;"
            @click="open = false"
        ></div>

        <div
            v-show="open"
            class="dropdown-menu position-absolute shadow mt-2 border-0"
            :class="[alignmentClasses, contentClasses, { show: open }]"
            :style="[widthStyle, { zIndex: 1050 }]"
            @click="open = false"
        >
            <slot name="content" />
        </div>
    </div>
</template>
