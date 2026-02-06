<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="h5 text-uppercase mb-1">Profile Information</h2>
            <p class="text-muted small">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="row gy-3">
            <div class="col-12">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="col-12">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="col-12">
                <div class="alert alert-warning small mb-3">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="alert-link ms-1"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </div>

                <div v-show="status === 'verification-link-sent'" class="text-success small">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="col-12 d-flex align-items-center gap-3">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <p v-if="form.recentlySuccessful" class="text-muted small mb-0">Saved.</p>
            </div>
        </form>
    </section>
</template>
