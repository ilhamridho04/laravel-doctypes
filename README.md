# NgodingSkuyy DocTypes 🚀

**Dynamic DocType system for Laravel 12 + Vue 3 + Tailwind v4 + shadcn-vue**

A comprehensive package that brings Frappe-like DocType functionality to Laravel applications, featuring dynamic field management, form generation, and stub-based code generation.

## ✨ Features

🎯 **Dynamic Field Management**
- Create and manage field types through UI
- Support for 13+ field types (text, select, checkbox, date, file, etc.)
- Real-time form schema generation
- JSON metadata storage in database

🔧 **Code Generation**  
- Stub-based Laravel file generation (Model, Controller, Request, Resource, Migration)
- Artisan commands for rapid prototyping
- Dynamic model creation with proper relationships

🎨 **Modern Frontend**
- Vue 3 with Composition API and `<script setup>`
- TypeScript support with comprehensive type definitions
- Tailwind v4 and shadcn-vue components
- Responsive and accessible UI

📊 **Complete CRUD System**
- RESTful API with Laravel resources
- Dynamic model controllers for generated entities
- Pagination, filtering, and search
- Form validation and error handling

## 🚀 Quick Start

### Installation

```bash
# Install the package
composer require ngodingskuyy/doctypes

# Publish assets and config
php artisan vendor:publish --provider="NgodingSkuyy\Doctypes\Providers\DoctypeServiceProvider"

# Run migrations
php artisan migrate

# Seed demo data
php artisan doctype:demo
```

### Create Your First DocType

1. **Via UI**: Navigate to `/doctypes/create` and build your DocType visually
2. **Via Code**: Use the demo command to see examples

```bash
# See comprehensive examples
php artisan doctype:demo

# Generate Laravel files from DocType  
php artisan doctype:demo --generate
```

### Frontend Setup

```vue
<!-- In your Vue app -->
<template>
  <div>
    <!-- List all doctypes -->
    <DoctypeList />
    
    <!-- Create/edit doctype with fields -->
    <DoctypeForm />
    
    <!-- Render dynamic form from schema -->
    <GeneratedForm doctype-name="Customer" @submit="handleSubmit" />
  </div>
</template>

<script setup>
import { DoctypeList, DoctypeForm, GeneratedForm } from '@/features/doctypes';
</script>
```

## 📋 Field Types Supported

| Type | Description | Example |
|------|-------------|---------|
| `text` | Single line text | Name, Title |
| `textarea` | Multi-line text | Description, Notes |
| `number` | Numeric input | Age, Price, Quantity |
| `email` | Email validation | user@example.com |
| `password` | Password field | Hidden input |
| `select` | Dropdown options | Status, Category |
| `checkbox` | Boolean field | Is Active, Featured |
| `date` | Date picker | Birth Date, Due Date |
| `datetime` | Date and time | Created At, Event Time |
| `time` | Time picker | Meeting Time |
| `file` | File upload | Documents, Attachments |
| `image` | Image upload | Avatar, Gallery |
| `json` | JSON data | Settings, Metadata |

## 🔧 API Usage

### Create DocType with Fields

```php
POST /api/doctypes/doctypes
{
  "name": "Customer",
  "label": "Customer Management", 
  "description": "CRM system",
  "fields": [
    {
      "fieldname": "first_name",
      "label": "First Name",
      "fieldtype": "text",
      "required": true,
      "in_list_view": true
    },
    {
      "fieldname": "status", 
      "label": "Status",
      "fieldtype": "select",
      "options": "Active\nInactive",
      "required": true
    }
  ]
}
```

### Get Form Schema

```php
GET /api/doctypes/doctypes/Customer/schema
{
  "data": {
    "doctype": "Customer",
    "title": "Customer Management",
    "fields": [
      {
        "name": "first_name",
        "label": "First Name", 
        "type": "text",
        "required": true
      }
    ]
  }
}
```

### Generate Laravel Files

```php
POST /api/doctypes/doctypes/Customer/generate
{
  "types": ["model", "controller", "request", "resource", "migration"],
  "force": false
}
```

## 🎨 Frontend Components

### DoctypeForm - Visual Field Builder

```vue
<template>
  <DoctypeForm 
    :doctype-id="id" 
    @saved="handleSaved"
    @cancelled="handleCancel" 
  />
</template>
```

**Features:**
- Drag & drop field reordering  
- Real-time field preview
- Comprehensive field options
- Validation rules configuration

