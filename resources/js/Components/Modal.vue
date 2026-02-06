<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;

            dialog.value?.showModal();
        } else {
            document.body.style.overflow = '';

            setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();

        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);

    document.body.style.overflow = '';
});

const maxWidthStyle = computed(() => {
    const sizes = {
        sm: '24rem',
        md: '28rem',
        lg: '32rem',
        xl: '36rem',
        '2xl': '42rem',
    };

    return {
        maxWidth: sizes[props.maxWidth] || sizes['2xl'],
        width: '100%',
    };
});
</script>

<template>
    <dialog class="border-0 bg-transparent p-0" ref="dialog">
        <div class="position-fixed top-0 start-0 w-100 h-100 overflow-auto py-4" style="z-index: 1050;" scroll-region>
            <div
                v-show="show"
                class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75"
                @click="close"
            ></div>

            <div class="d-flex justify-content-center align-items-start mt-4 mt-md-5" v-show="show">
                <div class="modal-dialog" :style="maxWidthStyle">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <slot v-if="showSlot" />
                    </div>
                </div>
            </div>
        </div>
    </dialog>
</template>
