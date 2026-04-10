<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
});

const images = computed(() => props.images ?? []);
const activeImages = computed(() => images.value.filter((image) => image.is_active));
const activeCarouselIndex = ref(0);
const activeCarouselImage = computed(() => activeImages.value[activeCarouselIndex.value] ?? null);
const canSlideActiveCarousel = computed(() => activeImages.value.length > 1);
const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success ?? null);

const showModal = ref(false);
const modalMode = ref('create');
const existingUrl = ref(null);

const createForm = useForm({
    title: '',
    caption: '',
    sort_order: 0,
    is_active: true,
    image: null,
});

const editForm = useForm({
    id: null,
    title: '',
    caption: '',
    sort_order: 0,
    is_active: true,
    image: null,
    _method: 'put',
});

const activeForm = computed(() => (modalMode.value === 'create' ? createForm : editForm));
const modalTitle = computed(() => (modalMode.value === 'create' ? 'Cargar imagen' : 'Editar imagen'));
const modalActionLabel = computed(() => (modalMode.value === 'create' ? 'Guardar imagen' : 'Actualizar imagen'));

const showPreviousActiveImage = () => {
    if (!activeImages.value.length) {
        return;
    }

    activeCarouselIndex.value = (activeCarouselIndex.value - 1 + activeImages.value.length) % activeImages.value.length;
};

const showNextActiveImage = () => {
    if (!activeImages.value.length) {
        return;
    }

    activeCarouselIndex.value = (activeCarouselIndex.value + 1) % activeImages.value.length;
};

const openCreateModal = () => {
    modalMode.value = 'create';
    showModal.value = true;
    existingUrl.value = null;
    createForm.reset();
    createForm.clearErrors();
    createForm.sort_order = 0;
    createForm.is_active = true;
    createForm.image = null;
};

const openEditModal = (image) => {
    modalMode.value = 'edit';
    showModal.value = true;
    existingUrl.value = image.url;
    editForm.reset();
    editForm.clearErrors();
    editForm.id = image.id;
    editForm.title = image.title ?? '';
    editForm.caption = image.caption ?? '';
    editForm.sort_order = image.sort_order ?? 0;
    editForm.is_active = image.is_active;
    editForm.image = null;
};

const closeModal = () => {
    showModal.value = false;
    existingUrl.value = null;
    createForm.reset();
    editForm.reset();
};

const handleImageSelection = (event) => {
    const file = event.target.files?.[0] ?? null;
    activeForm.value.image = file;
    event.target.value = '';
};

const submitCreate = () => {
    createForm.post(route('images.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const submitEdit = () => {
    editForm.post(route('images.update', editForm.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const deleteImage = (image) => {
    if (!confirm(`¿Eliminar imagen ${image.title ? `\"${image.title}\"` : `#${image.id}`}?`)) {
        return;
    }

    router.delete(route('images.destroy', image.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Imágenes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                <h2 class="h4 mb-0 text-uppercase">Imágenes</h2>
                <button type="button" class="btn btn-zin btn-sm text-uppercase" @click="openCreateModal">
                    <i class="bi bi-plus-circle me-1"></i> Cargar imagen
                </button>
            </div>
        </template>

        <div class="py-3">
            <div v-if="flashSuccess" class="alert alert-success border-0 shadow-sm">
                {{ flashSuccess }}
            </div>

            <div v-if="activeCarouselImage" class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h6 mb-0 text-uppercase text-muted">Carrusel activo en Welcome</h3>
                        <small class="text-muted">{{ activeCarouselIndex + 1 }} / {{ activeImages.length }}</small>
                    </div>

                    <div class="position-relative rounded overflow-hidden border">
                        <img
                            :src="activeCarouselImage.url"
                            :alt="activeCarouselImage.title || 'Imagen activa'"
                            class="w-100"
                            style="max-height: 360px; object-fit: cover;"
                        />

                        <button
                            v-if="canSlideActiveCarousel"
                            type="button"
                            class="btn btn-dark btn-sm position-absolute top-50 start-0 translate-middle-y ms-2"
                            aria-label="Anterior"
                            @click="showPreviousActiveImage"
                        >
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button
                            v-if="canSlideActiveCarousel"
                            type="button"
                            class="btn btn-dark btn-sm position-absolute top-50 end-0 translate-middle-y me-2"
                            aria-label="Siguiente"
                            @click="showNextActiveImage"
                        >
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="mt-3">
                        <div class="fw-semibold">{{ activeCarouselImage.title || 'Sin título' }}</div>
                        <small v-if="activeCarouselImage.caption" class="text-muted">{{ activeCarouselImage.caption }}</small>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div v-if="!images.length" class="p-4 text-center text-muted">
                        Todavía no cargaste imágenes.
                    </div>

                    <div v-else class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Vista previa</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="image in images" :key="image.id">
                                    <td style="width: 130px;">
                                        <img :src="image.url" :alt="image.title || `Imagen ${image.id}`" class="rounded border" style="width: 110px; height: 74px; object-fit: cover;" />
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ image.title || 'Sin título' }}</div>
                                        <small class="text-muted">ID: {{ image.id }}</small>
                                    </td>
                                    <td>
                                        <span v-if="image.caption">{{ image.caption }}</span>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                    <td>{{ image.sort_order }}</td>
                                    <td>
                                        <span class="badge" :class="image.is_active ? 'text-bg-success' : 'text-bg-secondary'">
                                            {{ image.is_active ? 'Activa' : 'Oculta' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" @click="openEditModal(image)">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" @click="deleteImage(image)">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-uppercase">{{ modalTitle }}</h5>
                        <button type="button" class="btn btn-outline-secondary btn-sm" @click="closeModal">Cerrar</button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Título</label>
                                <input v-model="activeForm.title" type="text" class="form-control" placeholder="Título opcional" />
                                <div v-if="activeForm.errors.title" class="text-danger small mt-1">{{ activeForm.errors.title }}</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Orden</label>
                                <input v-model="activeForm.sort_order" type="number" min="0" class="form-control" />
                                <div v-if="activeForm.errors.sort_order" class="text-danger small mt-1">{{ activeForm.errors.sort_order }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <input v-model="activeForm.caption" type="text" class="form-control" placeholder="Texto corto opcional" />
                                <div v-if="activeForm.errors.caption" class="text-danger small mt-1">{{ activeForm.errors.caption }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Archivo de imagen</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept="image/*"
                                    @change="handleImageSelection"
                                />
                                <div class="form-text">JPG, PNG o WebP. Máx. 6 MB.</div>
                                <div v-if="activeForm.errors.image" class="text-danger small mt-1">{{ activeForm.errors.image }}</div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input id="is_active" v-model="activeForm.is_active" class="form-check-input" type="checkbox" />
                                    <label class="form-check-label" for="is_active">
                                        Mostrar en carrusel del Welcome
                                    </label>
                                </div>
                            </div>

                            <div v-if="existingUrl" class="col-12">
                                <label class="form-label">Imagen actual</label>
                                <div>
                                    <img :src="existingUrl" alt="Imagen actual" class="img-fluid rounded border" style="max-height: 220px;" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" @click="closeModal">
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="btn btn-zin"
                            :disabled="activeForm.processing"
                            @click="modalMode === 'create' ? submitCreate() : submitEdit()"
                        >
                            <span v-if="activeForm.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{ modalActionLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="modal-backdrop fade show"></div>
    </AuthenticatedLayout>
</template>
