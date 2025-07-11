<template>
    <div class="doctype-list">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Doctypes</h1>
            <router-link to="/doctypes/create" class="btn btn-primary">
                Create New Doctype
            </router-link>
        </div>

        <div v-if="loading" class="text-center py-8">
            Loading doctypes...
        </div>

        <div v-else-if="error" class="alert alert-error">
            {{ error }}
        </div>

        <div v-else>
            <div class="bg-white rounded-lg shadow">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Label
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fields
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
                        <tr v-for="doctype in doctypes" :key="doctype.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ doctype.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ doctype.label }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ doctype.fields_count || doctype.fields?.length || 0 }} fields
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ formatDate(doctype.created_at) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <router-link :to="`/doctypes/${doctype.id}/edit`"
                                    class="text-indigo-600 hover:text-indigo-900 mr-4">
                                    Edit
                                </router-link>
                                <button @click="handleDelete(doctype.id!)" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="meta.last_page > 1" class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-700">
                    Showing {{ (meta.current_page - 1) * meta.per_page + 1 }} to
                    {{ Math.min(meta.current_page * meta.per_page, meta.total) }} of {{ meta.total }} results
                </div>
                <div class="flex space-x-2">
                    <button @click="fetchDoctypes(meta.current_page - 1)" :disabled="meta.current_page === 1"
                        class="btn btn-secondary">
                        Previous
                    </button>
                    <button @click="fetchDoctypes(meta.current_page + 1)"
                        :disabled="meta.current_page === meta.last_page" class="btn btn-secondary">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { onMounted } from 'vue';
    import { useDoctypes } from '../services/useDoctypes';

    const { doctypes, loading, error, meta, fetchDoctypes, deleteDoctype } = useDoctypes();

    const formatDate = (dateString?: string) => {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString();
    };

    const handleDelete = async (id: number) => {
        if (confirm('Are you sure you want to delete this doctype?')) {
            try {
                await deleteDoctype(id);
            } catch (err) {
                console.error('Failed to delete doctype:', err);
            }
        }
    };

    onMounted(() => {
        fetchDoctypes();
    });
</script>

<style scoped>
    .btn {
        @apply px-4 py-2 rounded-md font-medium transition-colors;
    }

    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700;
    }

    .btn-secondary {
        @apply bg-gray-300 text-gray-700 hover:bg-gray-400;
    }

    .btn:disabled {
        @apply opacity-50 cursor-not-allowed;
    }

    .alert {
        @apply p-4 rounded-md;
    }

    .alert-error {
        @apply bg-red-100 border border-red-400 text-red-700;
    }
</style>
