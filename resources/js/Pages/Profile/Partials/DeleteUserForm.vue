<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="d-flex flex-column gap-3">
        <header>
            <h2 class="h5 text-uppercase mb-1">Delete Account</h2>
            <p class="text-muted small">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please download any data that you wish to retain
                before continuing.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-4 p-md-5">
                <h2 class="h5 text-uppercase">Are you sure you want to delete your account?</h2>
                <p class="text-muted small mt-2">
                    Once your account is deleted, all of its resources and data will be permanently removed. Please enter your password to confirm this
                    action.
                </p>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" class="visually-hidden" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <DangerButton
                        class="ms-2"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
