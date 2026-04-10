<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Modal from '@/Components/Modal.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    hasSalePoints: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const isAuthenticated = computed(() => page.props.auth?.user != null);
const showLightbox = ref(false);
const lightboxTitle = ref('');
const lightboxPhotos = ref([]);
const lightboxIndex = ref(0);

const navLinks = computed(() => [
    { label: 'Inicio', href: '/', type: 'link' },
    { label: 'Shop', href: 'shop', type: 'route' },
    { label: 'Reconocimientos', href: '/#reconocimientos', type: 'link' },
    ...(props.hasSalePoints ? [{ label: 'Dónde Encontrarnos', href: '/#donde-encontrarnos', type: 'link' }] : []),
    { label: 'Contacto', href: '/#contacto', type: 'link' },
]);

const activeLightboxPhoto = computed(() => lightboxPhotos.value[lightboxIndex.value] ?? null);
const hasMultipleLightboxPhotos = computed(() => lightboxPhotos.value.length > 1);

const openLightbox = (product, startIndex = 0) => {
    if (!product.photos?.length) {
        return;
    }

    lightboxTitle.value = product.title;
    lightboxPhotos.value = product.photos;
    lightboxIndex.value = startIndex;
    showLightbox.value = true;
};

const closeLightbox = () => {
    showLightbox.value = false;
    lightboxPhotos.value = [];
    lightboxTitle.value = '';
    lightboxIndex.value = 0;
};

const showPreviousPhoto = () => {
    if (!lightboxPhotos.value.length) {
        return;
    }

    lightboxIndex.value = (lightboxIndex.value - 1 + lightboxPhotos.value.length) % lightboxPhotos.value.length;
};

const showNextPhoto = () => {
    if (!lightboxPhotos.value.length) {
        return;
    }

    lightboxIndex.value = (lightboxIndex.value + 1) % lightboxPhotos.value.length;
};

const handleLightboxKeydown = (event) => {
    if (!showLightbox.value) {
        return;
    }

    if (event.key === 'ArrowLeft') {
        event.preventDefault();
        showPreviousPhoto();
    }

    if (event.key === 'ArrowRight') {
        event.preventDefault();
        showNextPhoto();
    }
};

onMounted(() => document.addEventListener('keydown', handleLightboxKeydown));
onUnmounted(() => document.removeEventListener('keydown', handleLightboxKeydown));
</script>

