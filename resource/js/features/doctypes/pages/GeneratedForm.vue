<template>
    <div class="generated-form">
        <div class="max-w-4xl mx-auto">
            <div v-if="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-sm text-muted-foreground">Loading form...</p>
            </div>

            <div v-else-if="error" class="bg-destructive/10 border border-destructive rounded-md p-4">
                <div class="flex">
                    <ExclamationCircleIcon class="h-5 w-5 text-destructive" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-destructive">Error</h3>
                        <p class="mt-1 text-sm text-destructive">{{ error }}</p>
                    </div>
                </div>
            </div>

            <div v-else-if="schema.length === 0" class="text-center py-12">
                <DocumentIcon class="mx-auto h-12 w-12 text-muted-foreground" />
                <h3 class="mt-2 text-sm font-medium text-foreground">No fields defined</h3>
                <p class="mt-1 text-sm text-muted-foreground">
                    This doctype doesn't have any fields configured yet.
                </p>
            </div>

            <form v-else @submit.prevent="submitForm" class="space-y-6">
                <!-- Form Header -->
                <div class="bg-card shadow-sm rounded-lg p-6 border border-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-foreground">
                                {{ doctype?.label || 'Dynamic Form' }}
                            </h1>
                            <p v-if="doctype?.description" class="mt-1 text-sm text-muted-foreground">
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
                <div class="bg-card shadow-sm rounded-lg p-6 border border-border">
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
                <div class="bg-card shadow-sm rounded-lg p-6 border border-border">
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" @click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-muted-foreground bg-background border border-input rounded-md shadow-sm hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                            Reset
                        </button>
                        <button type="button" @click="$emit('cancel')"
                            class="px-4 py-2 text-sm font-medium text-muted-foreground bg-background border border-input rounded-md shadow-sm hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                            Cancel
                        </button>
                        <button type="submit" :disabled="saving || !isFormValid"
                            class="px-4 py-2 text-sm font-medium text-primary-foreground bg-primary border border-transparent rounded-md shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring disabled:opacity-50">
                            {{ saving ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </div>

                <!-- Debug Info (Development Only) -->
                <div v-if="showDebug" class="bg-muted border border-border rounded-lg p-4">
                    <h3 class="text-sm font-medium text-foreground mb-2">Debug Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-xs font-medium text-muted-foreground mb-1">Form Data</h4>
                            <pre
                                class="text-xs bg-background p-2 rounded border border-border overflow-auto max-h-40">{{ JSON.stringify(formData, null, 2) }}</pre>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-muted-foreground mb-1">Form Errors</h4>
                            <pre
                                class="text-xs bg-background p-2 rounded border border-border overflow-auto max-h-40">{{ JSON.stringify(formErrors, null, 2) }}</pre>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted, watch } from 'vue';
    import { ExclamationCircleIcon, DocumentIcon } from '@heroicons/vue/24/outline';
    import FieldRenderer from '../components/FieldRenderer.vue';
    import { useDoctypes } from '../services/useDoctypes';
    import type {
        Doctype,
        DoctypeFormSchema,
        DynamicFormData,
        FormErrors
    } from '../types/doctype';

    interface Props {
        doctypeName: string;
        recordId?: string | number;
        readonly?: boolean;
        showDebug?: boolean;
    }

    interface Emits {
        (event: 'submit', data: DynamicFormData): void;
        (event: 'cancel'): void;
        (event: 'save', data: DynamicFormData): void;
    }

    const props = withDefaults(defineProps<Props>(), {
        readonly: false,
        showDebug: false,
    });

    const emit = defineEmits<Emits>();

    const {
        loading,
        error,
        fetchDoctypeSchema,
        initializeFormData,
        updateFormField,
        validateForm,
        submitFormData,
        formData,
        formErrors
    } = useDoctypes();

    const doctype = ref<Doctype | null>(null);
    const schema = ref<DoctypeFormSchema[]>([]);
    const saving = ref(false);

    // Load schema when component mounts or doctypeName changes
    const loadSchema = async () => {
        if (!props.doctypeName) return;

        try {
            const response = await fetchDoctypeSchema(props.doctypeName);
            doctype.value = response.doctype;
            schema.value = response.schema;

            // Initialize form data with default values
            initializeFormData(schema.value);

            // If editing existing record, load its data
            if (props.recordId) {
                await loadRecordData();
            }
        } catch (err) {
            console.error('Failed to load schema:', err);
        }
    };

    // Load existing record data for editing
    const loadRecordData = async () => {
        if (!props.recordId || !props.doctypeName) return;

        try {
            const response = await fetch(`/api/${props.doctypeName}/${props.recordId}`);
            if (response.ok) {
                const data = await response.json();
                // Update form data with loaded record
                Object.keys(data).forEach(key => {
                    if (formData.value.hasOwnProperty(key)) {
                        updateFormField(key, data[key]);
                    }
                });
            }
        } catch (err) {
            console.error('Failed to load record data:', err);
        }
    };

    // Handle field updates
    const updateField = (fieldName: string, value: any) => {
        updateFormField(fieldName, value);
    };

    // Validate specific field
    const validateField = (fieldName: string) => {
        const field = schema.value.find(f => f.name === fieldName);
        if (!field) return;

        validateForm([field]);
    };

    // Check if form is valid
    const isFormValid = computed(() => {
        return Object.keys(formErrors.value).length === 0;
    });

    // Submit form
    const submitForm = async () => {
        if (!validateForm(schema.value)) {
            return;
        }

        saving.value = true;

        try {
            if (props.recordId) {
                // Update existing record
                const response = await fetch(`/api/${props.doctypeName}/${props.recordId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData.value)
                });

                if (response.ok) {
                    const data = await response.json();
                    emit('save', data);
                }
            } else {
                // Create new record
                const data = await submitFormData(props.doctypeName, formData.value);
                emit('submit', data);
            }
        } catch (err) {
            console.error('Failed to submit form:', err);
        } finally {
            saving.value = false;
        }
    };

    // Reset form to initial state
    const resetForm = () => {
        initializeFormData(schema.value);
    };

    // Get field CSS classes for layout
    const getFieldClasses = (field: DoctypeFormSchema) => {
        if (field.type === 'textarea' || field.type === 'json') {
            return 'md:col-span-2';
        }
        return 'md:col-span-1';
    };

    // Get initials for avatar
    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    // Watch for doctypeName changes
    watch(() => props.doctypeName, loadSchema, { immediate: true });

    onMounted(() => {
        loadSchema();
    });
</script>
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
