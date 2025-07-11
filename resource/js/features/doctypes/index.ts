/**
 * DocTypes Package - TypeScript Exports
 * 
 * This file exports all types, composables, and utilities for the DocTypes package.
 * Import from this file to get access to all DocTypes functionality.
 */

// Core Types
export type {
    // Main interfaces
    Doctype,
    DoctypeField,
    DoctypeSettings,
    DoctypePermissions,
    DoctypeFieldOptions,
    SelectOption,
    FieldType,

    // Form Schema Types
    DoctypeFormSchema,
    FormField,
    FormLayout,
    FormSection,
    FormTab,
    FormSettings,
    FormFieldValidation,

    // API Request/Response Types
    DoctypeCreateRequest,
    DoctypeUpdateRequest,
    DoctypeListResponse,
    DoctypeResponse,
    DoctypeSchemaResponse,
    DoctypeGeneratorResponse,
    PaginationMeta,
    PaginationLink,
    DoctypeFilters,

    // Generator Types
    GeneratedFile,
    FileGenerationRequest,
    FileGenerationOptions,

    // Validation Types
    FieldValidationRule,
    FormFieldConfig,
    GeneratedFormData,
    FormValidationErrors,

    // Dynamic Form Types
    DynamicFormData,
    FormErrors,

    // Dynamic Model CRUD Types
    DynamicModelResponse,
    DynamicModelListResponse,
    DynamicModelCreateRequest,
    DynamicModelUpdateRequest,
    DynamicModelFilters,

    // Vue Composable Types
    UseDoctypesState,
    UseDoctypesActions,
    UseDynamicFormState,
    UseDynamicFormActions,

    // Component Types
    FieldRendererProps,
    FieldRendererEmits,

    // Form Builder Types
    FormBuilderState,
    FormBuilderActions,
    FieldTypeOption,

    // Import/Export Types
    DoctypeExportData,
    DoctypeImportRequest,
    DoctypeImportResponse,

    // Search Types
    SearchResult,
    SearchRequest,
    SearchResponse,

    // Plugin Types
    DoctypePlugin,
    DoctypeHooks,

    // Utility Types
    DoctypeEventType,
    DoctypeEvent,
    ApiError,
    ValidationError,
} from './types/doctype';

// Composables
export { useDoctypes } from './services/useDoctypes';
export { useDynamicForm } from './services/useDynamicForm';

// Field Type Configuration
export {
    FIELD_TYPES,
    FIELD_VALIDATION_RULES,
    FIELD_CATEGORIES,
    DEFAULT_FIELD_PROPERTIES,
    getFieldTypeConfig,
    getFieldTypesByCategory,
    getDefaultFieldOptions,
    hasFieldOptions,
    getValidationMessage,
    createNewField,
    getFieldTypesGrouped,
    searchFieldTypes,
} from './config/fieldTypes';

// Utility Functions
import type { DoctypeField, Doctype } from './types/doctype';

