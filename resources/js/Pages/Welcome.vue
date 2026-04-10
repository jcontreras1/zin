<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
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
    galleryImages: {
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
const currentGalleryIndex = ref(0);
const galleryTimer = ref(null);

const galleryImages = computed(() => props.galleryImages ?? []);
const hasGalleryImages = computed(() => galleryImages.value.length > 0);
const hasMultipleGalleryImages = computed(() => galleryImages.value.length > 1);
const activeGalleryImage = computed(() => galleryImages.value[currentGalleryIndex.value] ?? null);

const showPreviousGalleryImage = () => {
    if (!hasGalleryImages.value) {
        return;
    }

    currentGalleryIndex.value = (currentGalleryIndex.value - 1 + galleryImages.value.length) % galleryImages.value.length;
};

const showNextGalleryImage = () => {
    if (!hasGalleryImages.value) {
        return;
    }

    currentGalleryIndex.value = (currentGalleryIndex.value + 1) % galleryImages.value.length;
};

const stopGalleryAutoplay = () => {
    if (galleryTimer.value) {
        clearInterval(galleryTimer.value);
        galleryTimer.value = null;
    }
};

const startGalleryAutoplay = () => {
    stopGalleryAutoplay();

    if (hasMultipleGalleryImages.value) {
        galleryTimer.value = setInterval(() => {
            showNextGalleryImage();
        }, 5000);
    }
};

onMounted(() => {
    startGalleryAutoplay();
});

onUnmounted(() => {
    stopGalleryAutoplay();
});

const milestones = [
    {
        year: '2025',
        title: 'Catad’Or World Wine Awards 2025',
        copy: 'Segundo Lugar',
        image: 'storage/gallery/milestone-catador-2025.jpg',
    },
    // {
    //     year: '2018',
    //     title: 'Sala de botanicos',
    //     copy: 'Integramos destilaciones al vapor con hojas andinas, frutas de productores vecinos y especias.',
    // },
    // {
    //     year: '2023',
    //     title: 'Linea sin alcohol',
    //     copy: 'Creamos macerados vivos pensados para maridar sin renunciar al ritual y la complejidad.',
    // },
];

const navLinks = computed(() => [
    { label: 'Inicio', href: '#inicio', type: 'anchor' },
    { label: 'Shop', href: 'shop', type: 'route' },
    ...(milestones.length > 0 ? [{ label: 'Reconocimientos', href: '#reconocimientos', type: 'anchor' }] : []),
    ...(props.salePoints?.length ? [{ label: 'Dónde Encontrarnos', href: '#donde-encontrarnos', type: 'anchor' }] : []),
    { label: 'Contacto', href: '#contacto', type: 'anchor' },
]);

const contactChannels = [
    // {
    //     title: 'Visitanos',
    //     detail: 'Ruta del Sol km 12, Mendoza',
    //     action: 'Ver mapa',
    //     href: 'https://maps.google.com',
    // },
    // {
    //     title: 'Escribinos',
    //     detail: 'administracion@zin.com.ar',
    //     action: 'Enviar correo',
    //     href: 'mailto:administracion@zin.com.ar',
    // },
    // {
    //     title: 'Llamanos',
    //     detail: '+54 9 261 555 0110',
    //     action: 'Llamar',
    //     href: 'tel:+5492615550110',
    // },
];

const contactForm = useForm({
    name: '',
    email: '',
    message: '',
});

const contactSuccess = computed(() => page.props.flash?.contact_success ?? false);

const submitContact = () => {
    contactForm.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => contactForm.reset(),
    });
};
</script>

