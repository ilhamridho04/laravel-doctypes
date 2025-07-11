<template>
    <div class="doctype-form">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">
                {{ isEdit ? 'Edit Doctype' : 'Create Doctype' }}
            </h1>
            <router-link to="/doctypes" class="btn btn-secondary">
                Back to List
            </router-link>
        </div>

        <div v-if="loading" class="text-center py-8">
            Loading...
        </div>

        <div v-else-if="error" class="alert alert-error mb-6">
            {{ error }}
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Doctype Basic Info -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4">Basic Information</h2>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Name *
                        </label>
                        <input v-model="form.name" type="text" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., User, Order, Product" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Label *
                        </label>
                        <input v-model="form.label" type="text" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., User Management, Order Processing" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea v-model="form.description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Optional description for this doctype"></textarea>
                </div>
            </div>

            <!-- Fields Section -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Fields</h2>
                    <button type="button" @click="addField" class="btn btn-primary">
                        Add Field
                    </button>
                </div>

                <div v-if="form.fields.length === 0" class="text-gray-500 text-center py-8">
                    No fields added yet. Click "Add Field" to get started.
                </div>

                <div v-else class="space-y-4">
                    <div v-for="(field, index) in form.fields" :key="index"
                        class="border border-gray-200 rounded-lg p-4">
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Field Name *
                                </label>
                                <input v-model="field.fieldname" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="e.g., first_name" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Label *
                                </label>
                                <input v-model="field.label" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="e.g., First Name" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Field Type *
                                </label>
                                <select v-model="field.fieldtype" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="number">Number</option>
                                    <option value="email">Email</option>
                                    <option value="password">Password</option>
                                    <option value="select">Select</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="date">Date</option>
                                    <option value="datetime">DateTime</option>
                                    <option value="time">Time</option>
                                    <option value="file">File</option>
                                    <option value="image">Image</option>
                                    <option value="json">JSON</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="button" @click="removeField(index)" class="btn btn-danger">
                                    Remove
                                </button>
                            </div>
                        </div>

                        <!-- Field Options -->
                        <div class="mt-4 grid grid-cols-3 gap-4">
                            <div v-if="field.fieldtype === 'select'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Options (one per line)
                                </label>
                                <textarea v-model="field.options" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <input v-model="field.description" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Optional field description" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Default Value
                                </label>
                                <input v-model="field.default_value" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Optional default value" />
                            </div>
                        </div>

                        <!-- Field Flags -->
                        <div class="mt-4 flex space-x-6">
                            <label class="flex items-center">
                                <input v-model="field.required" type="checkbox" class="mr-2" />
                                Required
                            </label>
                            <label class="flex items-center">
                                <input v-model="field.unique" type="checkbox" class="mr-2" />
                                Unique
                            </label>
                            <label class="flex items-center">
                                <input v-model="field.in_list_view" type="checkbox" class="mr-2" />
                                Show in List
                            </label>
                            <label class="flex items-center">
                                <input v-model="field.in_standard_filter" type="checkbox" class="mr-2" />
                                Show in Filter
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <router-link to="/doctypes" class="btn btn-secondary">
                    Cancel
                </router-link>
                <button type="submit" :disabled="loading" class="btn btn-primary">
                    {{ loading ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { useDoctypes } from '../services/useDoctypes';
    import type { DoctypeField, DoctypeCreateRequest, DoctypeUpdateRequest } from '../types/doctype';

    const route = useRoute();
    const router = useRouter();
    const { currentDoctype, loading, error, fetchDoctype, createDoctype, updateDoctype } = useDoctypes();

    const isEdit = computed(() => !!route.params.id);

    const form = ref({
        name: '',
        label: '',
        description: '',
        fields: [] as Omit<DoctypeField, 'id'>[]
    });

    const addField = () => {
        form.value.fields.push({
            fieldname: '',
            label: '',
            fieldtype: 'text',
            required: false,
            unique: false,
            in_list_view: false,
            in_standard_filter: false,
            sort_order: form.value.fields.length + 1
        });
    };

    const removeField = (index: number) => {
        form.value.fields.splice(index, 1);
        // Update sort orders
        form.value.fields.forEach((field, idx) => {
            field.sort_order = idx + 1;
        });
    };

    const handleSubmit = async () => {
        try {
            if (isEdit.value) {
                const updateData: DoctypeUpdateRequest = {
                    id: Number(route.params.id),
                    ...form.value
                };
                await updateDoctype(Number(route.params.id), updateData);
            } else {
                const createData: DoctypeCreateRequest = form.value;
                await createDoctype(createData);
            }

            router.push('/doctypes');
        } catch (err) {
            console.error('Error saving doctype:', err);
        }
    };

    onMounted(async () => {
        if (isEdit.value) {
            try {
                const doctype = await fetchDoctype(Number(route.params.id));
                form.value = {
                    name: doctype.name,
                    label: doctype.label,
                    description: doctype.description || '',
                    fields: doctype.fields.map(field => ({
                        fieldname: field.fieldname,
                        label: field.label,
                        fieldtype: field.fieldtype,
                        options: field.options,
                        required: field.required || false,
                        unique: field.unique || false,
                        in_list_view: field.in_list_view || false,
                        in_standard_filter: field.in_standard_filter || false,
                        description: field.description,
                        default_value: field.default_value,
                        sort_order: field.sort_order || 0
                    }))
                };
            } catch (err) {
                console.error('Error loading doctype:', err);
            }
        }
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

    .btn-danger {
        @apply bg-red-600 text-white hover:bg-red-700;
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
