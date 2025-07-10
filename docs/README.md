# DocTypes Package

Dynamic DocType system for Laravel - Create Frappe-like dynamic document types with Vue.js frontend.

## Features

- 🚀 **Dynamic Document Types**: Create custom document types with configurable fields
- 🎨 **Vue.js Frontend**: Beautiful, responsive UI components with shadcn-vue
- 📝 **Form Builder**: Generate forms dynamically based on DocType configuration
- 🔧 **Code Generation**: Auto-generate Laravel models, controllers, requests, and resources
- 🎯 **Field Types**: Support for text, number, email, select, date, checkbox, and more
- 🔍 **Filtering & Search**: Built-in filtering and search capabilities
- 📊 **List Views**: Configurable list views with sortable columns
- 🎨 **Customizable**: Icons, colors, and themes for each DocType
- 🔐 **Validation**: Comprehensive validation rules for each field type

## Installation

1. Install the package via Composer:

```bash
composer require ngodingskuyy/doctypes
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --tag="doctypes-config"
```

3. Run the migrations:

```bash
php artisan migrate
```

4. (Optional) Seed sample data:

```bash
php artisan db:seed --class=DoctypeSeeder
```

## Frontend Setup

### Vue.js Components

The package includes Vue.js components for managing DocTypes:

- `DoctypeList.vue` - List and manage DocTypes
- `DoctypeForm.vue` - Create and edit DocTypes
- `GeneratedForm.vue` - Dynamic form generator
- `FieldRenderer.vue` - Individual field renderer

### Example Usage

```vue
<template>
  <div>
    <!-- List all DocTypes -->
    <DoctypeList
      @create="showCreateForm"
      @edit="showEditForm"
      @generateForm="showGeneratedForm"
    />

    <!-- Create/Edit DocType -->
    <DoctypeForm
      v-if="showForm"
      :doctype="selectedDoctype"
      @saved="handleSaved"
      @cancel="hideForm"
    />

    <!-- Generated Form -->
    <GeneratedForm
      v-if="showGenerated"
      :doctype-id="selectedDoctypeId"
      @submit="handleFormSubmit"
      @cancel="hideGenerated"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { DoctypeList, DoctypeForm, GeneratedForm } from '@/features/doctypes'

const showForm = ref(false)
const showGenerated = ref(false)
const selectedDoctype = ref(null)
const selectedDoctypeId = ref(null)

const showCreateForm = () => {
  selectedDoctype.value = null
  showForm.value = true
}

const showEditForm = (doctype) => {
  selectedDoctype.value = doctype
  showForm.value = true
}

const showGeneratedForm = (doctype) => {
  selectedDoctypeId.value = doctype.id
  showGenerated.value = true
}

const handleSaved = (doctype) => {
  showForm.value = false
  // Refresh list or handle saved doctype
}

const handleFormSubmit = (formData) => {
  console.log('Form submitted:', formData)
  // Handle form submission
}
</script>
```

## API Usage

### Creating a DocType

```javascript
// Using the useDoctypes composable
import { useDoctypes } from '@/features/doctypes/services/useDoctypes'

const { createDoctype } = useDoctypes()

const newDoctype = {
  name: 'customer',
  label: 'Customer',
  description: 'Customer information',
  fields: [
    {
      fieldname: 'name',
      label: 'Customer Name',
      fieldtype: 'text',
      required: true,
      in_list_view: true
    },
    {
      fieldname: 'email',
      label: 'Email',
      fieldtype: 'email',
      required: true,
      unique: true
    }
  ]
}

const doctype = await createDoctype(newDoctype)
```

### Generating Forms

```javascript
// Get form schema
const { getFormSchema } = useDoctypes()
const schema = await getFormSchema(doctypeId)

// Generate form dynamically
// The GeneratedForm component will handle this automatically
```

### Backend API Endpoints

```php
// List DocTypes
GET /api/doctypes/doctypes

// Create DocType
POST /api/doctypes/doctypes

// Get DocType
GET /api/doctypes/doctypes/{id}

// Update DocType
PUT /api/doctypes/doctypes/{id}

// Delete DocType
DELETE /api/doctypes/doctypes/{id}

// Get Form Schema
GET /api/doctypes/doctypes/{id}/schema

// Generate Code
POST /api/doctypes/doctypes/{id}/generate

// Field Management
POST /api/doctypes/doctypes/{id}/fields
PUT /api/doctypes/doctypes/{id}/fields/{fieldname}
DELETE /api/doctypes/doctypes/{id}/fields/{fieldname}
```

## Field Types

