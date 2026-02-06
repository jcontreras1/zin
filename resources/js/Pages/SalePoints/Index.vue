<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    salePoints: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const salePoints = computed(() => props.salePoints ?? []);
const categories = computed(() => props.categories ?? []);

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success ?? null);

const showModal = ref(false);
const modalMode = ref('create');

const createForm = useForm({
    name: '',
    logo: null,
    phone: '',
    instagram: '',
    facebook: '',
    webpage: '',
    address: '',
    locality: '',
    province: '',
    category_ids: [],
});

const editForm = useForm({
    id: null,
    name: '',
    logo: null,
    phone: '',
    instagram: '',
    facebook: '',
    webpage: '',
    address: '',
    locality: '',
    province: '',
    category_ids: [],
    remove_logo: false,
    _method: 'put',
});

const activeForm = computed(() => (modalMode.value === 'create' ? createForm : editForm));
const modalTitle = computed(() => (modalMode.value === 'create' ? 'Crear punto de venta' : 'Editar punto de venta'));
const modalActionLabel = computed(() => (modalMode.value === 'create' ? 'Guardar' : 'Actualizar'));

const existingLogo = ref(null);

const openCreateModal = () => {
    modalMode.value = 'create';
    showModal.value = true;
    existingLogo.value = null;
    createForm.reset();
    createForm.clearErrors();
    createForm.logo = null;
    createForm.category_ids = [];
};

const openEditModal = (salePoint) => {
    modalMode.value = 'edit';
    showModal.value = true;
    existingLogo.value = salePoint.logo;
    editForm.reset();
    editForm.clearErrors();
    editForm.id = salePoint.id;
    editForm.name = salePoint.name;
    editForm.phone = salePoint.phone ?? '';
    editForm.instagram = salePoint.instagram ?? '';
    editForm.facebook = salePoint.facebook ?? '';
    editForm.webpage = salePoint.webpage ?? '';
    editForm.address = salePoint.address ?? '';
    editForm.locality = salePoint.locality ?? '';
    editForm.province = salePoint.province ?? '';
    editForm.category_ids = salePoint.categories.map(c => c.id);
    editForm.logo = null;
    editForm.remove_logo = false;
};

const closeModal = () => {
    showModal.value = false;
    createForm.reset();
    editForm.reset();
    existingLogo.value = null;
};

const handleLogoSelection = (event) => {
    const file = event.target.files?.[0];
    if (file) {
        activeForm.value.logo = file;
    }
};

const removeLogo = () => {
    if (modalMode.value === 'edit') {
        editForm.remove_logo = true;
        existingLogo.value = null;
    }
    activeForm.value.logo = null;
};

