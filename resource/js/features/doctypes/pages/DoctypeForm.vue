<template>
    <div class="doctype-form max-w-4xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ isEditing ? 'Edit DocType' : 'Create New DocType' }}
            </h1>
            <p class="text-gray-600 mt-2">
                {{ isEditing ? 'Modify your DocType definition' : 'Define fields and configuration for your new DocType'
                }}
            </p>
        </div>

        <!-- Main Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">DocType Information</h3>
            </div>
            <div class="p-6">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                DocType Name *
                            </label>
                            <input id="name" v-model="form.name" type="text" required :disabled="isEditing"
                                placeholder="e.g., Customer, Product, Order"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />
                            <p class="text-xs text-gray-500 mt-1">Unique identifier for this DocType</p>
                        </div>

                        <div>
                            <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                                Display Label *
                            </label>
                            <input id="label" v-model="form.label" type="text" required
                                placeholder="e.g., Customer Management, Product Catalog"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <p class="text-xs text-gray-500 mt-1">Human-readable name for the DocType</p>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" v-model="form.description" rows="3"
                            placeholder="Brief description of what this DocType represents..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Fields Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">Fields</h4>
                            <button type="button" @click="addField"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors inline-flex items-center">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Field
                            </button>
                        </div>

                        <!-- Fields List -->
                        <div v-if="form.fields.length === 0"
                            class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No fields defined</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding your first field.</p>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="(field, index) in form.fields" :key="index"
                                class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label :for="`field-name-${index}`"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            Field Name *
                                        </label>
                                        <input :id="`field-name-${index}`" v-model="field.fieldname" type="text"
                                            required placeholder="field_name"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" />
                                    </div>

                                    <div>
                                        <label :for="`field-label-${index}`"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            Label *
                                        </label>
                                        <input :id="`field-label-${index}`" v-model="field.label" type="text" required
                                            placeholder="Field Label"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" />
                                    </div>

                                    <div>
                                        <label :for="`field-type-${index}`"
                                            class="block text-sm font-medium text-gray-700 mb-1">
                                            Field Type *
                                        </label>
                                        <select :id="`field-type-${index}`" v-model="field.fieldtype" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <option value="">Select Type</option>
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
                                        <button type="button" @click="removeField(index)"
                                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-3 rounded-md transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Field Options -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <label class="flex items-center">
                                            <input v-model="field.required" type="checkbox"
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                                            <span class="ml-2 text-sm text-gray-700">Required</span>
                                        </label>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input v-model="field.unique" type="checkbox"
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                                            <span class="ml-2 text-sm text-gray-700">Unique</span>
                                        </label>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input v-model="field.in_list_view" type="checkbox"
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                                            <span class="ml-2 text-sm text-gray-700">Show in List</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Select Options -->
                                <div v-if="field.fieldtype === 'select'" class="mt-4">
                                    <label :for="`field-options-${index}`"
                                        class="block text-sm font-medium text-gray-700 mb-1">
                                        Options (one per line)
                                    </label>
                                    <textarea :id="`field-options-${index}`" v-model="field.options" rows="3"
                                        placeholder="Option 1&#10;Option 2&#10;Option 3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                                </div>

                                <!-- Field Description -->
                                <div class="mt-4">
                                    <label :for="`field-description-${index}`"
                                        class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <input :id="`field-description-${index}`" v-model="field.description" type="text"
                                        placeholder="Optional field description..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" @click="$emit('cancel')"
                            class="bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md border border-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loading || !isFormValid"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center">
                            <div v-if="loading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2">
                            </div>
                            {{ loading ? 'Saving...' : (isEditing ? 'Update DocType' : 'Create DocType') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Message -->
        <div v-if="message" class="mt-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ message }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';

    const props = defineProps({
        doctypeId: {
            type: [String, Number],
            default: null
        }
    });

    const emit = defineEmits(['save', 'cancel']);

    // State
    const loading = ref(false);
    const message = ref('');

    const form = ref({
        name: '',
        label: '',
        description: '',
        fields: []
    });

    // Computed
    const isEditing = computed(() => !!props.doctypeId);

    const isFormValid = computed(() => {
        return form.value.name &&
            form.value.label &&
            form.value.fields.length > 0 &&
            form.value.fields.every(field =>
                field.fieldname &&
                field.label &&
                field.fieldtype
            );
    });

    // Methods
    const addField = () => {
        form.value.fields.push({
            fieldname: '',
            label: '',
            fieldtype: '',
            required: false,
            unique: false,
            in_list_view: false,
            options: '',
            description: ''
        });
    };

    const removeField = (index) => {
        form.value.fields.splice(index, 1);
    };

    const handleSubmit = async () => {
        if (!isFormValid.value) return;

        loading.value = true;
        try {
            // Prepare data for submission
            const submissionData = {
                ...form.value,
                fields: form.value.fields.map(field => ({
                    ...field,
                    options: field.fieldtype === 'select' ? field.options : null
                }))
            };

            emit('save', submissionData);

            message.value = isEditing.value
                ? 'DocType updated successfully!'
                : 'DocType created successfully!';

            // Clear message after 3 seconds
            setTimeout(() => {
                message.value = '';
            }, 3000);

        } catch (error) {
            console.error('Error saving DocType:', error);
        } finally {
            loading.value = false;
        }
    };

    const loadDoctype = async () => {
        if (!props.doctypeId) return;

        loading.value = true;
        try {
            // In a real app, this would be an API call
            // const response = await api.get(`/doctypes/${props.doctypeId}`);
            // form.value = response.data;

            // For now, just simulate loading
            console.log('Loading DocType:', props.doctypeId);
        } catch (error) {
            console.error('Error loading DocType:', error);
        } finally {
            loading.value = false;
        }
    };

    // Lifecycle
    onMounted(() => {
        if (isEditing.value) {
            loadDoctype();
        }
    });
</script>

<style scoped>
    /* Additional custom styles if needed */
</style>
