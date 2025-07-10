<template>
    <div class="doctype-list">
        <div class="mb-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-foreground">DocTypes</h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Manage your dynamic document types and their field structures.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <button @click="createNew" type="button"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 sm:w-auto">
                        <PlusIcon class="h-4 w-4 mr-2" />
                        Create DocType
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 bg-card p-4 rounded-lg shadow-sm border border-border">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-foreground mb-1">
                        Search
                    </label>
                    <input id="search" type="text" v-model="filters.search" placeholder="Search doctypes..."
                        class="block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @input="debouncedFetch" />
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-foreground mb-1">
                        Status
                    </label>
                    <select id="status" v-model="filters.active"
                        class="block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @change="fetchDoctypes">
                        <option value="">All Status</option>
                        <option :value="true">Active</option>
                        <option :value="false">Inactive</option>
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-foreground mb-1">
                        Type
                    </label>
                    <select id="type" v-model="filters.system"
                        class="block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @change="fetchDoctypes">
                        <option value="">All Types</option>
                        <option :value="false">Custom</option>
                        <option :value="true">System</option>
                    </select>
                </div>

                <div>
                    <label for="per_page" class="block text-sm font-medium text-foreground mb-1">
                        Per Page
                    </label>
                    <select id="per_page" v-model="filters.per_page"
                        class="block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @change="fetchDoctypes">
                        <option :value="10">10</option>
                        <option :value="15">15</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <p class="mt-2 text-sm text-muted-foreground">Loading doctypes...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="hasError" class="bg-destructive/10 border border-destructive rounded-md p-4">
            <div class="flex">
                <ExclamationCircleIcon class="h-5 w-5 text-destructive" />
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-destructive">Error</h3>
                    <p class="mt-1 text-sm text-destructive">{{ error }}</p>
                    <button @click="fetchDoctypes" class="mt-2 text-sm text-destructive hover:text-destructive/80 underline">
                        Try again
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="isEmpty" class="text-center py-12">
            <DocumentIcon class="mx-auto h-12 w-12 text-muted-foreground" />
            <h3 class="mt-2 text-sm font-medium text-foreground">No doctypes found</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Get started by creating your first doctype.
            </p>
            </p>
            <div class="mt-6">
                <button @click="createNew" type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Create DocType
                </button>
            </div>
        </div>

        <!-- DocTypes Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="doctype in doctypes" :key="doctype.id"
                class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-semibold"
                                :style="{ backgroundColor: doctype.color || '#6b7280' }">
                                {{ doctype.icon || getInitials(doctype.label) }}
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ doctype.label }}
                                </h3>
                                <p class="text-sm text-gray-500">{{ doctype.name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span v-if="doctype.is_system"
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                System
                            </span>
                            <span :class="[
                                'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                doctype.is_active
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-gray-100 text-gray-800'
                            ]">
                                {{ doctype.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <p v-if="doctype.description" class="text-sm text-gray-600 mb-4">
                        {{ doctype.description }}
                    </p>

                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <span>{{ doctype.fields_count || 0 }} fields</span>
                        <span>{{ formatDate(doctype.created_at) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <button @click="viewDoctype(doctype)"
                            class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                            View Details
                        </button>
                        <div class="flex items-center space-x-2">
                            <button @click="editDoctype(doctype)" class="text-gray-600 hover:text-gray-500"
                                title="Edit">
                                <PencilIcon class="h-4 w-4" />
                            </button>
                            <button @click="generateForm(doctype)" class="text-blue-600 hover:text-blue-500"
                                title="Generate Form">
                                <DocumentDuplicateIcon class="h-4 w-4" />
                            </button>
                            <button v-if="!doctype.is_system" @click="confirmDelete(doctype)"
                                class="text-red-600 hover:text-red-500" title="Delete">
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="mt-8 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ (currentPage - 1) * filters.per_page + 1 }} to
                {{ Math.min(currentPage * filters.per_page, totalItems) }} of {{ totalItems }} results
            </div>
            <div class="flex items-center space-x-2">
                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                    class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <span class="text-sm text-gray-700">
                    Page {{ currentPage }} of {{ totalPages }}
                </span>
                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                    class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <ExclamationTriangleIcon class="w-16 h-16 text-red-600 mx-auto" />
                    <h3 class="text-lg font-medium text-gray-900 mt-5">Delete DocType</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Are you sure you want to delete "{{ doctypeToDelete?.label }}"? This action cannot be undone.
                    </p>
                    <div class="items-center px-4 py-3 mt-5">
                        <button @click="deleteDoctype"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete
                        </button>
                        <button @click="cancelDelete"
                            class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-3">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useDoctypes } from '../services/useDoctypes';
    import type { Doctype, DoctypeFilters } from '../types/doctype';
    import {
        PlusIcon,
        PencilIcon,
        TrashIcon,
        DocumentIcon,
        DocumentDuplicateIcon,
        ExclamationCircleIcon,
        ExclamationTriangleIcon,
    } from '@heroicons/vue/24/outline';

    interface Emits {
        (event: 'create'): void;
        (event: 'edit', doctype: Doctype): void;
        (event: 'view', doctype: Doctype): void;
        (event: 'generateForm', doctype: Doctype): void;
    }

    const emit = defineEmits<Emits>();

    const {
        doctypes,
        loading,
        error,
        hasError,
        isEmpty,
        totalPages,
        currentPage,
        totalItems,
        fetchDoctypes,
        deleteDoctype: deleteDoctypeApi,
    } = useDoctypes();

    const filters = ref<DoctypeFilters>({
        search: '',
        active: '',
        system: '',
        per_page: 15,
        page: 1,
    });

    const showDeleteModal = ref(false);
    const doctypeToDelete = ref<Doctype | null>(null);

    // Debounced search
    let searchTimeout: ReturnType<typeof setTimeout>;
    const debouncedFetch = () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filters.value.page = 1;
            fetchDoctypes(filters.value);
        }, 300);
    };

    const createNew = () => {
        emit('create');
    };

    const editDoctype = (doctype: Doctype) => {
        emit('edit', doctype);
    };

    const viewDoctype = (doctype: Doctype) => {
        emit('view', doctype);
    };

    const generateForm = (doctype: Doctype) => {
        emit('generateForm', doctype);
    };

    const confirmDelete = (doctype: Doctype) => {
        doctypeToDelete.value = doctype;
        showDeleteModal.value = true;
    };

    const deleteDoctype = async () => {
        if (!doctypeToDelete.value?.id) return;

        try {
            await deleteDoctypeApi(doctypeToDelete.value.id);
            showDeleteModal.value = false;
            doctypeToDelete.value = null;
            // Refresh the list
            await fetchDoctypes(filters.value);
        } catch (error) {
            console.error('Failed to delete doctype:', error);
        }
    };

    const cancelDelete = () => {
        showDeleteModal.value = false;
        doctypeToDelete.value = null;
    };

    const goToPage = (page: number) => {
        if (page < 1 || page > totalPages.value) return;
        filters.value.page = page;
        fetchDoctypes(filters.value);
    };

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const formatDate = (dateString?: string) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString();
    };

    onMounted(() => {
        fetchDoctypes(filters.value);
    });
</script>

<style scoped>
    .doctype-list {
        padding: 1rem;
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>
