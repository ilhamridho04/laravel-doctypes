<template>
    <div class="generated-form max-w-4xl mx-auto p-6">
        <!-- Form Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ schema?.title || props.doctypeName }}</h1>
            <p v-if="schema?.description" class="text-gray-600 mt-2">
                {{ schema.description }}
            </p>
        </div>

        <!-- Loading State -->
        <div v-if="loading && !schema" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600">Loading form schema...</span>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="alert alert-error mb-6">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            {{ error }}
        </div>

        <!-- Form -->
        <form v-else-if="schema" @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Form Fields Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-for="field in schema.fields" :key="field.name" :class="getFieldColSpan(field)"
                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <FieldRenderer :field="field" :value="formData[field.name]"
                        :disabled="props.mode === 'view' || submitting"
                        @update:value="updateField(field.name, $event)" />

                    <!-- Field Error -->
                    <p v-if="fieldErrors[field.name]" class="text-red-500 text-sm mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ fieldErrors[field.name] }}
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div v-if="props.mode !== 'view'" class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" @click="resetForm" :disabled="submitting" class="btn btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>

                <button type="button" @click="$emit('cancel')" :disabled="submitting" class="btn btn-outline">
                    Cancel
                </button>

                <button type="submit" :disabled="submitting || !isFormValid" class="btn btn-primary">
                    <span v-if="submitting"
                        class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                    <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ submitting ? 'Saving...' : (props.mode === 'edit' ? 'Update' : 'Create') }}
                </button>
            </div>
        </form>

        <!-- Success/Error Messages -->
        <div v-if="message" class="mt-6">
            <div :class="[
                'p-4 rounded-md flex items-center',
                messageType === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'
            ]">
                <svg v-if="messageType === 'success'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                {{ message }}
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, reactive, computed, onMounted, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import { useDoctypes } from '../services/useDoctypes';
    import FieldRenderer from '../components/FieldRenderer.vue';

    const props = defineProps({
        doctypeName: {
            type: String,
            required: true
        },
        recordId: {
            type: Number,
            default: null
        },
        mode: {
            type: String,
            default: 'create',
            validator: (value) => ['create', 'edit', 'view'].includes(value)
        },
        initialData: {
            type: Object,
            default: () => ({})
        }
    });

    const emit = defineEmits(['save', 'cancel', 'success', 'error']);

    const route = useRoute();
    const { getFormSchema, createDocument, updateDocument, getDocument, loading, error } = useDoctypes();

    // State
    const schema = ref(null);
    const formData = reactive({});
    const fieldErrors = reactive({});
    const submitting = ref(false);
    const message = ref('');
    const messageType = ref('success');

    // Computed
    const isFormValid = computed(() => {
        if (!schema.value) return false;

        return schema.value.fields.every(field => {
            if (field.required) {
                const value = formData[field.name];
                return value !== null && value !== undefined && value !== '';
            }
            return true;
        });
    });

    // Methods
    const getFieldColSpan = (field) => {
        const fullWidthTypes = ['textarea', 'json', 'table'];
        return fullWidthTypes.includes(field.type) ? 'md:col-span-2' : '';
    };

    const updateField = (fieldName, value) => {
        formData[fieldName] = value;

        // Clear field error when updated
        if (fieldErrors[fieldName]) {
            delete fieldErrors[fieldName];
        }

        // Clear message when form is modified
        message.value = '';
    };

    const validateForm = () => {
        // Clear previous errors
        Object.keys(fieldErrors).forEach(key => delete fieldErrors[key]);

        if (!schema.value) return false;

        let isValid = true;

        schema.value.fields.forEach(field => {
            const value = formData[field.name];

            // Required field validation
            if (field.required && (value === null || value === undefined || value === '')) {
                fieldErrors[field.name] = `${field.label} is required`;
                isValid = false;
            }

            // Type-specific validation
            if (value !== null && value !== undefined && value !== '') {
                switch (field.type) {
                    case 'email':
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            fieldErrors[field.name] = 'Please enter a valid email address';
                            isValid = false;
                        }
                        break;
                    case 'number':
                        if (isNaN(Number(value))) {
                            fieldErrors[field.name] = 'Please enter a valid number';
                            isValid = false;
                        }
                        break;
                    case 'json':
                        if (typeof value === 'string') {
                            try {
                                JSON.parse(value);
                            } catch (e) {
                                fieldErrors[field.name] = 'Please enter valid JSON';
                                isValid = false;
                            }
                        }
                        break;
                }
            }
        });

        return isValid;
    };

    const resetForm = () => {
        if (schema.value) {
            // Reset to default values
            schema.value.fields.forEach(field => {
                formData[field.name] = field.default_value ?? getDefaultValue(field.type);
            });

            // Apply initial data if provided
            if (props.initialData) {
                Object.assign(formData, props.initialData);
            }
        }

        // Clear errors and messages
        Object.keys(fieldErrors).forEach(key => delete fieldErrors[key]);
        message.value = '';
    };

    const getDefaultValue = (fieldType) => {
        switch (fieldType) {
            case 'checkbox':
                return false;
            case 'number':
                return 0;
            case 'json':
                return {};
            case 'select':
                return '';
            default:
                return '';
        }
    };

    const handleSubmit = async () => {
        if (!validateForm()) {
            return;
        }

        submitting.value = true;

        try {
            let result;

            if (props.mode === 'edit' && props.recordId) {
                result = await updateDocument(props.doctypeName, props.recordId, formData);
                message.value = 'Document updated successfully';
            } else {
                result = await createDocument(props.doctypeName, formData);
                message.value = 'Document created successfully';
            }

            messageType.value = 'success';
            emit('success', result);
            emit('save', { ...formData });

            // Scroll to top to show success message
            window.scrollTo({ top: 0, behavior: 'smooth' });

        } catch (error) {
            messageType.value = 'error';
            message.value = error.message || 'An error occurred while saving the document';
            emit('error', error.message);

            // Handle validation errors from backend
            if (error.errors) {
                Object.assign(fieldErrors, error.errors);
            }

            // Scroll to top to show error message
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } finally {
            submitting.value = false;
        }
    };

    const loadFormSchema = async () => {
        try {
            schema.value = await getFormSchema(props.doctypeName);
            resetForm();

            // If editing, load existing data
            if (props.mode === 'edit' && props.recordId) {
                const document = await getDocument(props.doctypeName, props.recordId);
                Object.assign(formData, document.data || {});
            }

        } catch (err) {
            messageType.value = 'error';
            message.value = err.message || 'Failed to load form schema';
            emit('error', err.message);
        }
    };

    // Lifecycle
    onMounted(() => {
        loadFormSchema();
    });

    // Watchers
    watch(() => props.doctypeName, () => {
        if (props.doctypeName) {
            loadFormSchema();
        }
    });

    watch(() => props.recordId, () => {
        if (props.recordId && props.mode === 'edit') {
            loadFormSchema();
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

    .btn-secondary {
        background-color: #6b7280;
        color: #374151;
    }

    .btn-secondary:hover {
        background-color: #9ca3af;
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
