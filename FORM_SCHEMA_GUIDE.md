# DocType Form Schema Generation Guide

This guide explains how to use the comprehensive DocType system for generating dynamic forms and CRUD operations.

## 🎯 Overview

The DocType system allows you to:
1. **Define dynamic form structures** in the database
2. **Generate form schemas** for Vue frontend components
3. **Auto-generate Laravel files** (models, controllers, etc.)
4. **Handle CRUD operations** for any DocType
5. **Render dynamic forms** with validation

## 📋 Complete Workflow

### 1. Define DocType Structure

Create DocTypes with fields in the database:

```php
// In your seeder or admin panel
$product = Doctype::create([
    'name' => 'product',
    'label' => 'Product',
    'description' => 'Product catalog management',
    'is_active' => true,
    'icon' => 'cube',
    'color' => '#10b981',
]);

$product->addField([
    'fieldname' => 'name',
    'label' => 'Product Name',
    'fieldtype' => 'Data',
    'required' => true,
    'options' => json_encode([
        'placeholder' => 'Enter product name',
        'max_length' => 100
    ]),
]);

$product->addField([
    'fieldname' => 'price',
    'label' => 'Price',
    'fieldtype' => 'Currency',
    'required' => true,
    'options' => json_encode([
        'min' => 0,
        'step' => 0.01
    ]),
]);
```

### 2. Generate Form Schema

The backend automatically converts DocType fields to frontend form schema:

```php
// In controller
$generatorService = app(\Doctypes\Services\DoctypeGeneratorService::class);
$schema = $generatorService->generateFormSchema($doctype);

// Returns:
[
    [
        'name' => 'name',
        'label' => 'Product Name',
        'type' => 'text',
        'required' => true,
        'placeholder' => 'Enter product name',
        'validation' => ['maxLength' => 100]
    ],
    [
        'name' => 'price',
        'label' => 'Price',
        'type' => 'number',
        'required' => true,
        'min' => 0,
        'step' => 0.01,
        'validation' => ['min' => 0]
    ]
]
```

### 3. API Endpoints

#### Get DocType Schema
```http
GET /api/doctypes/doctypes/product/schema
```

Response:
```json
{
    "doctype": {
        "name": "product",
        "label": "Product",
        "description": "Product catalog management"
    },
    "schema": [
        {
            "name": "name",
            "label": "Product Name",
            "type": "text",
            "required": true,
            "validation": {"maxLength": 100}
        }
    ]
}
```

#### Generate Laravel Files
```http
POST /api/doctypes/doctypes/product/generate
{
    "types": ["model", "controller", "migration"],
    "force": true
}
```

#### CRUD Operations (Dynamic)
```http
# After generating files, use standard REST API
GET /api/product              # List products
POST /api/product             # Create product
GET /api/product/1            # Get product
PUT /api/product/1            # Update product
DELETE /api/product/1         # Delete product
```

### 4. Frontend Usage

#### Basic Form Component
```vue
<template>
    <GeneratedForm 
        doctype-name="product"
        @submit="handleSubmit"
    />
</template>

<script setup>
import GeneratedForm from '@/features/doctypes/pages/GeneratedForm.vue';

const handleSubmit = (data) => {
    console.log('Form submitted:', data);
};
</script>
```

#### Advanced Usage with Records
```vue
<script setup>
import { useDoctypes } from '@/features/doctypes/services/useDoctypes';

const {
    fetchDoctypeSchema,
    initializeFormData,
    validateForm,
    submitFormData,
    formData,
    formErrors
} = useDoctypes();

// Load schema
const loadSchema = async () => {
    const response = await fetchDoctypeSchema('product');
    initializeFormData(response.schema);
};

// Submit form
const handleSubmit = async () => {
    if (validateForm(schema)) {
        await submitFormData('product', formData.value);
    }
};
</script>
```

## 🔧 Field Type Mapping

Backend to Frontend field type conversion:

| Backend Type | Frontend Type | HTML Input | Additional Options |
|-------------|---------------|------------|-------------------|
| Data | text | `<input type="text">` | placeholder, maxLength |
| Long Text | textarea | `<textarea>` | rows, placeholder |
| Int/Float/Currency | number | `<input type="number">` | min, max, step |
| Check | checkbox | `<input type="checkbox">` | - |
| Select | select | `<select>` | options array |
| Date | date | `<input type="date">` | - |
| Datetime | datetime | `<input type="datetime-local">` | - |
| Attach Image | image | `<input type="file">` | accept, multiple |
| JSON | json | `<textarea>` | JSON validation |

