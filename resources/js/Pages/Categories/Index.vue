<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const categories = computed(() => props.categories ?? []);

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success ?? null);

const showModal = ref(false);
const modalMode = ref('create');

const createForm = useForm({
    name: '',
    description: '',
});

const editForm = useForm({
    id: null,
    name: '',
    description: '',
});

const activeForm = computed(() => (modalMode.value === 'create' ? createForm : editForm));
const modalTitle = computed(() => (modalMode.value === 'create' ? 'Crear categoría' : 'Editar categoría'));
const modalActionLabel = computed(() => (modalMode.value === 'create' ? 'Guardar categoría' : 'Actualizar categoría'));

const openCreateModal = () => {
    modalMode.value = 'create';
    showModal.value = true;
    createForm.reset();
    createForm.clearErrors();
};

const openEditModal = (category) => {
    modalMode.value = 'edit';
    showModal.value = true;
    editForm.reset();
    editForm.clearErrors();
    editForm.id = category.id;
    editForm.name = category.name;
    editForm.description = category.description ?? '';
};

const closeModal = () => {
    showModal.value = false;
    createForm.reset();
    editForm.reset();
};

const submitCreate = () => {
    createForm.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

const submitEdit = () => {
    editForm.put(route('categories.update', editForm.id), {
        preserveScroll: true,
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
const categoryToDelete = ref(null);

const openDeleteModal = (category) => {
    categoryToDelete.value = category;
    confirmDelete.value = true;
};

const deleteCategory = () => {
    if (!categoryToDelete.value) return;

    router.delete(route('categories.destroy', categoryToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            confirmDelete.value = false;
            categoryToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    confirmDelete.value = false;
    categoryToDelete.value = null;
};
</script>

<template>
    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0 fw-semibold">Categorías</h2>
                <button @click="openCreateModal" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Crear categoría
                </button>
            </div>
        </template>

        <div v-if="flashSuccess" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ flashSuccess }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="card">
            <div class="card-body">
                <div v-if="categories.length === 0" class="text-center text-muted py-5">
                    <p class="mb-0">No hay categorías registradas.</p>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="text-center">Productos</th>
                                <th>Creación</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in categories" :key="category.id">
                                <td class="fw-semibold">{{ category.name }}</td>
                                <td>
                                    <span v-if="category.description" class="text-muted">
                                        {{ category.description.length > 50 ? category.description.substring(0, 50) + '...' : category.description }}
                                    </span>
                                    <span v-else class="text-muted fst-italic">Sin descripción</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ category.products_count }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ new Date(category.created_at).toLocaleDateString() }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button @click="openEditModal(category)" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button @click="openDeleteModal(category)" class="btn btn-outline-danger">
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

        <!-- Modal para crear/editar categoría -->
        <div v-if="showModal" class="modal d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ modalTitle }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitForm">
                            <div class="mb-3">
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

                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea
                                    class="form-control"
                                    :class="{ 'is-invalid': activeForm.errors.description }"
                                    id="description"
                                    v-model="activeForm.description"
                                    rows="3"
                                ></textarea>
                                <div v-if="activeForm.errors.description" class="invalid-feedback">
                                    {{ activeForm.errors.description }}
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
                        <p>¿Está seguro que desea eliminar la categoría <strong>{{ categoryToDelete?.name }}</strong>?</p>
                        <p v-if="categoryToDelete?.products_count > 0" class="text-warning mb-0">
                            <small>Esta categoría tiene {{ categoryToDelete.products_count }} producto(s) asociado(s). Al eliminarla, los productos quedarán sin categoría.</small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cancelDelete">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="deleteCategory">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