<template>

    <!-- <Head title="" /> -->
    <div class="zin-page overflow-hidden">
        <div class="zin-glow"></div>

        <!-- Barra autenticada -->
        <nav v-if="isAuthenticated"
            class="navbar navbar-expand-md navbar-dark bg-dark border-bottom border-dark-subtle sticky-top">
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

                    <div
                        class="ms-md-auto mt-3 mt-md-0 d-flex flex-column flex-md-row align-items-md-center gap-3 text-white-50">
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
                <a href="#inicio" class="text-decoration-none">
                        <ApplicationLogo class="text-white" style="height: 36px; width: auto;" />
                    </a>
                <nav class="d-flex flex-wrap gap-4 tracking-wide small fw-semibold text-white-50">
                    <template v-for="link in navLinks" :key="link.label">
                        <a v-if="link.type === 'anchor'" :href="link.href" class="text-decoration-none text-white-50">
                            {{ link.label }}
                        </a>
                        <Link v-else :href="route(link.href)" class="text-decoration-none text-white-50"
                            preserve-scroll>
                            {{ link.label }}
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="container position-relative py-5 py-lg-5">
            <section id="inicio" class="pb-5">
                <div class="text-center mx-auto" style="max-width: 860px;">
                    <p class="zin-highlight small">Fábrica de Gin Tonic sin alcohol</p>
                    <h1 class="font-display display-4 mt-4">Una nueva forma de brindar</h1>
                    <p class="zin-muted mt-4">
                        Llevamos a cabo un cuidadoso proceso de elaboración para llevar a tu copa bebidas complejas,
                        frescas y
                        equilibradas, sin alcohol. Perfectas para disfrutar en cualquier momento del día.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-5">
                        <Link :href="route('shop')" class="btn btn-zin zin-pill px-5 py-3" preserve-scroll>
                            Ir al shop
                        </Link>
                        <a href="#contacto" class="btn btn-outline-light zin-pill px-5 py-3 border border-light-subtle">
                            Contactanos
                        </a>
                    </div>
                </div>

                <div class="position-relative mt-5">
                    <div class="position-absolute top-0 start-0 bottom-0 end-0 rounded-5"
                        style="background: radial-gradient(circle, rgba(42,76,59,0.4), transparent); filter: blur(40px);">
                    </div>

                    <div v-if="hasGalleryImages" class="position-relative zin-hero-carousel zin-card p-3 p-md-4 p-lg-5">
                        <div class="zin-hero-media rounded-4 overflow-hidden">
                            <img :src="activeGalleryImage.url" :alt="activeGalleryImage.title || 'Imagen destacada Zin'"
                                class="w-100 h-100 object-fit-cover" />
                        </div>

                        <button v-if="hasMultipleGalleryImages" type="button"
                            class="zin-carousel-nav zin-carousel-nav-prev" aria-label="Imagen anterior"
                            @click="showPreviousGalleryImage">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button v-if="hasMultipleGalleryImages" type="button"
                            class="zin-carousel-nav zin-carousel-nav-next" aria-label="Imagen siguiente"
                            @click="showNextGalleryImage">
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <div
                            class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mt-4">
                            <div>
                                <p class="zin-highlight small mb-1">Galería Zin</p>
                                <h3 class="font-display fs-2 mb-0">{{ activeGalleryImage.title || 'Selección destacada'
                                }}</h3>
                                <p v-if="activeGalleryImage.caption" class="zin-muted mt-2 mb-0">{{
                                    activeGalleryImage.caption
                                }}</p>
                            </div>
                            <div v-if="hasMultipleGalleryImages" class="text-white-50 small">
                                {{ currentGalleryIndex + 1 }} / {{ galleryImages.length }}
                            </div>
                        </div>

                        <div v-if="hasMultipleGalleryImages" class="d-flex gap-2 mt-4 overflow-auto pb-1">
                            <button v-for="(image, index) in galleryImages" :key="image.id" type="button"
                                class="zin-carousel-dot border-0 rounded-pill"
                                :class="{ 'is-active': index === currentGalleryIndex }"
                                :aria-label="`Ir a imagen ${index + 1}`" @click="currentGalleryIndex = index"></button>
                        </div>
                    </div>

                    <div v-else class="zin-card p-5 text-center position-relative">
                        <p class="zin-muted mb-0">Todavía no hay imágenes activas para mostrar en la galería.</p>
                    </div>
                </div>
            </section>

            <section class="zin-gradient-card p-4 p-lg-5 mt-4">
                <div class="row g-5 align-items-start">
                    <div class="col">
                        <p class="zin-badge">Nosotros</p>
                        <h3 class="font-display zin-section-title mt-3">Misión</h3>
                        <p class="zin-muted mt-4">
                            En ZIN creamos bebidas sin alcohol que redefinen la experiencia social, permitiendo que
                            todas las personas puedan ser parte del ritual del brindis.
                            <br>
                            <br>
                            Desarrollamos recetas propias que combinan calidad, sabor y equilibrio, ofreciendo
                            alternativas como nuestro bitter y nuestro gin sin alcohol, pensadas para quienes 
                            buscan disfrutar sin resignar bienestar.
                            <br>
                            <br>
                            Nos enfocamos en generar productos accesibles, inclusivos y conscientes, acompañando
                            momentos que merecen ser celebrados.

                        </p>
                        <h3 class="font-display zin-section-title mt-3 pt-4">Visión</h3>
                        <p class="zin-muted mt-4">
                            Ser una marca referente en bebidas sin alcohol, liderando una nueva forma de disfrutar,
                            donde el
                            sabor, la salud y la inclusión conviven en cada encuentro.
                            <br>
                            <br>
                            Aspiramos a impulsar un cambio cultural, en el que elegir no consumir alcohol no sea una
                            excepción,
                            sino una opción natural, valorada y disponible en todos los ámbitos sociales.
                        </p>
                        <h3 class="font-display zin-section-title mt-3 pt-4">Valores</h3>

                        <ul class="list-unstyled mt-4 d-flex flex-column gap-3">
                            <li v-for="(value, key) in {
                                inclusion: 'Inclusión: creemos que todos deberían poder participar plenamente en los momentos sociales.',
                                bienestar: 'Bienestar: desarrollamos productos pensados para cuidar la salud, con bajo contenido de azúcar y enfoque en ingredientes de calidad.',
                                innovacion: 'Innovación: creemos en la creatividad constante. Creamos recetas propias, explorando nuevas formas de disfrutar bebidas complejas sin alcohol.',
                                responsabilidad: 'Responsabilidad: promovemos el consumo consciente y alternativas seguras para quienes practican deporte o eligen no consumir alcohol.'
                            }" :key="key" class="d-flex gap-3">
                                <div class="zin-badge flex-shrink-0" style="width: 4px; height: 4px; margin-top: 8px;"></div>
                                <p class="zin-muted mb-0"><strong>{{ key.charAt(0).toUpperCase() + key.slice(1) }}:</strong> {{ value.split(': ').slice(1).join(': ') }}</p>
                            </li>
                        </ul>


                        <div class="row g-3 mt-4">

                        </div>
                    </div>

                </div>
            </section>

            <section v-if="milestones.length > 0" id="reconocimientos" class="zin-gradient-card p-4 p-lg-5 mt-4">
                <div class="text-center mb-5">
                    <!-- <p class="zin-badge">Reconocimientos</p> -->
                    <h3 class="font-display zin-section-title mt-3">Reconocimientos</h3>
                </div>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-1 g-4">
                    <div v-for="milestone in milestones" :key="milestone.year + milestone.title" class="col">
                        <div class="zin-card p-4 h-100 d-flex flex-row gap-4 align-items-center">
                            <div class="flex-grow-1 d-flex flex-column gap-2">
                                <p class="zin-highlight small mb-0">{{ milestone.year }}</p>
                                <h4 class="font-display fs-5 mb-0">{{ milestone.title }}</h4>
                                <p class="zin-muted mb-0">{{ milestone.copy }}</p>
                            </div>
                            <div v-if="milestone.image" class="flex-shrink-0">
                                <img :src="milestone.image" :alt="milestone.title" class="rounded" style="max-width: 220px; max-height: 220px; object-fit: cover;" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="salePoints.length > 0" id="donde-encontrarnos" class="mt-5 pb-5">
                <div class="text-center mb-5">
                    <p class="zin-highlight small">Puntos de venta</p>
                    <h2 class="font-display zin-section-title mt-3">Dónde encontrarnos</h2>
                    <p class="zin-muted mt-3 mx-auto" style="max-width: 600px;">
                        Descubrí todos los lugares donde podés conseguir nuestros productos
                    </p>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <article v-for="salePoint in salePoints" :key="salePoint.id" class="col">
                        <div class="zin-card p-4 h-100 d-flex flex-column">
                            <div v-if="salePoint.logo" class="mb-3 text-center">
                                <img :src="salePoint.logo" :alt="salePoint.name" class="img-fluid"
                                    style="max-height: 80px; max-width: 100%;" />
                            </div>
                            <h3 class="font-display fs-4">{{ salePoint.name }}</h3>

                            <div class="mt-3 flex-grow-1">
                                <div v-if="salePoint.address || salePoint.locality || salePoint.province" class="mb-2">
                                    <small class="text-white-50 d-block">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <span v-if="salePoint.address">{{ salePoint.address }} - </span>
                                        <span v-if="salePoint.locality"> {{ salePoint.locality }}</span>
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
                                        <span v-for="category in salePoint.categories" :key="category"
                                            class="zin-badge">
                                            {{ category }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-3 pt-3 border-top border-secondary align-items-center">
                                <a v-if="salePoint.instagram" :href="salePoint.instagram" target="_blank"
                                    class="text-white-50 fs-5" title="Instagram">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a v-if="salePoint.facebook" :href="salePoint.facebook" target="_blank"
                                    class="text-white-50 fs-5" title="Facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a v-if="salePoint.webpage" :href="salePoint.webpage" target="_blank"
                                    class="text-white-50 fs-5" title="Sitio web">
                                    <i class="bi bi-globe"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section id="contacto" class="mt-5 pb-5">
                <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8 mx-auto">
                    <div class="zin-card p-4 p-lg-5">
                        <h2 class="font-display zin-section-title mb-4">Contactate con nosotros</h2>
                        <div v-if="contactSuccess" class="alert alert-success border-0 mb-4">
                            ¡Mensaje enviado! Nos pondremos en contacto pronto.
                        </div>
                        <form class="zin-form" @submit.prevent="submitContact">
                            <div class="mb-4">
                                <label class="zin-badge">Nombre</label>
                                <input v-model="contactForm.name" type="text" class="form-control mt-2" placeholder="Tu nombre" />
                                <div v-if="contactForm.errors.name" class="text-danger small mt-1">{{ contactForm.errors.name }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="zin-badge">Email</label>
                                <input v-model="contactForm.email" type="email" class="form-control mt-2" placeholder="tunombre@ejemplo.com" />
                                <div v-if="contactForm.errors.email" class="text-danger small mt-1">{{ contactForm.errors.email }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="zin-badge">Mensaje</label>
                                <textarea v-model="contactForm.message" rows="4" class="form-control mt-2"
                                    placeholder="Contanos que queres probar"></textarea>
                                <div v-if="contactForm.errors.message" class="text-danger small mt-1">{{ contactForm.errors.message }}</div>
                            </div>
                            <button type="submit" class="btn btn-zin zin-pill w-100 py-3" :disabled="contactForm.processing">
                                <span v-if="contactForm.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Enviar consulta
                            </button>
                        </form>
                    </div>
                </div>
                </div>
            </section>
        </main>

        <footer class="zin-divider py-4 text-center tracking-wide small text-white-50">
            <a href="https://www.instagram.com/zingin0.0" target="_blank" class="text-white-50 text-decoration-none">
                <i class="bi bi-instagram h4 me-2"></i>
            </a>
        </footer>
    </div>
</template>

<style scoped>
.zin-hero-carousel {
    background:
        radial-gradient(circle at top, rgba(90, 125, 99, 0.15), transparent 50%),
        linear-gradient(170deg, rgba(14, 20, 18, 0.97), rgba(10, 14, 12, 0.97));
}

.zin-hero-media {
    position: relative;
    width: 100%;
    padding-top: 68%;
}

.zin-hero-media>img {
    position: absolute;
    inset: 0;
}

.zin-carousel-nav {
    position: absolute;
    top: 42%;
    transform: translateY(-50%);
    width: 2.75rem;
    height: 2.75rem;
    border: 0;
    border-radius: 999px;
    color: #fff;
    background: rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(8px);
}

.zin-carousel-nav-prev {
    left: 1.15rem;
}

.zin-carousel-nav-next {
    right: 1.15rem;
}

.zin-carousel-dot {
    width: 28px;
    height: 8px;
    background-color: rgba(255, 255, 255, 0.28);
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.zin-carousel-dot.is-active,
.zin-carousel-dot:hover {
    background-color: rgba(213, 233, 220, 0.92);
    transform: translateY(-1px);
}

@media (max-width: 767.98px) {
    .zin-hero-media {
        padding-top: 84%;
    }

    .zin-carousel-nav {
        width: 2.2rem;
        height: 2.2rem;
    }

    .zin-carousel-nav-prev {
        left: 0.65rem;
    }

    .zin-carousel-nav-next {
        right: 0.65rem;
    }
}
</style>
