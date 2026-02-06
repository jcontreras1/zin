<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    products: {
        type: Array,
        default: () => [],
    },
    salePoints: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isAuthenticated = computed(() => page.props.auth?.user != null);

const navLinks = [
    { label: 'Inicio', href: '#inicio', type: 'anchor' },
    { label: 'Shop', href: 'shop', type: 'route' },
    { label: 'Dónde Encontrarnos', href: '#donde-encontrarnos', type: 'anchor' },
    { label: 'Contacto', href: '#contacto', type: 'anchor' },
];

const fallbackProducts = [
    {
        id: 'sample-1',
        title: 'Citrus Bloom',
        description_one: 'Tonicas botanicas, cascara de yuzu, rocio de azahar',
        description_two: 'Lote experimental sin alcohol',
        badge: 'Edicion limitada',
        price_formatted: '',
        cover: null,
        photos_count: 0,
    },
    {
        id: 'sample-2',
        title: 'Andes Smoke',
        description_one: 'Malta tostada, roble humedo, cacao amargo',
        description_two: 'Serie reserva 2025',
        badge: 'Serie reserva',
        price_formatted: '',
        cover: null,
        photos_count: 0,
    },
];

const productCards = computed(() => {
    if (props.products.length) {
        return props.products.map((product) => ({
            id: product.id,
            title: product.title,
            description_one: product.description_one,
            description_two: product.description_two,
            badge: 'Lote activo',
            price_formatted: product.price_formatted,
            cover: product.photos?.[0]?.url ?? null,
            photos_count: product.photos?.length ?? 0,
        }));
    }

    return fallbackProducts;
});

const milestones = [
    {
        year: '2025',
        title: 'Catad’Or World Wine Awards 2025',
        copy: 'Segundo Lugar',
    },
    {
        year: '2018',
        title: 'Sala de botanicos',
        copy: 'Integramos destilaciones al vapor con hojas andinas, frutas de productores vecinos y especias.',
    },
    // {
    //     year: '2023',
    //     title: 'Linea sin alcohol',
    //     copy: 'Creamos macerados vivos pensados para maridar sin renunciar al ritual y la complejidad.',
    // },
];

const contactChannels = [
    // {
    //     title: 'Visitanos',
    //     detail: 'Ruta del Sol km 12, Mendoza',
    //     action: 'Ver mapa',
    //     href: 'https://maps.google.com',
    // },
    {
        title: 'Escribinos',
        detail: 'info@zinfabrica.com',
        action: 'Enviar correo',
        href: 'mailto:info@zinfabrica.com',
    },
    {
        title: 'Llamanos',
        detail: '+54 9 261 555 0110',
        action: 'Llamar',
        href: 'tel:+5492615550110',
    },
];
</script>