The package supports various field types:

- **text**: Single-line text input
- **textarea**: Multi-line text input
- **number**: Numeric input with validation
- **email**: Email input with validation
- **password**: Password input
- **select**: Dropdown selection
- **checkbox**: Boolean checkbox
- **date**: Date picker
- **datetime**: Date and time picker
- **time**: Time picker
- **file**: File upload
- **image**: Image upload
- **json**: JSON object input

## Code Generation

Generate Laravel files from DocType configuration:

```php
use Doctypes\Services\DoctypeGeneratorService;

$generator = new DoctypeGeneratorService();
$files = $generator->generateFromDoctype($doctype);

// Generated files:
// - Model
// - Controller
// - Request (Form validation)
// - Resource (API response)
// - Migration
```

## Configuration

The package configuration is located in `config/doctypes.php`:

```php
return [
    'enabled' => true,
    'middleware' => ['api', 'auth:sanctum'],
    
    // Add custom field types
    'field_types' => [
        'text', 'textarea', 'number', 'email', 'password',
        'select', 'checkbox', 'date', 'datetime', 'time',
        'file', 'image', 'json'
    ],
    
    // Default settings
    'defaults' => [
        'color' => '#3b82f6',
        'icon' => 'document',
        'per_page' => 15,
    ]
];
```

## Examples

### Creating a User Profile DocType

```javascript
const userProfile = {
  name: 'user_profile',
  label: 'User Profile',
  description: 'User profile information',
  icon: 'user',
  color: '#3b82f6',
  fields: [
    {
      fieldname: 'first_name',
      label: 'First Name',
      fieldtype: 'text',
      required: true,
      in_list_view: true
    },
    {
      fieldname: 'last_name',
      label: 'Last Name',
      fieldtype: 'text',
      required: true,
      in_list_view: true
    },
    {
      fieldname: 'email',
      label: 'Email',
      fieldtype: 'email',
      required: true,
      unique: true,
      in_list_view: true
    },
    {
      fieldname: 'phone',
      label: 'Phone',
      fieldtype: 'text',
      in_list_view: true
    },
    {
      fieldname: 'date_of_birth',
      label: 'Date of Birth',
      fieldtype: 'date'
    },
    {
      fieldname: 'gender',
      label: 'Gender',
      fieldtype: 'select',
      options: {
        options: ['male', 'female', 'other']
      }
    },
    {
      fieldname: 'bio',
      label: 'Biography',
      fieldtype: 'textarea'
    }
  ]
}
```

### Creating a Product DocType

```javascript
const product = {
  name: 'product',
  label: 'Product',
  description: 'Product catalog',
  icon: 'package',
  color: '#f59e0b',
  fields: [
    {
      fieldname: 'name',
      label: 'Product Name',
      fieldtype: 'text',
      required: true,
      in_list_view: true
    },
    {
      fieldname: 'sku',
      label: 'SKU',
      fieldtype: 'text',
      required: true,
      unique: true,
      in_list_view: true
    },
    {
      fieldname: 'price',
      label: 'Price',
      fieldtype: 'number',
      required: true,
      in_list_view: true,
      options: {
        min: 0,
        step: 0.01
      }
    },
    {
      fieldname: 'category',
      label: 'Category',
      fieldtype: 'select',
      options: {
        options: ['electronics', 'clothing', 'books']
      },
      in_standard_filter: true
    },
    {
      fieldname: 'description',
      label: 'Description',
      fieldtype: 'textarea'
    },
    {
      fieldname: 'in_stock',
      label: 'In Stock',
      fieldtype: 'checkbox',
      default_value: true,
      in_list_view: true
    }
  ]
}
```

## Development

### Running Tests

```bash
composer test
```

### Building Frontend

```bash
npm run build
```

### Linting

```bash
npm run lint
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

MIT License. See [LICENSE](LICENSE) for details.

## Support

For support, please open an issue on GitHub or contact support@ngodingskuy.com.

## 📚 More Documentation

- **[Installation Guide](INSTALL.md)** - Complete installation instructions
- **[Quick Start](QUICKSTART.md)** - Get up and running in 5 minutes
- **[API Reference](API.md)** - Complete API documentation
- **[Field Types](FIELD_TYPES.md)** - All supported field types and configurations
- **[Troubleshooting](TROUBLESHOOTING.md)** - Common issues and solutions
- **[Documentation Index](INDEX.md)** - Navigate all documentation

## Changelog

### v1.0.0
- Initial release
- Dynamic DocType creation
- Vue.js frontend components
- Code generation
- Field validation
- API endpoints
