<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center bg-body-secondary py-5 px-3">
        <div class="mb-4 text-center">
            <Link href="/" class="text-decoration-none">
                <ApplicationLogo class="text-body-secondary" style="width: 72px; height: 72px;" />
            </Link>
        </div>

        <div class="card shadow-lg border-0 w-100" style="max-width: 420px;">
            <div class="card-body p-4">
                <slot />
            </div>
        </div>
    </div>
</template>
