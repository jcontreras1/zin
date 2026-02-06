<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h4 mb-0 text-uppercase">Profile</h2>
        </template>

        <div class="py-3">
            <div class="d-flex flex-column gap-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
