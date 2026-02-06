<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h4 mb-0 text-uppercase">Dashboard</h2>
        </template>

        <div class="py-3">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            Bienvenido al panel de Zin.
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <h5 class="mb-1">Carga y administración de productos</h5>
                                <p class="text-muted mb-0">Creá, editá y eliminá productos y subí las fotos para el carrusel.</p>
                            </div>
                            <Link :href="route('products.index')" class="btn btn-zin text-uppercase">
                                Ir a productos
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