## 🎨 Form Rendering

The `FieldRenderer.vue` component handles all field types:

```vue
<FieldRenderer 
    :field="fieldSchema"
    :model-value="formData[field.name]"
    :error="formErrors[field.name]"
    @update:model-value="updateField"
/>
```

Field schema structure:
```typescript
interface DoctypeFormSchema {
    name: string;           // Field name
    label: string;          // Display label
    type: FieldType;        // Input type
    required: boolean;      // Required validation
    description?: string;   // Help text
    placeholder?: string;   // Input placeholder
    options?: any[];        // Select options
    validation?: object;    // Validation rules
    // Type-specific options
    min?: number;
    max?: number;
    step?: number;
    rows?: number;
    accept?: string;
}
```

## 🚀 File Generation

Generate Laravel files from DocType:

```bash
# Via Artisan command
php artisan doctype:generate product --model --controller --migration

# Via API
POST /api/doctypes/doctypes/product/generate
{
    "types": ["model", "controller", "request", "resource", "migration"],
    "force": true
}
```

Generated files:
- **Model**: `app/Models/Product.php`
- **Controller**: `app/Http/Controllers/ProductController.php`
- **Request**: `app/Http/Requests/ProductRequest.php`
- **Resource**: `app/Http/Resources/ProductResource.php`
- **Migration**: `database/migrations/create_products_table.php`

## 🔍 Validation System

### Backend Validation
```php
// In DoctypeGeneratorService
$validation = $this->generateFieldValidation($field);

// Returns validation rules for Laravel
[
    'required' => true,
    'string' => true,
    'max' => 100
]
```

### Frontend Validation
```typescript
// In useDoctypes.ts
const validateForm = (schema: DoctypeFormSchema[]): boolean => {
    // Validates all fields according to schema
    // Updates formErrors ref with error messages
    return isValid;
};
```

## 📱 Demo Page

Use `DoctypeDemo.vue` to test the complete workflow:

1. **Select DocType** - Choose from available DocTypes
2. **View Schema** - See generated form schema
3. **Dynamic Form** - Interact with generated form
4. **Generate Files** - Create Laravel files
5. **CRUD Operations** - Test create/read/update/delete

## 🎯 Best Practices

### 1. Field Naming
- Use snake_case for field names
- Keep field names descriptive
- Avoid reserved keywords

### 2. Options Structure
```json
{
    "placeholder": "Enter value",
    "min": 0,
    "max": 100,
    "options": ["option1", "option2"],
    "accept": "image/*",
    "rows": 5
}
```

### 3. Validation Rules
```json
{
    "required": true,
    "minLength": 3,
    "maxLength": 50,
    "pattern": "^[a-zA-Z0-9]+$",
    "min": 0,
    "max": 999999
}
```

### 4. Error Handling
Always handle API errors gracefully:

```typescript
try {
    const response = await fetchDoctypeSchema(doctypeName);
    // Handle success
} catch (error) {
    // Show user-friendly error message
    console.error('Failed to load schema:', error);
}
```

## 🔄 Integration Examples

### With Existing Laravel Project
1. Install the package
2. Run migrations
3. Seed sample DocTypes
4. Publish frontend assets
5. Use GeneratedForm in your Vue components

### Custom Field Types
Extend the field type mapping by updating:
- `mapFieldTypeToFormType()` in `DoctypeGeneratorService`
- Field rendering logic in `FieldRenderer.vue`
- Validation rules in both backend and frontend

## 📚 API Reference

### Core Endpoints
- `GET /api/doctypes/doctypes` - List all DocTypes
- `GET /api/doctypes/doctypes/{name}/schema` - Get form schema
- `POST /api/doctypes/doctypes/{name}/generate` - Generate files
- `GET /api/{doctype}` - List records (after generation)
- `POST /api/{doctype}` - Create record
- `PUT /api/{doctype}/{id}` - Update record
- `DELETE /api/{doctype}/{id}` - Delete record

### Frontend Composables
- `useDoctypes()` - Main composable for DocType operations
- `fetchDoctypeSchema()` - Get form schema
- `generateDoctypeFiles()` - Generate Laravel files
- `initializeFormData()` - Initialize form with defaults
- `validateForm()` - Validate form data
- `submitFormData()` - Submit form to API

This comprehensive system provides a complete solution for dynamic form generation and CRUD operations based on DocType configurations.
