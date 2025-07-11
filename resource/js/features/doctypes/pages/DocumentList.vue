<template>
    <div class="document-list max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ listConfig?.title || props.doctypeName }} Documents</h1>
                <p class="text-gray-600 mt-1">Manage your {{ props.doctypeName.toLowerCase() }} documents</p>
            </div>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button @click="refreshDocuments" :disabled="loading"
                    class="btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    🔄 Refresh
                </button>
                <router-link :to="`/doctypes/${props.doctypeName}/create`">
                    <button class="btn btn-primary">
                        ➕ Create New
                    </button>
                </router-link>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input v-model="searchQuery" @input="debouncedSearch" type="text"
                            placeholder="Search documents..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input v-model="filters.from_date" @change="applyFilters" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input v-model="filters.to_date" @change="applyFilters" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                </div>

                <!-- Clear Filters -->
                <div v-if="hasActiveFilters" class="mt-4 flex justify-end">
                    <button @click="clearFilters" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Clear all filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div v-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            📄
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Documents</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.total }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            🕐
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.today }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            📅
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">This Week</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.this_week }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            📊
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">This Month</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.this_month }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading documents...</span>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="alert alert-error">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="text-red-400">⚠️</span>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error</h3>
                    <div class="mt-2 text-sm text-red-700">{{ error }}</div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="documents.length === 0" class="text-center py-12">
            <div class="mx-auto h-16 w-16 text-gray-400 mb-4 text-4xl">📄</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
            <p class="text-gray-600 mb-4">Get started by creating your first {{ props.doctypeName.toLowerCase() }}
                document</p>
            <router-link :to="`/doctypes/${props.doctypeName}/create`">
                <button class="btn btn-primary">Create First Document</button>
            </router-link>
        </div>

        <!-- Documents Table -->
        <div v-else class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Document
                        </th>
                        <th v-for="field in visibleFields" :key="field"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ getFieldLabel(field) }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="document in documents" :key="document.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ document.display_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ document.name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td v-for="field in visibleFields" :key="field"
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ formatFieldValue(document[field]) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDate(document.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <router-link :to="`/doctypes/${props.doctypeName}/${document.id}`"
                                    class="text-blue-600 hover:text-blue-900">
                                    👁️ View
                                </router-link>
                                <router-link :to="`/doctypes/${props.doctypeName}/${document.id}/edit`"
                                    class="text-green-600 hover:text-green-900">
                                    ✏️ Edit
                                </router-link>
                                <button @click="duplicateDocument(document)"
                                    class="text-purple-600 hover:text-purple-900">
                                    📋 Duplicate
                                </button>
                                <button @click="confirmDelete(document)" class="text-red-600 hover:text-red-900">
                                    🗑️ Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.total > meta.per_page" class="mt-8 flex justify-center">
            <nav class="flex items-center space-x-2">
                <button @click="goToPage(meta.current_page - 1)" :disabled="meta.current_page <= 1"
                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    ← Previous
                </button>

                <template v-for="page in paginationPages" :key="page">
                    <button v-if="page !== '...'" @click="goToPage(page)" :class="[
                        'px-3 py-2 text-sm font-medium rounded-md',
                        page === meta.current_page
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                    ]">
                        {{ page }}
                    </button>
                    <span v-else class="px-3 py-2 text-sm text-gray-500">...</span>
                </template>

                <button @click="goToPage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page"
                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next →
                </button>
            </nav>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <span class="text-red-400 text-2xl">⚠️</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Delete Document</h3>
                        </div>
                    </div>
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Are you sure you want to delete "{{ documentToDelete?.display_name }}"? This action cannot
                            be
                            undone.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button @click="closeDeleteModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button @click="deleteDocument"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, computed, onMounted, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useDoctypes } from '../services/useDoctypes';
    import { debounce } from 'lodash-es';

    const props = defineProps({
        doctypeName: {
            type: String,
            required: true
        }
    });

    const route = useRoute();
    const router = useRouter();
    const {
        getDocuments,
        deleteDocument: deleteDocumentApi,
        duplicateDocument: duplicateDocumentApi,
        getListConfig,
        getDoctypeStats,
        documents,
        loading,
        error,
        meta
    } = useDoctypes();

    // State
    const searchQuery = ref('');
    const filters = reactive({
        from_date: '',
        to_date: '',
    });
    const showDeleteModal = ref(false);
    const documentToDelete = ref(null);
    const listConfig = ref(null);
    const stats = ref(null);

    // Computed
    const hasActiveFilters = computed(() => {
        return searchQuery.value || filters.from_date || filters.to_date;
    });

    const visibleFields = computed(() => {
        return listConfig.value?.list_fields || [];
    });

    const paginationPages = computed(() => {
        const pages = [];
        const currentPage = meta.value.current_page;
        const lastPage = meta.value.last_page;

        // Always show first page
        if (currentPage > 3) {
            pages.push(1);
            if (currentPage > 4) pages.push('...');
        }

        // Show pages around current page
        for (let i = Math.max(1, currentPage - 2); i <= Math.min(lastPage, currentPage + 2); i++) {
            pages.push(i);
        }

        // Always show last page
        if (currentPage < lastPage - 2) {
            if (currentPage < lastPage - 3) pages.push('...');
            pages.push(lastPage);
        }

        return pages;
    });

    // Methods
    const debouncedSearch = debounce(() => {
        loadDocuments();
    }, 300);

    const applyFilters = () => {
        loadDocuments();
    };

    const clearFilters = () => {
        searchQuery.value = '';
        filters.from_date = '';
        filters.to_date = '';
        loadDocuments();
    };

    const loadDocuments = async (page = 1) => {
        const searchFilters = {
            search: searchQuery.value,
            ...filters
        };

        await getDocuments(props.doctypeName, page, 15, searchFilters);
    };

    const refreshDocuments = () => {
        loadDocuments(meta.value.current_page);
        loadStats();
    };

    const loadListConfig = async () => {
        try {
            listConfig.value = await getListConfig(props.doctypeName);
        } catch (err) {
            console.error('Failed to load list configuration:', err);
        }
    };

    const loadStats = async () => {
        try {
            stats.value = await getDoctypeStats(props.doctypeName);
        } catch (err) {
            console.error('Failed to load statistics:', err);
        }
    };

    const goToPage = (page) => {
        if (page >= 1 && page <= meta.value.last_page) {
            loadDocuments(page);
        }
    };

    const getFieldLabel = (fieldName) => {
        const fieldConfig = listConfig.value?.fields[fieldName];
        return fieldConfig?.label || fieldName;
    };

    const formatFieldValue = (value) => {
        if (value === null || value === undefined) return '-';
        if (typeof value === 'object') return JSON.stringify(value);
        if (typeof value === 'boolean') return value ? 'Yes' : 'No';
        return value.toString();
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString();
    };

    const confirmDelete = (document) => {
        documentToDelete.value = document;
        showDeleteModal.value = true;
    };

    const closeDeleteModal = () => {
        showDeleteModal.value = false;
        documentToDelete.value = null;
    };

    const deleteDocument = async () => {
        if (documentToDelete.value) {
            try {
                await deleteDocumentApi(props.doctypeName, documentToDelete.value.id);
                closeDeleteModal();
                refreshDocuments();
            } catch (err) {
                console.error('Failed to delete document:', err);
            }
        }
    };

    const duplicateDocument = async (document) => {
        try {
            await duplicateDocumentApi(props.doctypeName, document.id);
            refreshDocuments();
        } catch (err) {
            console.error('Failed to duplicate document:', err);
        }
    };

    // Lifecycle
    onMounted(async () => {
        await loadListConfig();
        await loadDocuments();
        await loadStats();
    });

    // Watchers
    watch(() => props.doctypeName, async () => {
        if (props.doctypeName) {
            await loadListConfig();
            await loadDocuments();
            await loadStats();
        }
    });
</script>

<style scoped>
    .btn {
        padding: 1rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #2563eb;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
    }

    .btn-outline {
        background-color: white;
        color: #374151;
        border-color: #d1d5db;
    }

    .btn-outline:hover {
        background-color: #f9fafb;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
    }

    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }
</style>