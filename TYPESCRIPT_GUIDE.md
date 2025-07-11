# TypeScript Types Guide

This document explains the comprehensive TypeScript types available in the DocTypes package and how to use them effectively.

## 🏗️ Core Types

### DoctypeField
Represents a field definition in a DocType:

```typescript
import type { DoctypeField, FieldType } from '../types/doctype';

const userNameField: DoctypeField = {
    fieldname: 'username',
    label: 'Username',
    fieldtype: 'text',
    required: true,
    unique: true,
    in_list_view: true,
    description: 'Unique username for login',
    options: {
        minLength: 3,
        maxLength: 50,
        pattern: '^[a-zA-Z0-9_]+$',
        placeholder: 'Enter username'
    }
};
```

### Doctype
Represents a complete DocType definition:

```typescript
import type { Doctype, DoctypeSettings } from '../types/doctype';

const userDoctype: Doctype = {
    name: 'user_profile',
    label: 'User Profile',
    description: 'User account information',
    icon: 'user',
    color: '#3b82f6',
    is_active: true,
    fields: [userNameField, emailField, ...],
    settings: {
        auto_name: 'field',
        track_changes: true,
        is_submittable: false
    }
};
```

## 📝 Form Schema Types

### DoctypeFormSchema
Used for dynamic form generation:

```typescript
import type { DoctypeFormSchema, FormField } from '../types/doctype';

const formSchema: DoctypeFormSchema = {
    doctype: 'user_profile',
    title: 'User Profile',
    description: 'Create or edit user profile',
    fields: [
        {
            name: 'username',
            label: 'Username',
            type: 'text',
            required: true,
            placeholder: 'Enter username',
            validation: {
                required: true,
                minLength: 3,
                pattern: '^[a-zA-Z0-9_]+$'
            }
        }
    ],
    layout: {
        type: 'grid',
        columns: 2,
        sections: [
            {
                title: 'Basic Information',
                fields: ['username', 'email', 'full_name']
            }
        ]
    }
};
```

## 🔧 API Types

### Create/Update Requests
```typescript
import type { DoctypeCreateRequest, DoctypeUpdateRequest } from '../types/doctype';

const createRequest: DoctypeCreateRequest = {
    name: 'customer',
    label: 'Customer',
    description: 'Customer information management',
    fields: [...],
    settings: {
        auto_name: 'prompt',
        track_changes: true
    },
    is_active: true
};

const updateRequest: DoctypeUpdateRequest = {
    id: 1,
    label: 'Updated Customer',
    description: 'Updated description'
};
```

### API Responses
```typescript
import type { 
    DoctypeListResponse, 
    DoctypeResponse, 
    DoctypeSchemaResponse 
} from '../types/doctype';

// List response
const listResponse: DoctypeListResponse = {
    success: true,
    data: [...],
    meta: {
        current_page: 1,
        last_page: 5,
        per_page: 15,
        total: 75,
        from: 1,
        to: 15
    }
};

// Single item response
const itemResponse: DoctypeResponse = {
    success: true,
    data: userDoctype,
    message: 'DocType retrieved successfully'
};

// Schema response
const schemaResponse: DoctypeSchemaResponse = {
    success: true,
    data: {
        doctype: userDoctype,
        schema: formSchema
    }
};
```

## 🎯 Vue Composable Types

### useDoctypes Composable
```typescript
import type { UseDoctypesState, UseDoctypesActions } from '../types/doctype';

// In your composable
export const useDoctypes = (): UseDoctypesState & UseDoctypesActions => {
    // State
    const doctypes = ref<Doctype[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    
    // Actions
    const fetchDoctypes = async (filters?: DoctypeFilters) => {
        // Implementation
    };
    
    return {
        // State
        doctypes,
        loading,
        error,
        // Actions
        fetchDoctypes,
        // ... other methods
    };
};
```

### useDynamicForm Composable
```typescript
import type { UseDynamicFormState, UseDynamicFormActions } from '../types/doctype';

export const useDynamicForm = (): UseDynamicFormState & UseDynamicFormActions => {
    const formData = ref<DynamicFormData>({});
    const schema = ref<DoctypeFormSchema | null>(null);
    const errors = ref<FormErrors>({});
    
    const loadSchema = async (doctypeName: string) => {
        // Load schema from API
    };
    
    const validateForm = (): boolean => {
        // Validation logic
        return true;
    };
    
    return {
        formData,
        schema,
        errors,
        loadSchema,
        validateForm,
        // ... other methods
    };
};
```

## 🖼️ Component Props & Emits

