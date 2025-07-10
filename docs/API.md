# API Reference

Comprehensive API documentation for Laravel DocTypes.

## Base URL

All API endpoints are prefixed with `/api/doctypes`

## Authentication

Add your authentication middleware to the config:

```php
// config/doctypes.php
'middleware' => ['api', 'auth:sanctum'],
```

## Endpoints

### DocTypes

#### List DocTypes

```http
GET /api/doctypes/doctypes
```

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | string | Search doctypes by name or label |
| `active` | boolean | Filter by active status |
| `system` | boolean | Filter by system status |
| `per_page` | integer | Items per page (default: 15) |
| `page` | integer | Page number |

**Response:**

```json
{
  "data": [
    {
      "id": 1,
      "name": "customer",
      "label": "Customer",
      "description": "Customer information",
      "fields": [...],
      "is_active": true,
      "is_system": false,
      "icon": "user",
      "color": "#3b82f6",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "fields_count": 5
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Create DocType

```http
POST /api/doctypes/doctypes
```

**Request Body:**

```json
{
  "name": "customer",
  "label": "Customer",
  "description": "Customer information",
  "icon": "user",
  "color": "#3b82f6",
  "is_active": true,
  "fields": [
    {
      "fieldname": "name",
      "label": "Customer Name",
      "fieldtype": "text",
      "required": true,
      "unique": false,
      "in_list_view": true,
      "in_standard_filter": false,
      "description": "Full name of the customer",
      "sort_order": 1
    }
  ]
}
```

**Response:**

```json
{
  "message": "Doctype created successfully",
  "data": {
    "id": 1,
    "name": "customer",
    "label": "Customer",
    "description": "Customer information",
    "fields": [...],
    "is_active": true,
    "is_system": false,
    "icon": "user",
    "color": "#3b82f6",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "fields_count": 1
  }
}
```

#### Get DocType

```http
GET /api/doctypes/doctypes/{id}
```

**Response:**

```json
{
  "data": {
    "id": 1,
    "name": "customer",
    "label": "Customer",
    "description": "Customer information",
    "fields": [
      {
        "name": "name",
        "label": "Customer Name",
        "type": "text",
        "required": true,
        "unique": false,
        "options": null,
        "description": "Full name of the customer",
        "default_value": null,
        "in_list_view": true,
        "in_standard_filter": false
      }
    ],
    "settings": null,
    "is_active": true,
    "is_system": false,
    "icon": "user",
    "color": "#3b82f6",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "fields_count": 1
  }
}
```

#### Update DocType

```http
PUT /api/doctypes/doctypes/{id}
```

**Request Body:** Same as create request

#### Delete DocType

```http
DELETE /api/doctypes/doctypes/{id}
```

**Response:**

```json
{
  "message": "Doctype deleted successfully"
}
```

### Form Schema

#### Get Form Schema

```http
GET /api/doctypes/doctypes/{id}/schema
```

**Response:**

```json
{
  "schema": [
    {
      "name": "name",
      "label": "Customer Name",
      "type": "text",
      "required": true,
      "unique": false,
      "options": null,
      "description": "Full name of the customer",
      "default_value": null,
      "in_list_view": true,
      "in_standard_filter": false
    }
  ]
}
```

### Code Generation

#### Generate Laravel Files

```http
POST /api/doctypes/doctypes/{id}/generate
```

**Response:**

```json
{
  "message": "Files generated successfully",
  "files": {
    "model": "<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass Customer extends Model\n{\n    protected $fillable = ['name'];\n}",
    "controller": "<?php\n\nnamespace App\\Http\\Controllers;\n\nclass CustomerController extends Controller\n{\n    // Generated controller code\n}",
    "request": "<?php\n\nnamespace App\\Http\\Requests;\n\nclass CustomerRequest extends FormRequest\n{\n    // Generated request code\n}",
    "resource": "<?php\n\nnamespace App\\Http\\Resources;\n\nclass CustomerResource extends JsonResource\n{\n    // Generated resource code\n}",
    "migration": "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\n\nreturn new class extends Migration\n{\n    // Generated migration code\n}"
  }
}
```

### Field Management

#### Add Field

```http
POST /api/doctypes/doctypes/{id}/fields
```

**Request Body:**

```json
{
  "fieldname": "email",
  "label": "Email Address",
  "fieldtype": "email",
  "required": true,
  "unique": true,
  "in_list_view": true,
  "in_standard_filter": true,
  "description": "Customer email address",
  "sort_order": 2
}
```

**Response:**

```json
{
  "message": "Field added successfully",
  "field": {
    "name": "email",
    "label": "Email Address",
    "type": "email",
    "required": true,
    "unique": true,
    "options": null,
    "description": "Customer email address",
    "default_value": null,
    "in_list_view": true,
    "in_standard_filter": true
  }
}
```

#### Update Field

```http
PUT /api/doctypes/doctypes/{id}/fields/{fieldname}
```

**Request Body:** Same as add field request (excluding `fieldname`)

#### Remove Field

```http
DELETE /api/doctypes/doctypes/{id}/fields/{fieldname}
```

**Response:**

```json
{
  "message": "Field removed successfully"
}
```

## Field Types

### Supported Field Types

| Type | Description | Validation |
|------|-------------|------------|
| `text` | Single-line text input | string, max length |
| `textarea` | Multi-line text input | string, max length |
| `number` | Numeric input | numeric, min/max |
| `email` | Email input | email format |
| `password` | Password input | string, min length |
| `select` | Dropdown selection | in:option1,option2 |
| `checkbox` | Boolean checkbox | boolean |
| `date` | Date picker | date format |
| `datetime` | Date and time picker | datetime format |
| `time` | Time picker | time format |
| `file` | File upload | file, mimes, max size |
| `image` | Image upload | image, mimes, max size |
| `json` | JSON object input | array/object |

### Field Options

Different field types support different options:

#### Text/Textarea Fields
```json
{
  "options": {
    "placeholder": "Enter text here",
    "minLength": 3,
    "maxLength": 255,
    "pattern": "^[A-Za-z]+$"
  }
}
```

#### Number Fields
```json
{
  "options": {
    "min": 0,
    "max": 1000,
    "step": 0.01,
    "placeholder": "Enter amount"
  }
}
```

#### Select Fields
```json
{
  "options": {
    "options": ["option1", "option2", "option3"],
    "placeholder": "Select an option"
  }
}
```

#### File/Image Fields
```json
{
  "options": {
    "accept": "image/*",
    "maxSize": 2048,
    "multiple": false
  }
}
```

## Error Responses

### Validation Errors

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "fieldtype": ["The selected fieldtype is invalid."]
  }
}
```