export const DocTypeUtils = {
    /**
     * Validate field name format
     */
    validateFieldName: (name: string): boolean => {
        return /^[a-z][a-z0-9_]*$/.test(name);
    },

    /**
     * Validate doctype name format
     */
    validateDoctypeName: (name: string): boolean => {
        return /^[a-z][a-z0-9_]*$/.test(name);
    },

    /**
     * Convert label to field name
     */
    labelToFieldName: (label: string): string => {
        return label
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '_')
            .replace(/^_+|_+$/g, '');
    },

    /**
     * Convert field name to label
     */
    fieldNameToLabel: (fieldName: string): string => {
        return fieldName
            .split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    },

    /**
     * Generate table name from doctype name
     */
    generateTableName: (doctypeName: string): string => {
        return doctypeName.toLowerCase().replace(/_/g, '_');
    },

    /**
     * Generate model class name from doctype name
     */
    generateClassName: (doctypeName: string): string => {
        return doctypeName
            .split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join('');
    },

    /**
     * Validate JSON string
     */
    isValidJSON: (str: string): boolean => {
        try {
            JSON.parse(str);
            return true;
        } catch {
            return false;
        }
    },

    /**
     * Format file size in bytes to human readable
     */
    formatFileSize: (bytes: number): string => {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    /**
     * Deep clone an object
     */
    deepClone: <T>(obj: T): T => {
        return JSON.parse(JSON.stringify(obj));
    },

    /**
     * Compare two objects for equality
     */
    isEqual: (obj1: any, obj2: any): boolean => {
        return JSON.stringify(obj1) === JSON.stringify(obj2);
    },

    /**
     * Generate unique field name
     */
    generateUniqueFieldName: (baseName: string, existingFields: DoctypeField[]): string => {
        const existingNames = existingFields.map(f => f.fieldname);
        let counter = 1;
        let fieldName = baseName;

        while (existingNames.includes(fieldName)) {
            fieldName = `${baseName}_${counter}`;
            counter++;
        }

        return fieldName;
    },

    /**
     * Sort fields by sort_order
     */
    sortFields: (fields: DoctypeField[]): DoctypeField[] => {
        return [...fields].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));
    },

    /**
     * Get field by name
     */
    getFieldByName: (fields: DoctypeField[], fieldName: string): DoctypeField | undefined => {
        return fields.find(f => f.fieldname === fieldName);
    },

    /**
     * Get required fields
     */
    getRequiredFields: (fields: DoctypeField[]): DoctypeField[] => {
        return fields.filter(f => f.required);
    },

    /**
     * Get list view fields
     */
    getListViewFields: (fields: DoctypeField[]): DoctypeField[] => {
        return fields.filter(f => f.in_list_view);
    },

    /**
     * Get filterable fields
     */
    getFilterableFields: (fields: DoctypeField[]): DoctypeField[] => {
        return fields.filter(f => f.in_standard_filter);
    },

    /**
     * Validate field data
     */
    validateFieldData: (field: DoctypeField): string[] => {
        const errors: string[] = [];

        if (!field.fieldname) {
            errors.push('Field name is required');
        } else if (!DocTypeUtils.validateFieldName(field.fieldname)) {
            errors.push('Field name must start with a letter and contain only lowercase letters, numbers, and underscores');
        }

        if (!field.label) {
            errors.push('Field label is required');
        }

        if (!field.fieldtype) {
            errors.push('Field type is required');
        }

        return errors;
    },

    /**
     * Validate doctype data
     */
    validateDoctypeData: (doctype: Partial<Doctype>): string[] => {
        const errors: string[] = [];

        if (!doctype.name) {
            errors.push('Doctype name is required');
        } else if (!DocTypeUtils.validateDoctypeName(doctype.name)) {
            errors.push('Doctype name must start with a letter and contain only lowercase letters, numbers, and underscores');
        }

        if (!doctype.label) {
            errors.push('Doctype label is required');
        }

        if (!doctype.fields || doctype.fields.length === 0) {
            errors.push('At least one field is required');
        } else {
            doctype.fields.forEach((field, index) => {
                const fieldErrors = DocTypeUtils.validateFieldData(field);
                fieldErrors.forEach(error => {
                    errors.push(`Field ${index + 1}: ${error}`);
                });
            });

            // Check for duplicate field names
            const fieldNames = doctype.fields.map(f => f.fieldname).filter(Boolean);
            const duplicates = fieldNames.filter((name, index) => fieldNames.indexOf(name) !== index);
            if (duplicates.length > 0) {
                errors.push(`Duplicate field names: ${duplicates.join(', ')}`);
            }
        }

        return errors;
    }
};

// Re-export types with better names for common use cases
import type {
    Doctype as DocTypeDefinition,
    DoctypeField as DocTypeField,
    DoctypeFormSchema as FormSchema,
    FormField as SchemaField,
    DynamicFormData as FormDataType,
    FieldType as SupportedFieldType,
} from './types/doctype';

export type {
    DocTypeDefinition,
    DocTypeField,
    FormSchema,
    SchemaField,
    FormDataType,
    SupportedFieldType,
};

// Constants
export const DOCTYPE_CONSTANTS = {
    MAX_FIELD_NAME_LENGTH: 64,
    MAX_LABEL_LENGTH: 140,
    MAX_DESCRIPTION_LENGTH: 500,
    MAX_FIELDS_PER_DOCTYPE: 200,
    SUPPORTED_FILE_TYPES: {
        images: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        documents: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        archives: ['zip', 'rar', '7z', 'tar', 'gz'],
        videos: ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
        audio: ['mp3', 'wav', 'ogg', 'flac', 'aac']
    },
    DEFAULT_PAGINATION: {
        per_page: 15,
        max_per_page: 100
    }
} as const;
