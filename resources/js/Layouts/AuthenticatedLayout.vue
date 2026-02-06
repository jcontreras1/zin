<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-vh-100 bg-body-secondary d-flex flex-column">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark border-bottom border-dark-subtle sticky-top">
            <div class="container py-2">
                <Link href="/" class="navbar-brand d-flex align-items-center gap-2 text-uppercase small">
                    <ApplicationLogo class="text-white" style="width: 32px; height: 32px;" />
                    {{ $page.props.appName }} Home
                </Link>

                <button class="navbar-toggler" type="button" @click="showingNavigationDropdown = !showingNavigationDropdown">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div :class="['collapse', 'navbar-collapse', { show: showingNavigationDropdown }]">
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

        <header v-if="$slots.header" class="bg-body py-4 border-bottom border-light-subtle">
            <div class="container">
                <slot name="header" />
            </div>
        </header>

        <main class="flex-grow-1 py-4">
            <div class="container">
                <slot />
            </div>
        </main>
    </div>
</template>