const submitCreate = () => {
    createForm.post(route('sale-points.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const submitEdit = () => {
    editForm.post(route('sale-points.update', editForm.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        submitCreate();
    } else {
        submitEdit();
    }
};

const confirmDelete = ref(false);
const salePointToDelete = ref(null);

const openDeleteModal = (salePoint) => {
    salePointToDelete.value = salePoint;
    confirmDelete.value = true;
};

const deleteSalePoint = () => {
    if (!salePointToDelete.value) return;

    router.delete(route('sale-points.destroy', salePointToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            confirmDelete.value = false;
            salePointToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    confirmDelete.value = false;
    salePointToDelete.value = null;
};
</script>

<template>
    <Head title="Puntos de Venta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0 fw-semibold">Puntos de Venta</h2>
                <button @click="openCreateModal" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Crear punto de venta
                </button>
            </div>
        </template>

        <div v-if="flashSuccess" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ flashSuccess }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="card">
            <div class="card-body">
                <div v-if="salePoints.length === 0" class="text-center text-muted py-5">
                    <p class="mb-0">No hay puntos de venta registrados.</p>
                </div>

                <div v-else class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <div v-for="salePoint in salePoints" :key="salePoint.id" class="col">
                        <div class="card h-100">
                            <div v-if="salePoint.logo" class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                <img :src="salePoint.logo" :alt="salePoint.name" class="img-fluid" style="max-height: 100px; max-width: 100%;" />
                            </div>
                            <div v-else class="card-img-top bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 120px;">
                                <span class="text-muted">Sin logo</span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ salePoint.name }}</h5>
                                
                                <div class="mt-3">
                                    <div v-if="salePoint.address || salePoint.locality || salePoint.province" class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="bi bi-geo-alt"></i>
                                            <span v-if="salePoint.address">{{ salePoint.address }}</span>
                                            <span v-if="salePoint.locality">, {{ salePoint.locality }}</span>
                                            <span v-if="salePoint.province"> - {{ salePoint.province }}</span>
                                        </small>
                                    </div>
                                    
                                    <div v-if="salePoint.phone" class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-telephone"></i> {{ salePoint.phone }}
                                        </small>
                                    </div>

                                    <div class="d-flex gap-2 mt-2">
                                        <a v-if="salePoint.instagram" :href="salePoint.instagram" target="_blank" class="btn btn-sm btn-outline-secondary" title="Instagram">
                                            <i class="bi bi-instagram"></i>
                                        </a>
                                        <a v-if="salePoint.facebook" :href="salePoint.facebook" target="_blank" class="btn btn-sm btn-outline-secondary" title="Facebook">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                        <a v-if="salePoint.webpage" :href="salePoint.webpage" target="_blank" class="btn btn-sm btn-outline-secondary" title="Sitio web">
                                            <i class="bi bi-globe"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">Categorías disponibles:</small>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        <span v-for="category in salePoint.categories" :key="category.id" class="badge bg-secondary">
                                            {{ category.name }}
                                        </span>
                                        <span v-if="salePoint.categories.length === 0" class="text-muted fst-italic small">
                                            Sin categorías
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group btn-group-sm w-100" role="group">
                                    <button @click="openEditModal(salePoint)" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                    <button @click="openDeleteModal(salePoint)" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para crear/editar punto de venta -->
        <div v-if="showModal" class="modal d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ modalTitle }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.name }"
                                        id="name"
                                        v-model="activeForm.name"
                                        required
                                    />
                                    <div v-if="activeForm.errors.name" class="invalid-feedback">
                                        {{ activeForm.errors.name }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="logo" class="form-label">Logo</label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.logo }"
                                        id="logo"
                                        accept="image/*"
                                        @change="handleLogoSelection"
                                    />
                                    <div v-if="activeForm.errors.logo" class="invalid-feedback">
                                        {{ activeForm.errors.logo }}
                                    </div>
                                    <div v-if="existingLogo && !activeForm.remove_logo" class="mt-2">
                                        <img :src="existingLogo" alt="Logo actual" class="img-thumbnail" style="max-height: 100px;" />
                                        <button type="button" class="btn btn-sm btn-danger ms-2" @click="removeLogo">
                                            Eliminar logo
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.phone }"
                                        id="phone"
                                        v-model="activeForm.phone"
                                    />
                                    <div v-if="activeForm.errors.phone" class="invalid-feedback">
                                        {{ activeForm.errors.phone }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.instagram }"
                                        id="instagram"
                                        v-model="activeForm.instagram"
                                        placeholder="https://instagram.com/..."
                                    />
                                    <div v-if="activeForm.errors.instagram" class="invalid-feedback">
                                        {{ activeForm.errors.instagram }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="facebook" class="form-label">Facebook</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.facebook }"
                                        id="facebook"
                                        v-model="activeForm.facebook"
                                        placeholder="https://facebook.com/..."
                                    />
                                    <div v-if="activeForm.errors.facebook" class="invalid-feedback">
                                        {{ activeForm.errors.facebook }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="webpage" class="form-label">Sitio Web</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.webpage }"
                                        id="webpage"
                                        v-model="activeForm.webpage"
                                        placeholder="https://..."
                                    />
                                    <div v-if="activeForm.errors.webpage" class="invalid-feedback">
                                        {{ activeForm.errors.webpage }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Dirección</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.address }"
                                        id="address"
                                        v-model="activeForm.address"
                                    />
                                    <div v-if="activeForm.errors.address" class="invalid-feedback">
                                        {{ activeForm.errors.address }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="locality" class="form-label">Localidad</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.locality }"
                                        id="locality"
                                        v-model="activeForm.locality"
                                    />
                                    <div v-if="activeForm.errors.locality" class="invalid-feedback">
                                        {{ activeForm.errors.locality }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="province" class="form-label">Provincia</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': activeForm.errors.province }"
                                        id="province"
                                        v-model="activeForm.province"
                                    />
                                    <div v-if="activeForm.errors.province" class="invalid-feedback">
                                        {{ activeForm.errors.province }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Categorías disponibles</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        <div v-for="category in categories" :key="category.id" class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                :id="`category-${category.id}`"
                                                :value="category.id"
                                                v-model="activeForm.category_ids"
                                            />
                                            <label class="form-check-label" :for="`category-${category.id}`">
                                                {{ category.name }}
                                            </label>
                                        </div>
                                        <div v-if="categories.length === 0" class="text-muted fst-italic">
                                            No hay categorías disponibles. Crea categorías primero.
                                        </div>
                                    </div>
                                    <div v-if="activeForm.errors.category_ids" class="text-danger small mt-1">
                                        {{ activeForm.errors.category_ids }}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal" :disabled="activeForm.processing">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" @click="submitForm" :disabled="activeForm.processing">
                            <span v-if="activeForm.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{ modalActionLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación de eliminación -->
        <div v-if="confirmDelete" class="modal d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar eliminación</h5>
                        <button type="button" class="btn-close" @click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro que desea eliminar el punto de venta <strong>{{ salePointToDelete?.name }}</strong>?</p>
                        <p class="text-warning mb-0">
                            <small>Esta acción no se puede deshacer.</small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cancelDelete">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="deleteSalePoint">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
