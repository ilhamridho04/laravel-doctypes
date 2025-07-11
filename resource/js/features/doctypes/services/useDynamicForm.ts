import { ref, computed, reactive, watch } from 'vue'
import type { Doctype, DoctypeField, FieldType } from '../types/doctype'

export interface FormData {
    [key: string]: any
}

export interface FormValidation {
    isValid: boolean
    errors: Record<string, string[]>
}

export function useDynamicForm(doctype?: Doctype, initialData?: FormData) {
    const formData = reactive<FormData>({})
    const errors = ref<Record<string, string[]>>({})
    const isSubmitting = ref(false)

    // Get visible fields from doctype
    const fields = computed(() => {
        if (!doctype?.fields) return []
        return doctype.fields
    })

    // Initialize form data
    const initializeForm = (data?: FormData) => {
        // Clear existing data
        Object.keys(formData).forEach(key => {
            delete formData[key]
        })

        // Set new data
        if (fields.value.length > 0) {
            fields.value.forEach(field => {
                const value = data?.[field.fieldname]
                    ?? initialData?.[field.fieldname]
                    ?? field.default_value
                    ?? getDefaultValue(field)

                formData[field.fieldname] = value
            })
        }
    }

    // Get default value based on field type
    const getDefaultValue = (field: DoctypeField) => {
        switch (field.fieldtype) {
            case 'checkbox':
                return false
            case 'number':
                return 0
            case 'date':
            case 'datetime':
            case 'time':
                return ''
            case 'select':
                return ''
            default:
                return ''
        }
    }

    // Validate a single field
    const validateField = (field: DoctypeField, value: any): string[] => {
        const fieldErrors: string[] = []

        // Required field validation
        if (field.required && (value === null || value === undefined || value === '')) {
            fieldErrors.push(`${field.label} is required`)
        }

        // Type-specific validation
        switch (field.fieldtype) {
            case 'number':
                if (value !== '' && value !== null && isNaN(Number(value))) {
                    fieldErrors.push(`${field.label} must be a number`)
                }
                break
            case 'email':
                if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    fieldErrors.push(`${field.label} must be a valid email address`)
                }
                break
        }

        return fieldErrors
    }

    // Validate entire form
    const validateForm = (): FormValidation => {
        const formErrors: Record<string, string[]> = {}
        let isValid = true

        fields.value.forEach(field => {
            const fieldErrors = validateField(field, formData[field.fieldname])
            if (fieldErrors.length > 0) {
                formErrors[field.fieldname] = fieldErrors
                isValid = false
            }
        })

        errors.value = formErrors
        return { isValid, errors: formErrors }
    }

    // Get field error messages
    const getFieldErrors = (fieldname: string): string[] => {
        return errors.value[fieldname] || []
    }

    // Check if field has errors
    const hasFieldError = (fieldname: string): boolean => {
        return getFieldErrors(fieldname).length > 0
    }

    // Clear errors for a specific field
    const clearFieldErrors = (fieldname: string) => {
        if (errors.value[fieldname]) {
            delete errors.value[fieldname]
        }
    }

    // Clear all errors
    const clearErrors = () => {
        errors.value = {}
    }

    // Reset form to initial state
    const resetForm = () => {
        initializeForm()
        clearErrors()
    }

    // Get form data as plain object
    const getFormData = (): FormData => {
        return { ...formData }
    }

    // Update a single field value
    const updateField = (fieldname: string, value: any) => {
        formData[fieldname] = value
        // Clear errors for this field when value changes
        clearFieldErrors(fieldname)
    }

    // Set multiple field values
    const setFormData = (data: FormData) => {
        Object.keys(data).forEach(key => {
            if (formData.hasOwnProperty(key)) {
                formData[key] = data[key]
            }
        })
    }

    // Computed validation state
    const validation = computed<FormValidation>(() => {
        return validateForm()
    })

    // Initialize form on creation
    if (doctype) {
        initializeForm(initialData)
    }

    // Watch for doctype changes
    watch(
        () => doctype,
        (newDoctype) => {
            if (newDoctype) {
                initializeForm(initialData)
            }
        },
        { immediate: true }
    )

    return {
        // Reactive data
        formData,
        errors: computed(() => errors.value),
        isSubmitting,
        fields,
        validation,

        // Methods
        initializeForm,
        validateForm,
        validateField,
        getFieldErrors,
        hasFieldError,
        clearFieldErrors,
        clearErrors,
        resetForm,
        getFormData,
        updateField,
        setFormData,
        getDefaultValue
    }
}

export default useDynamicForm