### FieldRenderer Component
```typescript
import type { FieldRendererProps, FieldRendererEmits } from '../types/doctype';

// Component definition
defineProps<FieldRendererProps>();
defineEmits<FieldRendererEmits>();

// Usage
<FieldRenderer 
    :field="field" 
    v-model="formData[field.name]"
    :error="errors[field.name]"
    @change="handleFieldChange"
/>
```

### Form Builder Component
```typescript
import type { FormBuilderState, FormBuilderActions } from '../types/doctype';

const useFormBuilder = (): FormBuilderState & FormBuilderActions => {
    const doctype = ref<Partial<Doctype>>({});
    const fields = ref<DoctypeField[]>([]);
    
    const addField = (type: FieldType = 'text') => {
        fields.value.push({
            fieldname: '',
            label: '',
            fieldtype: type,
            required: false,
            sort_order: fields.value.length
        });
    };
    
    return {
        doctype,
        fields,
        addField,
        // ... other methods
    };
};
```

## 🔄 Generator Types

### File Generation
```typescript
import type { 
    FileGenerationRequest, 
    DoctypeGeneratorResponse 
} from '../types/doctype';

const generateFiles = async (): Promise<DoctypeGeneratorResponse> => {
    const request: FileGenerationRequest = {
        doctype: 'customer',
        types: ['model', 'controller', 'migration'],
        options: {
            force: true,
            module: 'CRM',
            with_seeder: true
        }
    };
    
    const response = await api.post('/api/doctypes/generate', request);
    return response.data;
};
```

## 🔍 Search & Filter Types

### Dynamic Model CRUD
```typescript
import type { 
    DynamicModelCreateRequest,
    DynamicModelFilters,
    DynamicModelListResponse 
} from '../types/doctype';

// Create record
const createRecord = async (data: Record<string, any>) => {
    const request: DynamicModelCreateRequest = {
        doctype: 'customer',
        data: {
            name: 'John Doe',
            email: 'john@example.com',
            phone: '+1234567890'
        }
    };
    
    return await api.post('/api/dynamic-models', request);
};

// List records with filters
const listRecords = async () => {
    const filters: DynamicModelFilters = {
        doctype: 'customer',
        search: 'john',
        search_fields: ['name', 'email'],
        filters: {
            active: true
        },
        sort_by: 'created_at',
        sort_order: 'desc',
        per_page: 20
    };
    
    return await api.get('/api/dynamic-models', { params: filters });
};
```

## 🛡️ Error Handling

### Validation & Errors
```typescript
import type { 
    ApiError, 
    ValidationError, 
    FormErrors 
} from '../types/doctype';

const handleApiError = (error: ApiError) => {
    if (error.errors) {
        // Handle validation errors
        const formErrors: FormErrors = {};
        Object.entries(error.errors).forEach(([field, messages]) => {
            formErrors[field] = messages[0]; // Take first message
        });
        return formErrors;
    }
    
    // Handle general error
    console.error(error.message);
    return {};
};
```

## 🔌 Plugin System

### Custom Field Types
```typescript
import type { FieldTypeOption, DoctypePlugin } from '../types/doctype';

const customPlugin: DoctypePlugin = {
    name: 'CustomFields',
    version: '1.0.0',
    description: 'Custom field types plugin',
    fieldTypes: [
        {
            value: 'rating',
            label: 'Star Rating',
            description: 'Star rating field (1-5)',
            category: 'advanced',
            hasOptions: true,
            defaultOptions: {
                min: 1,
                max: 5,
                step: 1
            }
        }
    ],
    hooks: {
        beforeSave: async (doctype) => {
            // Custom validation
            return doctype;
        }
    }
};
```

## 📚 Best Practices

1. **Always use proper types** for function parameters and return values
2. **Leverage type guards** for runtime type checking
3. **Use generics** for reusable components and functions
4. **Define custom types** for complex business logic
5. **Export all types** from the main types file
6. **Document types** with JSDoc comments
7. **Use strict TypeScript** configuration for better type safety

## 🎯 Common Patterns

### Type-safe API calls
```typescript
const apiCall = async <T>(url: string, options?: RequestInit): Promise<T> => {
    const response = await fetch(url, options);
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }
    return response.json();
};

// Usage
const doctype = await apiCall<DoctypeResponse>('/api/doctypes/1');
```

### Conditional field rendering
```typescript
const shouldShowField = (field: FormField, formData: DynamicFormData): boolean => {
    if (!field.depends_on) return true;
    
    // Parse depends_on condition
    const [fieldName, operator, value] = field.depends_on.split(' ');
    const fieldValue = formData[fieldName];
    
    switch (operator) {
        case '=':
            return fieldValue === value;
        case '!=':
            return fieldValue !== value;
        default:
            return true;
    }
};
```

This comprehensive type system ensures type safety across the entire DocTypes package while providing excellent developer experience with IntelliSense and compile-time error checking.
