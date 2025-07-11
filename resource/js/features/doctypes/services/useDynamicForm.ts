import { ref, computed, watch } from 'vue';
import type {
    DoctypeFormSchema,
    FormField,
    DynamicFormData,
    FormErrors,
    UseDynamicFormState,
    UseDynamicFormActions,
    Doctype,
    FormFieldValidation
} from '../types/doctype';

export const useDynamicForm = () => {
    // State
    const formData = ref<DynamicFormData>({});
    const originalData = ref<DynamicFormData>({});
    const schema = ref<DoctypeFormSchema | null>(null);
    const doctype = ref<Doctype | null>(null);
    const loading = ref(false);
    const saving = ref(false);
    const errors = ref<FormErrors>({});
    const mode = ref<'create' | 'edit' | 'view'>('create');

    // Computed properties
    const isDirty = computed(() => {
        if (mode.value === 'view') return false;
        return JSON.stringify(formData.value) !== JSON.stringify(originalData.value);
    });

    const isValid = computed(() => {
        return Object.keys(errors.value).length === 0 && validateAllFields();
    });

    // API base URL
    const baseUrl = '/api/doctypes';

    // Helper function for API calls
    const apiCall = async <T>(url: string, options: RequestInit = {}): Promise<T> => {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers,
            },
            ...options,
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return response.json();
    };

    // Load schema for a doctype
    const loadSchema = async (doctypeName: string): Promise<void> => {
        loading.value = true;

        try {
            const response = await apiCall<{
                success: boolean;
                data: { doctype: Doctype; schema: DoctypeFormSchema };
            }>(`${baseUrl}/name/${doctypeName}/schema`);

            schema.value = response.data.schema;
            doctype.value = response.data.doctype;

            // Initialize form data with default values
            initializeFormData();
        } catch (error) {
            console.error('Failed to load schema:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Load existing record
    const loadRecord = async (doctypeName: string, recordId: number | string): Promise<void> => {
        loading.value = true;

        try {
            // First load the schema
            await loadSchema(doctypeName);

            // Then load the record data
            const response = await apiCall<{
                success: boolean;
                data: Record<string, any>;
            }>(`/api/dynamic-models/${doctypeName}/${recordId}`);

            formData.value = { ...response.data };
            originalData.value = { ...response.data };
            mode.value = 'edit';
        } catch (error) {
            console.error('Failed to load record:', error);
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // Initialize form data with default values
    const initializeFormData = (): void => {
        if (!schema.value) return;

        const initialData: DynamicFormData = {};

        schema.value.fields.forEach(field => {
            initialData[field.name] = getDefaultValue(field);
        });

        formData.value = initialData;
        originalData.value = { ...initialData };
        errors.value = {};
    };

    // Get default value for a field
    const getDefaultValue = (field: FormField): any => {
        if (field.default_value !== undefined) {
            return field.default_value;
        }

        switch (field.type) {
            case 'checkbox':
                return false;
            case 'number':
                return field.min || 0;
            case 'select':
                return field.options?.[0] || '';
            case 'json':
                return {};
            case 'date':
            case 'datetime':
            case 'time':
                return '';
            default:
                return '';
        }
    };

    // Reset form to initial state
    const resetForm = (): void => {
        initializeFormData();
    };

    // Validate a single field
    const validateField = (fieldName: string): boolean => {
        if (!schema.value) return true;

        const field = schema.value.fields.find(f => f.name === fieldName);
        if (!field) return true;

        const value = formData.value[fieldName];
        const fieldErrors: string[] = [];

        // Required validation
        if (field.required && (!value || value === '')) {
            fieldErrors.push(`${field.label} is required`);
        }

        // Type-specific validation
        if (value && field.validation) {
            const validation = field.validation;

            if (validation.minLength && String(value).length < validation.minLength) {
                fieldErrors.push(`${field.label} must be at least ${validation.minLength} characters`);
            }

            if (validation.maxLength && String(value).length > validation.maxLength) {
                fieldErrors.push(`${field.label} must not exceed ${validation.maxLength} characters`);
            }

            if (validation.min && Number(value) < validation.min) {
                fieldErrors.push(`${field.label} must be at least ${validation.min}`);
            }

            if (validation.max && Number(value) > validation.max) {
                fieldErrors.push(`${field.label} must not exceed ${validation.max}`);
            }

            if (validation.pattern && !new RegExp(validation.pattern).test(String(value))) {
                fieldErrors.push(`${field.label} format is invalid`);
            }

            if (validation.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    fieldErrors.push(`${field.label} must be a valid email address`);
                }
            }

            if (validation.type === 'url' && value) {
                try {
                    new URL(value);
                } catch {
                    fieldErrors.push(`${field.label} must be a valid URL`);
                }
            }
        }

        // Set or clear errors
        if (fieldErrors.length > 0) {
            errors.value[fieldName] = fieldErrors[0];
            return false;
        } else {
            delete errors.value[fieldName];
            return true;
        }
    };

    // Validate all fields
    const validateAllFields = (): boolean => {
        if (!schema.value) return true;

        let isValid = true;
        schema.value.fields.forEach(field => {
            if (!validateField(field.name)) {
                isValid = false;
            }
        });

        return isValid;
    };

    // Validate entire form
    const validateForm = (): boolean => {
        return validateAllFields();
    };

    // Save the record
    const saveRecord = async (): Promise<any> => {
        if (!schema.value || !doctype.value) {
            throw new Error('Schema not loaded');
        }

        if (!validateForm()) {
            throw new Error('Form validation failed');
        }

        saving.value = true;

        try {
            let response;

            if (mode.value === 'create') {
                response = await apiCall('/api/dynamic-models', {
                    method: 'POST',
                    body: JSON.stringify({
                        doctype: doctype.value.name,
                        data: formData.value
                    })
                });
            } else {
                const recordId = formData.value.id;
                if (!recordId) {
                    throw new Error('Record ID not found for update');
                }

                response = await apiCall(`/api/dynamic-models/${doctype.value.name}/${recordId}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        doctype: doctype.value.name,
                        data: formData.value
                    })
                });
            }

            // Update original data to reflect saved state
            originalData.value = { ...formData.value };

            return response;
        } catch (error) {
            console.error('Failed to save record:', error);
            throw error;
        } finally {
            saving.value = false;
        }
    };

    // Update a field value
    const updateField = (fieldName: string, value: any): void => {
        formData.value[fieldName] = value;

        // Validate the field
        validateField(fieldName);
    };

    // Set field error
    const setFieldError = (fieldName: string, error: string): void => {
        errors.value[fieldName] = error;
    };

    // Clear field error
    const clearFieldError = (fieldName: string): void => {
        delete errors.value[fieldName];
    };

    // Clear all errors
    const clearAllErrors = (): void => {
        errors.value = {};
    };

    // Set form mode
    const setMode = (newMode: 'create' | 'edit' | 'view'): void => {
        mode.value = newMode;
    };

    // Check if field should be visible (depends_on logic)
    const shouldShowField = (field: FormField): boolean => {
        if (!field.depends_on) return true;

        try {
            const condition = field.depends_on.trim();
            const operators = ['!=', '=', '>', '<', '>=', '<='];

            for (const operator of operators) {
                if (condition.includes(operator)) {
                    const [fieldName, expectedValue] = condition.split(operator).map(s => s.trim());
                    const actualValue = formData.value[fieldName];

                    switch (operator) {
                        case '=':
                            return actualValue == expectedValue;
                        case '!=':
                            return actualValue != expectedValue;
                        case '>':
                            return Number(actualValue) > Number(expectedValue);
                        case '<':
                            return Number(actualValue) < Number(expectedValue);
                        case '>=':
                            return Number(actualValue) >= Number(expectedValue);
                        case '<=':
                            return Number(actualValue) <= Number(expectedValue);
                    }
                }
            }
        } catch (error) {
            console.warn('Error evaluating depends_on condition:', field.depends_on);
        }

        return true;
    };

    // Get visible fields
    const visibleFields = computed(() => {
        if (!schema.value) return [];
        return schema.value.fields.filter(field => shouldShowField(field));
    });

    // Watch for form data changes to trigger validation
    watch(
        formData,
        () => {
            // Debounced validation could be added here
        },
        { deep: true }
    );

    return {
        // State - matches UseDynamicFormState
        formData,
        originalData,
        schema,
        doctype,
        loading,
        saving,
        errors,
        isDirty,
        isValid,
        mode,

        // Actions - matches UseDynamicFormActions
        loadSchema,
        loadRecord,
        resetForm,
        validateField,
        validateForm,
        saveRecord,
        updateField,
        setFieldError,
        clearFieldError,
        clearAllErrors,
        setMode,

        // Additional utilities
        shouldShowField,
        visibleFields,
        initializeFormData,
        getDefaultValue,
        validateAllFields,
    } as UseDynamicFormState & UseDynamicFormActions & {
        shouldShowField: typeof shouldShowField;
        visibleFields: typeof visibleFields;
        initializeFormData: typeof initializeFormData;
        getDefaultValue: typeof getDefaultValue;
        validateAllFields: typeof validateAllFields;
    };
};
