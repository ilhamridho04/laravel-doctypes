<template>
    <div class="doctype-list max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">DocTypes</h1>
                    <p class="text-gray-600 mt-2">Manage your document types and schemas</p>
                </div>
                <button @click="$emit('create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors inline-flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create DocType
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6 bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Search DocTypes
                    </label>
                    <input id="search" v-model="searchQuery" type="text" placeholder="Search by name or description..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status" v-model="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="clearFilters"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading DocTypes...</span>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredDoctypes.length === 0 && !loading" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No DocTypes found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first DocType.</p>
            <div class="mt-6">
                <button @click="$emit('create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors inline-flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Your First DocType
                </button>
            </div>
        </div>

        <!-- DocTypes Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="doctype in filteredDoctypes" :key="doctype.id"
                class="bg-white shadow rounded-lg hover:shadow-lg transition-shadow cursor-pointer"
                @click="$emit('select', doctype)">
                <div class="p-6">
                    <!-- DocType Header -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-medium text-gray-900 truncate">
                                {{ doctype.label || doctype.name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ doctype.name }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <span :class="getStatusColor(doctype.is_active)"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                {{ doctype.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <p v-if="doctype.description" class="text-gray-600 text-sm mt-3 line-clamp-2">
                        {{ doctype.description }}
                    </p>

                    <!-- Stats -->
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-lg font-medium text-gray-900">
                                {{ doctype.fields_count || 0 }}
                            </div>
                            <div class="text-xs text-gray-500">Fields</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-medium text-gray-900">
                                {{ doctype.documents_count || 0 }}
                            </div>
                            <div class="text-xs text-gray-500">Documents</div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Created {{ formatDate(doctype.created_at) }}</span>
                            <span>Updated {{ formatDate(doctype.updated_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-3 rounded-b-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2">
                            <button @click.stop="$emit('edit', doctype)"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                                Edit
                            </button>
                            <span class="text-gray-300">•</span>
                            <button @click.stop="$emit('view-documents', doctype)"
                                class="text-green-600 hover:text-green-800 text-sm font-medium transition-colors">
                                Documents
                            </button>
                            <span class="text-gray-300">•</span>
                            <button @click.stop="$emit('generate-form', doctype)"
                                class="text-purple-600 hover:text-purple-800 text-sm font-medium transition-colors">
                                Form
                            </button>
                        </div>
                        <div class="flex space-x-1">
                            <button @click.stop="toggleStatus(doctype)"
                                :class="doctype.is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'"
                                class="text-sm font-medium transition-colors">
                                {{ doctype.is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <span class="text-gray-300">•</span>
                            <button @click.stop="confirmDelete(doctype)"
                                class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.total > pagination.per_page" class="mt-8 flex justify-center">
            <nav class="flex items-center space-x-2">
                <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1"
                    class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>

                <template v-for="page in visiblePages" :key="page">
                    <button v-if="page !== '...'" @click="changePage(page)"
                        :class="page === pagination.current_page ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        {{ page }}
                    </button>
                    <span v-else class="px-3 py-2 text-sm text-gray-500">...</span>
                </template>

                <button @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page >= pagination.last_page"
                    class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next
                </button>
            </nav>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75" @click="cancelDelete"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Delete DocType
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete "{{ deleteCandidate?.label ||
                                        deleteCandidate?.name }}"?
                                        This action cannot be undone and will also delete all associated documents.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="deleteDoctype" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Delete
                        </button>
                        <button @click="cancelDelete" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';

    const emit = defineEmits(['create', 'edit', 'select', 'view-documents', 'generate-form']);

    // State
    const loading = ref(false);
    const searchQuery = ref('');
    const statusFilter = ref('');
    const showDeleteModal = ref(false);
    const deleteCandidate = ref(null);

    const doctypes = ref([
        {
            id: 1,
            name: 'customer',
            label: 'Customer',
            description: 'Manage customer information and contact details',
            is_active: true,
            fields_count: 8,
            documents_count: 125,
            created_at: '2024-01-15T10:30:00Z',
            updated_at: '2024-01-20T14:45:00Z'
        },
        {
            id: 2,
            name: 'product',
            label: 'Product',
            description: 'Product catalog with specifications and pricing',
            is_active: true,
            fields_count: 12,
            documents_count: 89,
            created_at: '2024-01-18T09:15:00Z',
            updated_at: '2024-01-22T11:20:00Z'
        },
        {
            id: 3,
            name: 'order',
            label: 'Sales Order',
            description: 'Track sales orders and customer purchases',
            is_active: false,
            fields_count: 15,
            documents_count: 234,
            created_at: '2024-01-10T08:00:00Z',
            updated_at: '2024-01-25T16:30:00Z'
        }
    ]);

    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 3
    });

    // Computed
    const filteredDoctypes = computed(() => {
        let filtered = doctypes.value;

        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            filtered = filtered.filter(doctype =>
                doctype.name.toLowerCase().includes(query) ||
                doctype.label.toLowerCase().includes(query) ||
                (doctype.description && doctype.description.toLowerCase().includes(query))
            );
        }

        if (statusFilter.value) {
            filtered = filtered.filter(doctype =>
                statusFilter.value === 'active' ? doctype.is_active : !doctype.is_active
            );
        }

        return filtered;
    });

    const visiblePages = computed(() => {
        const pages = [];
        const current = pagination.value.current_page;
        const last = pagination.value.last_page;

        // Always show first page
        if (last > 1) pages.push(1);

        // Show ellipsis if needed
        if (current > 3) pages.push('...');

        // Show current page and neighbors
        for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) {
            if (i !== 1 && i !== last) pages.push(i);
        }

        // Show ellipsis if needed
        if (current < last - 2) pages.push('...');

        // Always show last page
        if (last > 1) pages.push(last);

        return pages;
    });

    // Methods
    const getStatusColor = (isActive) => {
        return isActive
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';
    };

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';

        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 1) return 'yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
        if (diffDays < 365) return `${Math.ceil(diffDays / 30)} months ago`;

        return date.toLocaleDateString();
    };

    const clearFilters = () => {
        searchQuery.value = '';
        statusFilter.value = '';
    };

    const changePage = (page) => {
        if (page >= 1 && page <= pagination.value.last_page) {
            pagination.value.current_page = page;
            // In a real app, this would trigger a data fetch
            loadDoctypes();
        }
    };

    const toggleStatus = async (doctype) => {
        try {
            doctype.is_active = !doctype.is_active;
            // In a real app, this would be an API call
            console.log('Toggling status for:', doctype.name);
        } catch (error) {
            console.error('Error toggling status:', error);
            // Revert on error
            doctype.is_active = !doctype.is_active;
        }
    };

    const confirmDelete = (doctype) => {
        deleteCandidate.value = doctype;
        showDeleteModal.value = true;
    };

    const cancelDelete = () => {
        deleteCandidate.value = null;
        showDeleteModal.value = false;
    };

    const deleteDoctype = async () => {
        if (!deleteCandidate.value) return;

        try {
            // In a real app, this would be an API call
            const index = doctypes.value.findIndex(d => d.id === deleteCandidate.value.id);
            if (index !== -1) {
                doctypes.value.splice(index, 1);
            }

            console.log('Deleted DocType:', deleteCandidate.value.name);
        } catch (error) {
            console.error('Error deleting DocType:', error);
        } finally {
            cancelDelete();
        }
    };

    const loadDoctypes = async () => {
        loading.value = true;
        try {
            // In a real app, this would be an API call
            // const response = await api.get('/doctypes', { params: { page: pagination.value.current_page } });
            // doctypes.value = response.data.data;
            // pagination.value = response.data.meta;

            console.log('Loading DocTypes...');
        } catch (error) {
            console.error('Error loading DocTypes:', error);
        } finally {
            loading.value = false;
        }
    };

    // Lifecycle
    onMounted(() => {
        loadDoctypes();
    });
</script>

<style scoped>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