### GeneratedForm - Dynamic Form Renderer

```vue
<template>
  <GeneratedForm
    doctype-name="Customer"
    :initial-data="customerData"
    @submit="saveCustomer"
    @field-changed="handleFieldChange"
  />
</template>
```

**Features:**
- Automatic form generation from schema
- Client-side validation
- Field dependency support
- Custom field renderers

### FieldRenderer - Individual Field Types

```vue
<template>
  <FieldRenderer
    :field="{ name: 'status', type: 'select', options: ['Active', 'Inactive'] }"
    :modelValue="formData.status"
    @update:modelValue="updateField"
  />
</template>
```

## 🛠️ Code Generation Examples

### Generate Customer Model

```bash
php artisan doctype:generate Customer --type=model
```

```php
// Generated: app/Models/Customer.php
class Customer extends Model {
    protected $fillable = ['first_name', 'last_name', 'email', 'status'];
    
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];
}
```

### Generate Controller with CRUD

```bash
php artisan doctype:generate Customer --type=controller
```

```php
// Generated: app/Http/Controllers/CustomerController.php  
class CustomerController extends Controller {
    public function index() { /* ... */ }
    public function store(CustomerRequest $request) { /* ... */ }
    public function show(Customer $customer) { /* ... */ }
    // ... complete CRUD operations
}
```

## 📚 Advanced Usage

### Custom Field Validation

```php
// In your DocType definition
$doctype->addField([
    'fieldname' => 'email',
    'fieldtype' => 'email', 
    'validation' => [
        'rules' => 'required|email|unique:customers,email',
        'messages' => [
            'email.unique' => 'This email is already registered'
        ]
    ]
]);
```

### Dynamic Model Relationships

```php
// Define relationships in field metadata
$doctype->addField([
    'fieldname' => 'category_id',
    'fieldtype' => 'select',
    'relationship' => [
        'model' => 'App\\Models\\Category',
        'type' => 'belongsTo',
        'display' => 'name'
    ]
]);
```

### Frontend Type Safety

```typescript
// Full TypeScript support
import type { Doctype, DoctypeField, DoctypeFormSchema } from './types/doctype';

const { doctypes, createDoctype, getFormSchema } = useDoctypes();

// Type-safe API calls
const newDoctype: Doctype = await createDoctype({
  name: 'Product',
  label: 'Product Management',
  fields: [/* typed field definitions */]
});
```

## 🔍 Development & Debugging

### Available Artisan Commands

```bash
# Demo and examples
php artisan doctype:demo                    # Show demo doctypes
php artisan doctype:demo --generate         # Generate files for demo data  
php artisan doctype:demo --reset           # Reset demo data

# Code generation
php artisan doctype:generate Customer       # Generate all files
php artisan doctype:generate Customer --type=model,controller
php artisan doctype:generate Customer --force # Overwrite existing files

# Seeding
php artisan db:seed --class="Doctypes\Database\Seeders\ComprehensiveDoctypeSeeder"
```

### Package Structure

```
doctypes/
├── src/                          # Laravel backend
│   ├── Models/                   # Doctype & DoctypeField models
│   ├── Http/Controllers/         # API controllers
│   ├── Services/                 # Generator service
│   ├── Console/Commands/         # Artisan commands
│   └── routes/api.php           # API routes
├── resource/js/features/doctypes/ # Vue frontend  
│   ├── pages/                   # Vue pages
│   ├── components/              # Vue components
│   ├── services/                # API services
│   └── types/                   # TypeScript definitions
├── database/                     # Migrations & seeders
└── stubs/                       # Code generation templates
```

## 📖 Documentation

- [📋 Quick Start Guide](QUICK_START.md) - Get started in 5 minutes
- [🔧 Generator Examples](GENERATOR_EXAMPLES.md) - Code generation samples  
- [🎯 Field Management Demo](FIELD_MANAGEMENT_DEMO.md) - Complete workflow
- [🐛 Troubleshooting](docs/TROUBLESHOOTING.md) - Common issues & solutions

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🙋‍♂️ Support

- **Issues**: [GitHub Issues](https://github.com/ngodingskuyy/doctypes/issues)
- **Discussions**: [GitHub Discussions](https://github.com/ngodingskuyy/doctypes/discussions)
- **Email**: support@ngodingskuyy.com

---

**Made with ❤️ by NgodingSkuyy Team**

*Dynamic DocTypes for Modern Laravel Applications*