<template>
    <Head title="Zin Fabrica" />
    <div class="zin-page overflow-hidden">
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
                <a href="#inicio" class="font-display fs-3 tracking-wide text-decoration-none text-white">ZIN</a>
                <nav class="d-flex flex-wrap gap-4 tracking-wide small fw-semibold text-white-50">
                    <template v-for="link in navLinks" :key="link.label">
                        <a
                            v-if="link.type === 'anchor'"
                            :href="link.href"
                            class="text-decoration-none text-white-50"
                        >
                            {{ link.label }}
                        </a>
                        <Link
                            v-else
                            :href="route(link.href)"
                            class="text-decoration-none text-white-50"
                            preserve-scroll
                        >
                            {{ link.label }}
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="container position-relative py-5 py-lg-5">
            <section id="inicio" class="row g-5 align-items-center pb-5">
                <div class="col-lg-6">
                    <p class="zin-highlight small">Fábrica de Gin Tonic sin alcohol</p>
                    <h1 class="font-display display-4 mt-4">Una nueva forma de brindar</h1>
                    <p class="zin-muted mt-4">
                        Llevamos a cabo un cuidadoso proceso de elaboración para llevar a tu copa bebidas complejas, frescas y equilibradas,
                        sin una gota de alcohol. Perfectas para disfrutar en cualquier momento del día.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 mt-5">
                        <Link :href="route('shop')" class="btn btn-zin zin-pill px-5 py-3" preserve-scroll>
                            Ir al shop
                        </Link>
                        <a href="#contacto" class="btn btn-outline-light zin-pill px-5 py-3 border border-light-subtle">
                            Contactanos
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 position-relative">
                    <div class="position-absolute top-0 start-0 bottom-0 end-0 rounded-5" style="background: radial-gradient(circle, rgba(42,76,59,0.4), transparent); filter: blur(40px);"></div>
                    <div class="position-relative row g-4">
                        <article v-for="product in productCards" :key="product.id" class="col-12 col-md-6 col-lg-12">
                            <div class="zin-card p-4 p-lg-5 h-100">
                                <div v-if="product.cover" class="ratio ratio-4x3 rounded-4 overflow-hidden mb-3">
                                    <img :src="product.cover" class="w-100 h-100 object-fit-cover" alt="Foto de {{ product.title }}" />
                                </div>
                                <p class="zin-badge">{{ product.badge }}</p>
                                <h3 class="font-display fs-3 mt-3">{{ product.title }}</h3>
                                <p class="zin-muted mt-2">{{ product.description_one }}</p>
                                <p v-if="product.description_two" class="text-white-50 small mt-2">{{ product.description_two }}</p>
                                <div class="d-flex justify-content-between mt-4 text-uppercase small tracking-wide text-white-50">
                                    <span v-if="product.price_formatted">${{ product.price_formatted }}</span>
                                    <span>
                                        <template v-if="product.photos_count">{{ product.photos_count }} fotos</template>
                                        <template v-else>Listo para catar</template>
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="zin-gradient-card p-4 p-lg-5 mt-4">
                <div class="row g-5 align-items-start">
                    <div class="col-lg-7">
                        <p class="zin-badge">Nosotros</p>
                        <h2 class="font-display zin-section-title mt-3">Una nueva experiencia 0%</h2>
                        <p class="zin-muted mt-4">
                            En Zin, creemos que disfrutar de una bebida excepcional no debería depender del alcohol.
                            Nuestra misión es ofrecer alternativas sofisticadas y llenas de sabor para quienes buscan una experiencia única sin comprometer la calidad.
                        </p>
                        <div class="row g-3 mt-4">
                            <div class="col-md-6">
                                <div class="zin-card zin-card--dense p-4 h-100">
                                    <p class="zin-badge">Botanicos</p>
                                    <p class="mt-3">Cedron, paramela, pimienta rosa, sauco</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="zin-card zin-card--dense p-4 h-100">
                                    <p class="zin-badge">Base</p>
                                    <p class="mt-3">Mosto de uva blanca, miel cruda y agua de deshielo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex flex-column gap-4">
                            <article v-for="milestone in milestones" :key="milestone.year" class="zin-card zin-card--dense p-4">
                                <p class="zin-badge text-uppercase">{{ milestone.year }}</p>
                                <h3 class="font-display fs-3 mt-2">{{ milestone.title }}</h3>
                                <p class="zin-muted mt-2">{{ milestone.copy }}</p>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section id="donde-encontrarnos" class="mt-5 pb-5">
                <div class="text-center mb-5">
                    <p class="zin-highlight small">Puntos de venta</p>
                    <h2 class="font-display zin-section-title mt-3">Dónde encontrarnos</h2>
                    <p class="zin-muted mt-3 mx-auto" style="max-width: 600px;">
                        Descubrí todos los lugares donde podés conseguir nuestros productos
                    </p>
                </div>

                <div v-if="salePoints.length === 0" class="text-center">
                    <div class="zin-card p-5">
                        <p class="zin-muted">Próximamente compartiremos los puntos de venta.</p>
                    </div>
                </div>

                <div v-else class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <article v-for="salePoint in salePoints" :key="salePoint.id" class="col">
                        <div class="zin-card p-4 h-100 d-flex flex-column">
                            <div v-if="salePoint.logo" class="mb-3 text-center">
                                <img :src="salePoint.logo" :alt="salePoint.name" class="img-fluid" style="max-height: 80px; max-width: 100%;" />
                            </div>
                            <h3 class="font-display fs-4">{{ salePoint.name }}</h3>
                            
                            <div class="mt-3 flex-grow-1">
                                <div v-if="salePoint.address || salePoint.locality || salePoint.province" class="mb-2">
                                    <small class="text-white-50 d-block">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <span v-if="salePoint.address">{{ salePoint.address }}</span>
                                        <span v-if="salePoint.locality">, {{ salePoint.locality }}</span>
                                        <span v-if="salePoint.province"> - {{ salePoint.province }}</span>
                                    </small>
                                </div>
                                
                                <div v-if="salePoint.phone" class="mb-2">
                                    <small class="text-white-50">
                                        <i class="bi bi-telephone me-1"></i> {{ salePoint.phone }}
                                    </small>
                                </div>

                                <div v-if="salePoint.categories.length > 0" class="mt-3">
                                    <small class="text-white-50 d-block mb-1">Productos disponibles:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span v-for="category in salePoint.categories" :key="category" class="zin-badge">
                                            {{ category }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-3 pt-3 border-top border-secondary align-items-center">
                                <a v-if="salePoint.instagram" :href="salePoint.instagram" target="_blank" class="text-white-50 fs-5" title="Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a v-if="salePoint.facebook" :href="salePoint.facebook" target="_blank" class="text-white-50 fs-5" title="Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a v-if="salePoint.webpage" :href="salePoint.webpage" target="_blank" class="text-white-50 fs-5" title="Sitio web">
                                    <i class="bi bi-globe"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section id="contacto" class="row g-5 align-items-start mt-5 pb-5">
                <div class="col-lg-5">
                    <h2 class="font-display zin-section-title">Convertite en distribuidor</h2>
                    <p class="zin-muted mt-3">
                        Si sos un bar, restaurante o comercio interesado en ofrecer nuestros productos, no dudes en contactarnos.
                        Estamos aquí para ayudarte a incorporar Zin a tu oferta y brindarte todo el soporte que necesites.
                    </p>
                    <div class="d-flex flex-column gap-3 mt-4">
                        <article v-for="channel in contactChannels" :key="channel.title" class="zin-card zin-card--dense p-4">
                            <p class="zin-badge">{{ channel.title }}</p>
                            <p class="fs-5 mt-2">{{ channel.detail }}</p>
                            <a :href="channel.href" class="zin-highlight small text-decoration-none">{{ channel.action }}</a>
                        </article>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="zin-card p-4 p-lg-5">
                        <form class="zin-form">
                            <div class="mb-4">
                                <label class="zin-badge">Nombre</label>
                                <input type="text" class="form-control mt-2" placeholder="Tu nombre" />
                            </div>
                            <div class="mb-4">
                                <label class="zin-badge">Email</label>
                                <input type="email" class="form-control mt-2" placeholder="hola@ejemplo.com" />
                            </div>
                            <div class="mb-4">
                                <label class="zin-badge">Mensaje</label>
                                <textarea rows="4" class="form-control mt-2" placeholder="Contanos que queres probar"></textarea>
                            </div>
                            <button type="button" class="btn btn-zin zin-pill w-100 py-3">Enviar consulta</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <footer class="zin-divider py-4 text-center tracking-wide small text-white-50">
            
        </footer>
    </div>
</template>
