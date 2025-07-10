<template>
    <div class="generated-form">
        <div class="max-w-4xl mx-auto">
            <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                <p class="mt-2 text-sm text-gray-500">Loading form...</p>
            </div>

            <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                    </div>
                </div>
            </div>

            <div v-else-if="schema.length === 0" class="text-center py-12">
                <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No fields defined</h3>
                <p class="mt-1 text-sm text-gray-500">
                    This doctype doesn't have any fields configured yet.
                </p>
            </div>

            <form v-else @submit.prevent="submitForm" class="space-y-6">
                <!-- Form Header -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">
                                {{ doctype?.label || 'Dynamic Form' }}
                            </h1>
                            <p v-if="doctype?.description" class="mt-1 text-sm text-gray-500">
                                {{ doctype.description }}
                            </p>
                        </div>
                        <div v-if="doctype?.icon || doctype?.color" class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-semibold"
                                :style="{ backgroundColor: doctype?.color || '#6b7280' }">
                                {{ doctype?.icon || getInitials(doctype?.label || '') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="field in schema" :key="field.name" :class="getFieldClasses(field)">
                            <FieldRenderer :field="field" :model-value="formData[field.name]"
                                :error="formErrors[field.name]" :readonly="readonly"
                                @update:model-value="updateField(field.name, $event)"
                                @blur="validateField(field.name)" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" @click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Reset
                        </button>
                        <button type="button" @click="$emit('cancel')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" :disabled="saving || !isFormValid"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            {{ saving ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </div>

                <!-- Debug Info (Development Only) -->
                <div v-if="showDebug" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Debug Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-xs font-medium text-gray-700 mb-1">Form Data</h4>
                            <pre
                                class="text-xs bg-white p-2 rounded border overflow-auto max-h-40">{{ JSON.stringify(formData, null, 2) }}</pre>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-700 mb-1">Form Errors</h4>
                            <pre
                                class="text-xs bg-white p-2 rounded border overflow-auto max-h-40">{{ JSON.stringify(formErrors, null, 2) }}</pre>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted, watch } from 'vue';
    import { useDoctypes } from '../services/useDoctypes';
    import FieldRenderer from '../components/FieldRenderer.vue';
    import type {
        Doctype,
        DoctypeFormSchema,
        GeneratedFormData,
        FormValidationErrors
    } from '../types/doctype';
    import { ExclamationCircleIcon, DocumentIcon } from '@heroicons/vue/24/outline';

    interface Props {
        doctypeId?: number;
        doctype?: Doctype;
        initialData?: GeneratedFormData;
        readonly?: boolean;
        showDebug?: boolean;
    }

    interface Emits {
        (event: 'submit', data: GeneratedFormData): void;
        (event: 'cancel'): void;
        (event: 'change', data: GeneratedFormData): void;
    }

    const props = withDefaults(defineProps<Props>(), {
        readonly: false,
        showDebug: false,
    });

    const emit = defineEmits<Emits>();

    const {
        currentDoctype,
        loading,
        error,
        fetchDoctype,
        getFormSchema
    } = useDoctypes();

    const schema = ref<DoctypeFormSchema[]>([]);
    const formData = ref<GeneratedFormData>({});
    const formErrors = ref<FormValidationErrors>({});
    const saving = ref(false);

    const doctype = computed(() => props.doctype || currentDoctype.value);

    const isFormValid = computed(() => {
        // Check if all required fields are filled
        const requiredFields = schema.value.filter(field => field.required);
        return requiredFields.every(field => {
            const value = formData.value[field.name];
            return value !== undefined && value !== null && value !== '';
        });
    });

    const updateField = (fieldName: string, value: any) => {
        formData.value[fieldName] = value;

        // Clear error when user starts typing
        if (formErrors.value[fieldName]) {
            delete formErrors.value[fieldName];
        }

        emit('change', formData.value);
    };

    const validateField = (fieldName: string) => {
        const field = schema.value.find(f => f.name === fieldName);
        if (!field) return;

        const value = formData.value[fieldName];
        const errors: string[] = [];

        // Required validation
        if (field.required && (value === undefined || value === null || value === '')) {
            errors.push(`${field.label} is required`);
        }

        // Email validation
        if (field.type === 'email' && value && !isValidEmail(value)) {
            errors.push(`${field.label} must be a valid email address`);
        }

        // Number validation
        if (field.type === 'number' && value !== undefined && value !== null && value !== '') {
            const numValue = Number(value);
            if (isNaN(numValue)) {
                errors.push(`${field.label} must be a valid number`);
            } else {
                if (field.options?.min !== undefined && numValue < field.options.min) {
                    errors.push(`${field.label} must be at least ${field.options.min}`);
                }
                if (field.options?.max !== undefined && numValue > field.options.max) {
                    errors.push(`${field.label} must be at most ${field.options.max}`);
                }
            }
        }

        // Text length validation
        if ((field.type === 'text' || field.type === 'textarea') && value) {
            if (field.options?.minLength && value.length < field.options.minLength) {
                errors.push(`${field.label} must be at least ${field.options.minLength} characters`);
            }
            if (field.options?.maxLength && value.length > field.options.maxLength) {
                errors.push(`${field.label} must be at most ${field.options.maxLength} characters`);
            }
        }

        // Set or clear errors
        if (errors.length > 0) {
            formErrors.value[fieldName] = errors;
        } else {
            delete formErrors.value[fieldName];
        }
    };

    const validateForm = () => {
        formErrors.value = {};

        schema.value.forEach(field => {
            validateField(field.name);
        });

        return Object.keys(formErrors.value).length === 0;
    };

    const resetForm = () => {
        formData.value = {};
        formErrors.value = {};

        // Set default values
        schema.value.forEach(field => {
            if (field.default_value !== undefined) {
                formData.value[field.name] = field.default_value;
            }
        });

        // Apply initial data if provided
        if (props.initialData) {
            Object.assign(formData.value, props.initialData);
        }
    };

    const submitForm = async () => {
        if (!validateForm()) {
            return;
        }

        saving.value = true;

        try {
            emit('submit', formData.value);
        } finally {
            saving.value = false;
        }
    };

    const getFieldClasses = (field: DoctypeFormSchema) => {
        const classes = [];

        // Full width for certain field types
        if (field.type === 'textarea' || field.type === 'json') {
            classes.push('md:col-span-2');
        }

        return classes.join(' ');
    };

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const isValidEmail = (email: string) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    };

    const loadSchema = async () => {
        try {
            if (props.doctype) {
                schema.value = props.doctype.fields || [];
            } else if (props.doctypeId) {
                const doctypeData = await fetchDoctype(props.doctypeId);
                schema.value = await getFormSchema(props.doctypeId);
            }

            // Initialize form data with default values
            resetForm();
        } catch (err) {
            console.error('Failed to load schema:', err);
        }
    };

    // Watch for changes in doctype
    watch(() => props.doctypeId, loadSchema, { immediate: true });
    watch(() => props.doctype, loadSchema, { immediate: true });

    onMounted(() => {
        loadSchema();
    });
</script>

<style scoped>
    .generated-form {
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