<template>
    <Head title="Shop" />
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
                        <li class="nav-item">
                            <NavLink :href="route('images.index')" :active="route().current('images.*')">
                                Imágenes
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
                <a href="/" class="text-decoration-none">
                        <ApplicationLogo class="text-white" style="height: 36px; width: auto;" />
                    </a>
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
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                    <article v-for="product in products.data" :key="product.id" class="col">
                        <div class="zin-card p-4 h-100 d-flex flex-column">
                            <!-- Galería de fotos -->
                            <div v-if="product.photos.length > 0" class="mb-4">
                                <button
                                    type="button"
                                    class="zin-photo-trigger ratio ratio-4x3 rounded-4 overflow-hidden p-0 border-0 w-100"
                                    @click="openLightbox(product, 0)"
                                >
                                    <img
                                        :src="product.photos[0].url"
                                        :alt="product.title"
                                        class="w-100 h-100 object-fit-cover"
                                    />
                                </button>
                                <div v-if="product.photos.length > 1" class="d-flex gap-2 mt-2">
                                    <button
                                        v-for="(photo, index) in product.photos.slice(1, 4)"
                                        :key="photo.id"
                                        type="button"
                                        class="zin-photo-trigger ratio ratio-1x1 rounded-3 overflow-hidden flex-fill p-0 border-0"
                                        style="max-width: 80px;"
                                        @click="openLightbox(product, index + 1)"
                                    >
                                        <img
                                            :src="photo.url"
                                            :alt="`${product.title} - Foto ${index + 2}`"
                                            class="w-100 h-100 object-fit-cover"
                                        />
                                    </button>
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
                            <div class="mt-4 pt-3 border-top border-secondary text-white-50 small">
                                <span>{{ product.photos.length }} foto<span v-if="product.photos.length > 1">s</span></span>
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

        <Modal :show="showLightbox" max-width="5xl" @close="closeLightbox">
            <div class="zin-lightbox text-white position-relative overflow-hidden">
                <button
                    type="button"
                    class="btn-close btn-close-white zin-lightbox-close"
                    aria-label="Cerrar visor"
                    @click="closeLightbox"
                ></button>

                <div class="zin-lightbox-frame">
                    <button
                        v-if="hasMultipleLightboxPhotos"
                        type="button"
                        class="zin-lightbox-nav zin-lightbox-nav-prev"
                        aria-label="Foto anterior"
                        @click="showPreviousPhoto"
                    >
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <div class="zin-lightbox-image-wrap">
                        <img
                            v-if="activeLightboxPhoto"
                            :src="activeLightboxPhoto.url"
                            :alt="lightboxTitle"
                            class="zin-lightbox-image"
                        />
                    </div>

                    <button
                        v-if="hasMultipleLightboxPhotos"
                        type="button"
                        class="zin-lightbox-nav zin-lightbox-nav-next"
                        aria-label="Foto siguiente"
                        @click="showNextPhoto"
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div class="zin-lightbox-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                        <div>
                            <p class="zin-highlight small mb-2">Galería</p>
                            <h2 class="font-display fs-3 mb-0">{{ lightboxTitle }}</h2>
                        </div>
                        <div v-if="hasMultipleLightboxPhotos" class="text-white-50 small">
                            Foto {{ lightboxIndex + 1 }} de {{ lightboxPhotos.length }}
                        </div>
                    </div>

                    <div v-if="hasMultipleLightboxPhotos" class="d-flex gap-2 mt-4 overflow-auto pb-2">
                        <button
                            v-for="(photo, index) in lightboxPhotos"
                            :key="photo.id"
                            type="button"
                            class="zin-lightbox-thumb p-0 border-0 rounded-3 overflow-hidden flex-shrink-0"
                            :class="{ 'is-active': index === lightboxIndex }"
                            @click="lightboxIndex = index"
                        >
                            <img
                                :src="photo.url"
                                :alt="`${lightboxTitle} - Miniatura ${index + 1}`"
                                class="w-100 h-100 object-fit-cover"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
.zin-photo-trigger {
    background: transparent;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.zin-photo-trigger:hover {
    transform: translateY(-2px);
    box-shadow: 0 1rem 2.5rem rgba(0, 0, 0, 0.2);
}

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

.zin-lightbox {
    background:
        radial-gradient(circle at top, rgba(102, 140, 112, 0.18), transparent 45%),
        linear-gradient(180deg, rgba(6, 10, 9, 0.98), rgba(12, 18, 15, 0.98));
}

.zin-lightbox-close {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    z-index: 3;
}

.zin-lightbox-frame {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 420px;
    padding: 1.5rem;
}

.zin-lightbox-image-wrap {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.zin-lightbox-image {
    width: 100%;
    max-height: min(72vh, 680px);
    object-fit: contain;
    border-radius: 1.5rem;
}

.zin-lightbox-body {
    padding: 0 1.5rem 1.5rem;
}

.zin-lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 3rem;
    height: 3rem;
    border: 0;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.14);
    color: #fff;
    backdrop-filter: blur(8px);
    z-index: 2;
}

.zin-lightbox-nav-prev {
    left: 1rem;
}

.zin-lightbox-nav-next {
    right: 1rem;
}

.zin-lightbox-thumb {
    width: 72px;
    height: 72px;
    opacity: 0.55;
    transition: opacity 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.zin-lightbox-thumb.is-active,
.zin-lightbox-thumb:hover {
    opacity: 1;
    transform: translateY(-2px);
    box-shadow: 0 0 0 2px rgba(196, 214, 201, 0.7);
}

@media (max-width: 767.98px) {
    .zin-lightbox-frame {
        min-height: 300px;
        padding: 3.5rem 1rem 1rem;
    }

    .zin-lightbox-nav {
        width: 2.5rem;
        height: 2.5rem;
    }

    .zin-lightbox-nav-prev {
        left: 0.5rem;
    }

    .zin-lightbox-nav-next {
        right: 0.5rem;
    }

    .zin-lightbox-body {
        padding: 0 1rem 1rem;
    }
}
</style>
