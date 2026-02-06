<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const products = computed(() => props.products ?? []);
const categories = computed(() => props.categories ?? []);

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success ?? null);

const showModal = ref(false);
const modalMode = ref('create');
const existingPhotos = ref([]);

const createForm = useForm({
    category_id: null,
    title: '',
    description_one: '',
    description_two: '',
    price: '',
    photos: [],
});

const editForm = useForm({
    id: null,
    category_id: null,
    title: '',
    description_one: '',
    description_two: '',
    price: '',
    photos: [],
    removed_photos: [],
    _method: 'put',
});

const activeForm = computed(() => (modalMode.value === 'create' ? createForm : editForm));
const modalTitle = computed(() => (modalMode.value === 'create' ? 'Cargar producto' : 'Editar producto'));
const modalActionLabel = computed(() => (modalMode.value === 'create' ? 'Guardar producto' : 'Actualizar producto'));

const openCreateModal = () => {
    modalMode.value = 'create';
    showModal.value = true;
    existingPhotos.value = [];
    createForm.reset();
    createForm.clearErrors();
    createForm.photos = [];
};

const openEditModal = (product) => {
    modalMode.value = 'edit';
    showModal.value = true;
    existingPhotos.value = product.photos.map((photo) => ({ ...photo, marked: false }));
    editForm.reset();
    editForm.clearErrors();
    editForm.id = product.id;
    editForm.category_id = product.category_id;
    editForm.title = product.title;
    editForm.description_one = product.description_one;
    editForm.description_two = product.description_two ?? '';
    editForm.price = product.price;
    editForm.photos = [];
    editForm.removed_photos = [];
};

const closeModal = () => {
    showModal.value = false;
    createForm.reset();
    editForm.reset();
    createForm.photos = [];
    editForm.photos = [];
    existingPhotos.value = [];
};

const handleFileSelection = (event, form) => {
    form.photos = Array.from(event.target.files || []);
    event.target.value = '';
};

const submitCreate = () => {
    createForm.post(route('products.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const submitEdit = () => {
    editForm.removed_photos = existingPhotos.value
        .filter((photo) => photo.marked)
        .map((photo) => photo.id);

    editForm.post(route('products.update', editForm.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const toggleExistingPhoto = (photo) => {
    photo.marked = !photo.marked;
};

const deleteProduct = (product) => {
    if (!confirm(`¿Eliminar "${product.title}"?`)) {
        return;
    }

    router.delete(route('products.destroy', product.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Productos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                <h2 class="h4 mb-0 text-uppercase">Productos</h2>
                <button type="button" class="btn btn-zin btn-sm text-uppercase" @click="openCreateModal">
                    <i class="bi bi-plus-circle me-1"></i> Cargar producto
                </button>
            </div>
        </template>

        <div class="py-3">
            <div v-if="flashSuccess" class="alert alert-success border-0 shadow-sm">
                {{ flashSuccess }}
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div v-if="!products.length" class="p-4 text-center text-muted">
                        Todavía no cargaste productos.
                    </div>

                    <div v-else class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th width="28%">Descripción 1</th>
                                    <th>Descripción 2</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-center">Fotos</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in products" :key="product.id">
                                    <td>
                                        <div class="fw-semibold">{{ product.title }}</div>
                                        <small class="text-muted">Actualizado: {{ product.created_at }}</small>
                                    </td>
                                    <td>
                                        <span v-if="product.category_name" class="badge bg-secondary">{{ product.category_name }}</span>
                                        <span v-else class="text-muted fst-italic">Sin categoría</span>
                                    </td>
                                    <td>{{ product.description_one }}</td>
                                    <td>
                                        <span v-if="product.description_two">{{ product.description_two }}</span>
                                        <span v-else class="text-muted">—</span>
                                    </td>
                                    <td class="text-end fw-semibold">${{ product.price_formatted }}</td>
                                    <td class="text-center">
                                        <span class="badge text-bg-dark">{{ product.photos.length }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" @click="openEditModal(product)">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" @click="deleteProduct(product)">
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
                            <div class="col-12">
                                <label class="form-label">Categoría</label>
                                <select v-model="activeForm.category_id" class="form-select">
                                    <option :value="null">Sin categoría</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <div v-if="activeForm.errors.category_id" class="text-danger small mt-1">
                                    {{ activeForm.errors.category_id }}
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Título</label>
                                <input v-model="activeForm.title" type="text" class="form-control" placeholder="Título del producto" />
                                <div v-if="activeForm.errors.title" class="text-danger small mt-1">{{ activeForm.errors.title }}</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Descripción 1</label>
                                <textarea
                                    v-model="activeForm.description_one"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Detalle principal"
                                ></textarea>
                                <div v-if="activeForm.errors.description_one" class="text-danger small mt-1">
                                    {{ activeForm.errors.description_one }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Descripción 2</label>
                                <textarea
                                    v-model="activeForm.description_two"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Detalle secundario"
                                ></textarea>
                                <div v-if="activeForm.errors.description_two" class="text-danger small mt-1">
                                    {{ activeForm.errors.description_two }}
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">Precio</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input v-model="activeForm.price" type="number" step="0.01" min="0" class="form-control" />
                                </div>
                                <div v-if="activeForm.errors.price" class="text-danger small mt-1">{{ activeForm.errors.price }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Fotos del producto</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept="image/*"
                                    multiple
                                    @change="handleFileSelection($event, activeForm)"
                                />
                                <div class="form-text">Podés subir varias fotos en formato JPG, PNG o WebP (máx. 6 MB c/u).</div>
                                <div v-if="activeForm.errors.photos" class="text-danger small mt-1">{{ activeForm.errors.photos }}</div>

                                <ul v-if="activeForm.photos.length" class="list-group list-group-flush mt-2">
                                    <li v-for="file in activeForm.photos" :key="file.name" class="list-group-item px-0">
                                        {{ file.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div v-if="modalMode === 'edit' && existingPhotos.length" class="mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="text-uppercase text-muted mb-0">Fotos actuales</h6>
                                <small class="text-muted">Tocá una foto para marcarla para eliminar</small>
                            </div>
                            <div class="row g-3">
                                <div v-for="photo in existingPhotos" :key="photo.id" class="col-6 col-md-3">
                                    <div
                                        class="position-relative rounded overflow-hidden border"
                                        :class="photo.marked ? 'border-danger' : 'border-light'"
                                    >
                                        <img :src="photo.url" class="img-fluid w-100" alt="Foto de producto" />
                                        <button
                                            type="button"
                                            class="btn btn-sm position-absolute top-0 end-0 m-2"
                                            :class="photo.marked ? 'btn-danger' : 'btn-outline-light'"
                                            @click="toggleExistingPhoto(photo)"
                                        >
                                            {{ photo.marked ? 'Eliminar' : 'Conservar' }}
                                        </button>
                                    </div>
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
