import type { FieldTypeOption, FieldType, DoctypeFieldOptions } from '../types/doctype';

/**
 * Complete field type definitions for the DocTypes system
 * This configuration drives the field type selector and default options
 */
export const FIELD_TYPES: FieldTypeOption[] = [
    // Basic Text Fields
    {
        value: 'text',
        label: 'Text',
        description: 'Single line text input',
        icon: 'text',
        category: 'basic',
        hasOptions: true,
        defaultOptions: {
            placeholder: 'Enter text...',
            maxLength: 255
        }
    },
    {
        value: 'textarea',
        label: 'Textarea',
        description: 'Multi-line text input',
        icon: 'document-text',
        category: 'basic',
        hasOptions: true,
        defaultOptions: {
            rows: 3,
            placeholder: 'Enter text...',
            maxLength: 1000
        }
    },
    {
        value: 'email',
        label: 'Email',
        description: 'Email address input with validation',
        icon: 'at-symbol',
        category: 'basic',
        hasOptions: true,
        defaultOptions: {
            placeholder: 'user@example.com'
        }
    },
    {
        value: 'password',
        label: 'Password',
        description: 'Password input field',
        icon: 'lock-closed',
        category: 'basic',
        hasOptions: true,
        defaultOptions: {
            minLength: 8,
            placeholder: 'Enter password...'
        }
    },

    // Numeric Fields
    {
        value: 'number',
        label: 'Number',
        description: 'Numeric input with validation',
        icon: 'hashtag',
        category: 'basic',
        hasOptions: true,
        defaultOptions: {
            min: 0,
            step: 1,
            placeholder: '0'
        }
    },

    // Selection Fields
    {
        value: 'select',
        label: 'Select',
        description: 'Dropdown selection field',
        icon: 'chevron-down',
        category: 'basic',
        hasOptions: true,
        defaultOptions: {
            options: ['Option 1', 'Option 2', 'Option 3'],
            placeholder: 'Select an option...'
        }
    },
    {
        value: 'checkbox',
        label: 'Checkbox',
        description: 'Boolean checkbox field',
        icon: 'check-square',
        category: 'basic',
        hasOptions: false,
        defaultOptions: {}
    },

    // Date and Time Fields
    {
        value: 'date',
        label: 'Date',
        description: 'Date picker field',
        icon: 'calendar',
        category: 'advanced',
        hasOptions: true,
        defaultOptions: {
            format: 'YYYY-MM-DD'
        }
    },
    {
        value: 'datetime',
        label: 'DateTime',
        description: 'Date and time picker',
        icon: 'clock',
        category: 'advanced',
        hasOptions: true,
        defaultOptions: {
            format: 'YYYY-MM-DD HH:mm:ss',
            show_time: true
        }
    },
    {
        value: 'time',
        label: 'Time',
        description: 'Time picker field',
        icon: 'clock',
        category: 'advanced',
        hasOptions: true,
        defaultOptions: {
            format: 'HH:mm:ss'
        }
    },

    // File Fields
    {
        value: 'file',
        label: 'File',
        description: 'File upload field',
        icon: 'document',
        category: 'advanced',
        hasOptions: true,
        defaultOptions: {
            accept: '*',
            maxSize: 10, // MB
            multiple: false
        }
    },
    {
        value: 'image',
        label: 'Image',
        description: 'Image upload field',
        icon: 'photograph',
        category: 'advanced',
        hasOptions: true,
        defaultOptions: {
            accept: 'image/*',
            maxSize: 5, // MB
            multiple: false
        }
    },

    // Special Fields
    {
        value: 'json',
        label: 'JSON',
        description: 'JSON data field',
        icon: 'code',
        category: 'special',
        hasOptions: true,
        defaultOptions: {
            rows: 5,
            placeholder: '{\n  "key": "value"\n}'
        }
    }
];

/**
 * Get field type configuration by value
 */
export const getFieldTypeConfig = (fieldType: FieldType): FieldTypeOption | undefined => {
    return FIELD_TYPES.find(type => type.value === fieldType);
};

/**
 * Get field types by category
 */
export const getFieldTypesByCategory = (category: 'basic' | 'advanced' | 'special'): FieldTypeOption[] => {
    return FIELD_TYPES.filter(type => type.category === category);
};

