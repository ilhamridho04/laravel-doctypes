<template>
    <div class="doctype-form">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ isEditing ? 'Edit DocType' : 'Create DocType' }}
                </h1>
                <p class="mt-2 text-sm text-gray-700">
                    {{ isEditing ? 'Update your doctype configuration' : 'Define a new document type with custom fields'
                    }}
                </p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-8">
                <!-- Basic Information -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input id="name" type="text" v-model="formData.name" :disabled="isEditing" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{ 'bg-gray-50 text-gray-500': isEditing }" placeholder="user_profile" />
                            <p class="mt-1 text-sm text-gray-500">
                                Unique identifier (letters, numbers, underscores only)
                            </p>
                        </div>

                        <div>
                            <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                                Label <span class="text-red-500">*</span>
                            </label>
                            <input id="label" type="text" v-model="formData.label" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="User Profile" />
                            <p class="mt-1 text-sm text-gray-500">
                                Display name for the doctype
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea id="description" v-model="formData.description" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Brief description of what this doctype represents" />
                        </div>

                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                                Icon
                            </label>
                            <input id="icon" type="text" v-model="formData.icon"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="user" />
                            <p class="mt-1 text-sm text-gray-500">
                                Icon identifier (e.g., user, document, settings)
                            </p>
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                Color
                            </label>
                            <div class="flex items-center space-x-2">
                                <input id="color" type="color" v-model="formData.color"
                                    class="h-10 w-16 rounded border border-gray-300 cursor-pointer" />
                                <input type="text" v-model="formData.color"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="#3b82f6" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="formData.is_active"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Fields Configuration -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-medium text-gray-900">Fields</h2>
                        <button type="button" @click="addField"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Add Field
                        </button>
                    </div>

                    <div v-if="formData.fields.length === 0" class="text-center py-8 text-gray-500">
                        <DocumentIcon class="mx-auto h-8 w-8 text-gray-400 mb-2" />
                        <p>No fields defined yet. Click "Add Field" to get started.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="(field, index) in formData.fields" :key="field.fieldname || index"
                            class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    Field {{ index + 1 }}: {{ field.label || 'Untitled Field' }}
                                </h3>
                                <button type="button" @click="removeField(index)"
                                    class="text-red-600 hover:text-red-500" title="Remove field">
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label :for="`field-${index}-name`"
                                        class="block text-sm font-medium text-gray-700 mb-1">
                                        Field Name <span class="text-red-500">*</span>
                                    </label>
                                    <input :id="`field-${index}-name`" type="text" v-model="field.fieldname" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="first_name" />
                                </div>

                                <div>
                                    <label :for="`field-${index}-label`"
                                        class="block text-sm font-medium text-gray-700 mb-1">
                                        Label <span class="text-red-500">*</span>
                                    </label>
                                    <input :id="`field-${index}-label`" type="text" v-model="field.label" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="First Name" />
                                </div>

                                <div>
                                    <label :for="`field-${index}-type`"
                                        class="block text-sm font-medium text-gray-700 mb-1">
                                        Field Type <span class="text-red-500">*</span>
                                    </label>
                                    <select :id="`field-${index}-type`" v-model="field.fieldtype" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select type</option>
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

                                <div class="md:col-span-2 lg:col-span-3">
                                    <label :for="`field-${index}-description`"
                                        class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <input :id="`field-${index}-description`" type="text" v-model="field.description"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Brief description of this field" />
                                </div>

                                <!-- Select Options -->
                                <div v-if="field.fieldtype === 'select'" class="md:col-span-2 lg:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Select Options
                                    </label>
                                    <div class="space-y-2">
                                        <div v-for="(option, optIndex) in getSelectOptions(field)" :key="optIndex"
                                            class="flex items-center space-x-2">
                                            <input type="text" v-model="getSelectOptions(field)[optIndex]"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Option value" />
                                            <button type="button" @click="removeSelectOption(field, optIndex)"
                                                class="text-red-600 hover:text-red-500">
                                                <TrashIcon class="h-4 w-4" />
                                            </button>
                                        </div>
                                        <button type="button" @click="addSelectOption(field)"
                                            class="text-indigo-600 hover:text-indigo-500 text-sm">
                                            + Add Option
                                        </button>
                                    </div>
                                </div>

                                <!-- Field Options -->
                                <div class="flex items-center space-x-4 md:col-span-2 lg:col-span-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="field.required"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                        <span class="ml-2 text-sm text-gray-700">Required</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="field.unique"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                        <span class="ml-2 text-sm text-gray-700">Unique</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="field.in_list_view"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                        <span class="ml-2 text-sm text-gray-700">Show in List</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="field.in_standard_filter"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                                        <span class="ml-2 text-sm text-gray-700">Filterable</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <button type="button" @click="$emit('cancel')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit" :disabled="loading"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        {{ loading ? 'Saving...' : (isEditing ? 'Update DocType' : 'Create DocType') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue';
    import { useDoctypes } from '../services/useDoctypes';
    import type { Doctype, DoctypeField } from '../types/doctype';
    import { PlusIcon, TrashIcon, DocumentIcon } from '@heroicons/vue/24/outline';

    interface Props {
        doctype?: Doctype;
    }

    interface Emits {
        (event: 'saved', doctype: Doctype): void;
        (event: 'cancel'): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const { createDoctype, updateDoctype, loading } = useDoctypes();

    const isEditing = computed(() => !!props.doctype?.id);

    const formData = ref<{
        name: string;
        label: string;
        description: string;
        icon: string;
        color: string;
        is_active: boolean;
        fields: DoctypeField[];
    }>({
        name: '',
        label: '',
        description: '',
        icon: '',
        color: '#3b82f6',
        is_active: true,
        fields: [],
    });

    const addField = () => {
        formData.value.fields.push({
            fieldname: '',
            label: '',
            fieldtype: 'text',
            required: false,
            unique: false,
            in_list_view: false,
            in_standard_filter: false,
            description: '',
            sort_order: formData.value.fields.length,
        });
    };

    const removeField = (index: number) => {
        formData.value.fields.splice(index, 1);
    };

    const getSelectOptions = (field: DoctypeField): string[] => {
        if (!field.options) {
            field.options = { options: [] };
        }
        if (!field.options.options) {
            field.options.options = [];
        }
        return field.options.options as string[];
    };

    const addSelectOption = (field: DoctypeField) => {
        const options = getSelectOptions(field);
        options.push('');
    };

    const removeSelectOption = (field: DoctypeField, index: number) => {
        const options = getSelectOptions(field);
        options.splice(index, 1);
    };

    const submitForm = async () => {
        try {
            let result: Doctype;

            if (isEditing.value && props.doctype?.id) {
                result = await updateDoctype(props.doctype.id, formData.value);
            } else {
                result = await createDoctype(formData.value);
            }

            emit('saved', result);
        } catch (error) {
            console.error('Failed to save doctype:', error);
        }
    };

    onMounted(() => {
        if (props.doctype) {
            formData.value = {
                name: props.doctype.name,
                label: props.doctype.label,
                description: props.doctype.description || '',
                icon: props.doctype.icon || '',
                color: props.doctype.color || '#3b82f6',
                is_active: props.doctype.is_active ?? true,
                fields: props.doctype.fields || [],
            };
        }
    });
</script>

<style scoped>
    .doctype-form {
        padding: 1rem;
    }
</style>
