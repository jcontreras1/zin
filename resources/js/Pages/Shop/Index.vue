<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const isAuthenticated = computed(() => page.props.auth?.user != null);

const navLinks = [
    { label: 'Inicio', href: '/', type: 'link' },
    { label: 'Shop', href: 'shop', type: 'route' },
    { label: 'Dónde Encontrarnos', href: '/#donde-encontrarnos', type: 'link' },
    { label: 'Contacto', href: '/#contacto', type: 'link' },
];
</script>

<template>
    <Head title="Shop · Zin" />
    <div class="zin-page min-vh-100 overflow-hidden">
        <div class="zin-glow"></div>

        <!-- Barra autenticada -->
        <nav v-if="isAuthenticated" class="navbar navbar-expand-md navbar-dark bg-dark border-bottom border-dark-subtle sticky-top">
            <div class="container py-2">
                <a href="/" class="navbar-brand d-flex align-items-center gap-2 text-uppercase small">
                    <ApplicationLogo class="text-white" style="width: 32px; height: 32px;" />
                    {{ $page.props.appName }} Home
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav me-auto mb-3 mb-md-0 gap-md-3 mt-3 mt-md-0">
                        <li class="nav-item">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </NavLink>
                        </li>
                        <li class="nav-item">
                            <NavLink :href="route('categories.index')" :active="route().current('categories.*')">
                                Categorías
                            </NavLink>
                        </li>
                        <li class="nav-item">
                            <NavLink :href="route('products.index')" :active="route().current('products.*')">
                                Productos
                            </NavLink>
                        </li>
                        <li class="nav-item">
                            <NavLink :href="route('sale-points.index')" :active="route().current('sale-points.*')">
                                Puntos de Venta
                            </NavLink>
                        </li>
                    </ul>

                    <div class="ms-md-auto mt-3 mt-md-0 d-flex flex-column flex-md-row align-items-md-center gap-3 text-white-50">
                        <div>
                            <div class="fw-semibold">{{ $page.props.auth.user.name }}</div>
                            <small class="text-white-50">{{ $page.props.auth.user.email }}</small>
                        </div>

                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="btn btn-outline-light btn-sm text-uppercase">
                                    Perfil
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Barra pública -->
        <header v-else class="zin-nav position-sticky top-0">
            <div class="container py-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <a href="/" class="font-display fs-3 tracking-wide text-decoration-none text-white">ZIN</a>
                <nav class="d-flex flex-wrap gap-4 tracking-wide small fw-semibold text-white-50">
                    <Link
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.type === 'route' ? route(link.href) : link.href"
                        class="text-decoration-none text-white-50"
                    >
                        {{ link.label }}
                    </Link>
                </nav>
            </div>
        </header>

        <main class="container position-relative py-5">
            <header class="text-center mb-5">
                <p class="zin-highlight small">Catálogo completo</p>
                <h1 class="font-display display-4 mt-3">Nuestros Productos</h1>
                <p class="zin-muted mt-3 mx-auto" style="max-width: 600px;">
                    Explora nuestra selección de bebidas sin alcohol, elaboradas con técnicas artesanales y los mejores ingredientes.
                </p>
            </header>

            <!-- Productos -->
            <div v-if="products.data.length === 0" class="text-center py-5">
                <div class="zin-card p-5">
                    <p class="zin-muted">No hay productos disponibles en este momento.</p>
                    <Link href="/" class="btn btn-outline-light zin-pill px-5 py-3 mt-4">
                        Volver al inicio
                    </Link>
                </div>
            </div>

            <div v-else>
                <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
                    <article v-for="product in products.data" :key="product.id" class="col">
                        <div class="zin-card p-4 h-100 d-flex flex-column">
                            <!-- Galería de fotos -->
                            <div v-if="product.photos.length > 0" class="mb-4">
                                <div class="ratio ratio-4x3 rounded-4 overflow-hidden">
                                    <img
                                        :src="product.photos[0].url"
                                        :alt="product.title"
                                        class="w-100 h-100 object-fit-cover"
                                    />
                                </div>
                                <div v-if="product.photos.length > 1" class="d-flex gap-2 mt-2">
                                    <div
                                        v-for="(photo, index) in product.photos.slice(1, 4)"
                                        :key="photo.id"
                                        class="ratio ratio-1x1 rounded-3 overflow-hidden flex-fill"
                                        style="max-width: 80px;"
                                    >
                                        <img
                                            :src="photo.url"
                                            :alt="`${product.title} - Foto ${index + 2}`"
                                            class="w-100 h-100 object-fit-cover"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Información del producto -->
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span v-if="product.category_name" class="zin-badge">{{ product.category_name }}</span>
                                    <span class="badge bg-success">Disponible</span>
                                </div>

                                <h2 class="font-display fs-2 mt-2">{{ product.title }}</h2>

                                <div class="mt-3">
                                    <p class="zin-muted mb-2">{{ product.description_one }}</p>
                                    <p v-if="product.description_two" class="text-white-50 small">
                                        {{ product.description_two }}
                                    </p>
                                </div>
                            </div>

                            <!-- Precio y acciones -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary">
                                <div>
                                    <div class="text-white-50 small text-uppercase tracking-wide">Precio</div>
                                    <div class="font-display fs-3">${{ product.price_formatted }}</div>
                                </div>
                                <button class="btn btn-zin zin-pill px-4" disabled>
                                    Proximamente
                                </button>
                            </div>

                            <!-- Metadata -->
                            <div class="mt-3 text-white-50 small">
                                <span>{{ product.photos.length }} fotos</span>
                                <span class="mx-2">·</span>
                                <span>Actualizado {{ new Date(product.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Paginación -->
                <nav v-if="products.links.length > 3" aria-label="Paginación de productos">
                    <ul class="pagination justify-content-center gap-2">
                        <li
                            v-for="(link, index) in products.links"
                            :key="index"
                            class="page-item"
                            :class="{ active: link.active, disabled: !link.url }"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="page-link bg-dark border-secondary text-white"
                                :class="{ 'bg-zin border-zin': link.active }"
                                preserve-scroll
                                v-html="link.label"
                            />
                            <span v-else class="page-link bg-dark border-secondary text-white-50" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </main>
    </div>
</template>

<style scoped>
.bg-zin {
    background-color: #2a4c3b !important;
}

.border-zin {
    border-color: #2a4c3b !important;
}

.page-link:hover {
    background-color: #1e3829 !important;
    border-color: #2a4c3b !important;
}
</style>
