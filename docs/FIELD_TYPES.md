# Field Types Reference

This document provides detailed information about all supported field types in the DocTypes package.

## Basic Field Types

### Text Field
Simple text input field.

```json
{
    "name": "title",
    "type": "text",
    "label": "Title",
    "required": true,
    "max_length": 255,
    "placeholder": "Enter title here",
    "default_value": ""
}
```

**Properties:**
- `max_length`: Maximum character length
- `placeholder`: Placeholder text
- `default_value`: Default value

### Email Field
Email input with validation.

```json
{
    "name": "email",
    "type": "email",
    "label": "Email Address",
    "required": true,
    "placeholder": "user@example.com"
}
```

### Password Field
Password input field.

```json
{
    "name": "password",
    "type": "password",
    "label": "Password",
    "required": true,
    "min_length": 8
}
```

**Properties:**
- `min_length`: Minimum character length

### Number Field
Numeric input field.

```json
{
    "name": "price",
    "type": "number",
    "label": "Price",
    "required": true,
    "min": 0,
    "max": 999999.99,
    "step": 0.01
}
```

**Properties:**
- `min`: Minimum value
- `max`: Maximum value
- `step`: Step increment

### Textarea Field
Multi-line text input.

```json
{
    "name": "description",
    "type": "textarea",
    "label": "Description",
    "required": false,
    "rows": 4,
    "max_length": 1000
}
```

**Properties:**
- `rows`: Number of visible rows
- `max_length`: Maximum character length

## Selection Fields

### Select Field
Dropdown selection field.

```json
{
    "name": "status",
    "type": "select",
    "label": "Status",
    "required": true,
    "options": [
        {"value": "draft", "label": "Draft"},
        {"value": "published", "label": "Published"},
        {"value": "archived", "label": "Archived"}
    ]
}
```

**Properties:**
- `options`: Array of value-label pairs

### Radio Field
Radio button selection.

```json
{
    "name": "gender",
    "type": "radio",
    "label": "Gender",
    "required": true,
    "options": [
        {"value": "male", "label": "Male"},
        {"value": "female", "label": "Female"},
        {"value": "other", "label": "Other"}
    ]
}
```

### Checkbox Field
Single checkbox or multiple checkboxes.

```json
{
    "name": "terms_accepted",
    "type": "checkbox",
    "label": "I accept the terms and conditions",
    "required": true
}
```

For multiple checkboxes:
```json
{
    "name": "interests",
    "type": "checkbox",
    "label": "Interests",
    "multiple": true,
    "options": [
        {"value": "tech", "label": "Technology"},
        {"value": "sports", "label": "Sports"},
        {"value": "music", "label": "Music"}
    ]
}
```

## Date and Time Fields

### Date Field
Date picker field.

```json
{
    "name": "birth_date",
    "type": "date",
    "label": "Birth Date",
    "required": true,
    "min": "1900-01-01",
    "max": "2023-12-31"
}
```

**Properties:**
- `min`: Minimum date
- `max`: Maximum date

### Datetime Field
Date and time picker.

```json
{
    "name": "event_datetime",
    "type": "datetime",
    "label": "Event Date & Time",
    "required": true
}
```

### Time Field
Time picker field.

```json
{
    "name": "meeting_time",
    "type": "time",
    "label": "Meeting Time",
    "required": true
}
```

## Advanced Fields

### File Upload Field
File upload with validation.

```json
{
    "name": "document",
    "type": "file",
    "label": "Upload Document",
    "required": false,
    "allowed_types": ["pdf", "doc", "docx"],
    "max_size": "10MB"
}
```

**Properties:**
- `allowed_types`: Array of allowed file extensions
- `max_size`: Maximum file size

### URL Field
URL input with validation.

```json
{
    "name": "website",
    "type": "url",
    "label": "Website",
    "required": false,
    "placeholder": "https://example.com"
}
```

### Phone Field
Phone number input.

```json
{
    "name": "phone",
    "type": "tel",
    "label": "Phone Number",
    "required": false,
    "pattern": "[0-9]{3}-[0-9]{3}-[0-9]{4}"
}
```

**Properties:**
- `pattern`: Regex pattern for validation

## Relational Fields

### Link Field
Reference to another DocType.

```json
{
    "name": "customer",
    "type": "link",
    "label": "Customer",
    "required": true,
    "link_doctype": "Customer",
    "link_field": "name"
}
```

**Properties:**
- `link_doctype`: Target DocType name
- `link_field`: Field to display from linked record

### Table Field
Child table for one-to-many relationships.

```json
{
    "name": "items",
    "type": "table",
    "label": "Order Items",
    "child_doctype": "Order Item"
}
```

**Properties:**
- `child_doctype`: Child DocType name

## Validation Rules

### Common Validation Properties

All field types support these validation properties:

- `required`: Boolean - Field is required
- `unique`: Boolean - Value must be unique
- `readonly`: Boolean - Field is read-only
- `hidden`: Boolean - Field is hidden in forms

### Custom Validation Rules

You can add custom validation rules:

```json
{
    "name": "custom_field",
    "type": "text",
    "validation_rules": [
        "required",
        "min:3",
        "max:50",
        "regex:/^[a-zA-Z0-9_]+$/"
    ]
}
```

## Conditional Fields

Fields can be shown/hidden based on other field values:

```json
{
    "name": "other_specify",
    "type": "text",
    "label": "Please specify",
    "show_if": {
        "field": "category",
        "value": "other"
    }
}
```

## Field Permissions

Control field visibility and editability:

```json
{
    "name": "internal_notes",
    "type": "textarea",
    "permissions": {
        "read": ["Admin", "Manager"],
        "write": ["Admin"]
    }
}
```

## Custom CSS Classes

Add custom styling to fields:

```json
{
    "name": "highlighted_field",
    "type": "text",
    "css_class": "bg-yellow-100 border-yellow-300"
}
```

This reference covers all supported field types and their configuration options. For implementation examples, see the [Quick Start Guide](QUICKSTART.md).