### Not Found

```json
{
  "message": "Doctype not found"
}
```

### Server Error

```json
{
  "message": "Failed to create doctype",
  "error": "Database connection failed"
}
```

## Rate Limiting

API endpoints may be rate limited. Default limits:

- 60 requests per minute for authenticated users
- 10 requests per minute for unauthenticated users

## Examples

### Create Customer DocType

```bash
curl -X POST http://your-app.local/api/doctypes/doctypes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your-token" \
  -d '{
    "name": "customer",
    "label": "Customer",
    "description": "Customer management",
    "fields": [
      {
        "fieldname": "name",
        "label": "Full Name",
        "fieldtype": "text",
        "required": true,
        "in_list_view": true
      },
      {
        "fieldname": "email",
        "label": "Email",
        "fieldtype": "email",
        "required": true,
        "unique": true,
        "in_list_view": true
      },
      {
        "fieldname": "phone",
        "label": "Phone",
        "fieldtype": "text",
        "in_list_view": true
      }
    ]
  }'
```

### Generate Code for DocType

```bash
curl -X POST http://your-app.local/api/doctypes/doctypes/1/generate \
  -H "Authorization: Bearer your-token"
```

### Get Form Schema

```bash
curl -X GET http://your-app.local/api/doctypes/doctypes/1/schema \
  -H "Authorization: Bearer your-token"
```