/**
 * Get default options for a field type
 */
export const getDefaultFieldOptions = (fieldType: FieldType): Partial<DoctypeFieldOptions> => {
    const config = getFieldTypeConfig(fieldType);
    return config?.defaultOptions || {};
};

/**
 * Check if a field type supports custom options
 */
export const hasFieldOptions = (fieldType: FieldType): boolean => {
    const config = getFieldTypeConfig(fieldType);
    return config?.hasOptions || false;
};

/**
 * Validation rules for each field type
 */
export const FIELD_VALIDATION_RULES = {
    text: {
        required: 'This field is required',
        minLength: 'Text must be at least {value} characters',
        maxLength: 'Text must not exceed {value} characters',
        pattern: 'Text format is invalid'
    },
    textarea: {
        required: 'This field is required',
        minLength: 'Text must be at least {value} characters',
        maxLength: 'Text must not exceed {value} characters'
    },
    email: {
        required: 'Email is required',
        type: 'Must be a valid email address'
    },
    password: {
        required: 'Password is required',
        minLength: 'Password must be at least {value} characters',
        pattern: 'Password does not meet requirements'
    },
    number: {
        required: 'Number is required',
        min: 'Number must be at least {value}',
        max: 'Number must not exceed {value}',
        type: 'Must be a valid number'
    },
    select: {
        required: 'Please select an option'
    },
    checkbox: {
        required: 'This field must be checked'
    },
    date: {
        required: 'Date is required',
        type: 'Must be a valid date'
    },
    datetime: {
        required: 'Date and time is required',
        type: 'Must be a valid date and time'
    },
    time: {
        required: 'Time is required',
        type: 'Must be a valid time'
    },
    file: {
        required: 'File is required',
        accept: 'File type not allowed',
        maxSize: 'File size must not exceed {value}MB'
    },
    image: {
        required: 'Image is required',
        accept: 'Must be an image file',
        maxSize: 'Image size must not exceed {value}MB'
    },
    json: {
        required: 'JSON data is required',
        type: 'Must be valid JSON format'
    }
};

/**
 * Get validation rule message for a field type and rule
 */
export const getValidationMessage = (
    fieldType: FieldType,
    rule: string,
    value?: any
): string => {
    const rules = FIELD_VALIDATION_RULES[fieldType] as Record<string, string> || {};
    const template = rules[rule] || 'Invalid value';

    return template.replace('{value}', String(value || ''));
};

/**
 * Default field properties for new fields
 */
export const DEFAULT_FIELD_PROPERTIES = {
    fieldname: '',
    label: '',
    fieldtype: 'text' as FieldType,
    required: false,
    unique: false,
    in_list_view: false,
    in_standard_filter: false,
    description: '',
    sort_order: 0,
    read_only: false,
    hidden: false
};

/**
 * Create a new field with default properties
 */
export const createNewField = (
    fieldType: FieldType = 'text',
    overrides: Partial<any> = {}
): any => {
    const defaultOptions = getDefaultFieldOptions(fieldType);

    return {
        ...DEFAULT_FIELD_PROPERTIES,
        fieldtype: fieldType,
        options: defaultOptions,
        ...overrides
    };
};

/**
 * Field categories with descriptions
 */
export const FIELD_CATEGORIES = {
    basic: {
        label: 'Basic Fields',
        description: 'Common input fields for most use cases',
        icon: 'collection'
    },
    advanced: {
        label: 'Advanced Fields',
        description: 'Specialized fields for complex data types',
        icon: 'cog'
    },
    special: {
        label: 'Special Fields',
        description: 'Custom and specialized field types',
        icon: 'star'
    }
};

/**
 * Get all available field types grouped by category
 */
export const getFieldTypesGrouped = () => {
    return {
        basic: getFieldTypesByCategory('basic'),
        advanced: getFieldTypesByCategory('advanced'),
        special: getFieldTypesByCategory('special')
    };
};

/**
 * Search field types by label or description
 */
export const searchFieldTypes = (query: string): FieldTypeOption[] => {
    const lowerQuery = query.toLowerCase();
    return FIELD_TYPES.filter(type =>
        type.label.toLowerCase().includes(lowerQuery) ||
        type.description.toLowerCase().includes(lowerQuery)
    );
};
