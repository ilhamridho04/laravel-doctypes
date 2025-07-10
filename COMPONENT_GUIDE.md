# Vue Component Guide

This guide explains the Vue.js components in the DocTypes package and how to use them effectively.

## Components Overview

### DoctypeForm.vue
A comprehensive form component for creating and editing DocTypes.

**Features:**
- ✅ Clean, modern UI with shadcn-vue design system
- ✅ Consistent Tailwind v4 styling
- ✅ Dynamic field management
- ✅ Field type selection with options
- ✅ Validation and error handling
- ✅ TypeScript support

**Key Improvements:**
- Removed stray "cop" text
- Unified styling using shadcn-vue design tokens
- Improved form layout and spacing
- Better field management UX
- Consistent button and input styling

### DoctypeList.vue
A data table component for listing and managing DocTypes.

**Features:**
- ✅ Sortable and filterable table
- ✅ Pagination support
- ✅ Search functionality
- ✅ Status filtering
- ✅ Bulk actions

### GeneratedForm.vue
A dynamic form component that renders forms based on DocType schemas.

**Features:**
- ✅ Dynamic field rendering
- ✅ Validation support
- ✅ File uploads
- ✅ Complex field types (select, checkbox, etc.)
- ✅ Error handling

### FieldRenderer.vue
A reusable component for rendering individual form fields.

**Features:**
- ✅ Supports all DocType field types
- ✅ Consistent styling
- ✅ Validation display
- ✅ Accessibility features

## Styling Guide

All components use shadcn-vue design system with Tailwind v4:

### Colors
- `text-foreground` - Primary text
- `text-muted-foreground` - Secondary text
- `text-destructive` - Error text
- `bg-background` - Primary background
- `bg-card` - Card background
- `bg-muted` - Muted background

### Components
- Buttons use shadcn-vue button classes
- Inputs use consistent border and focus styles
- Cards have proper shadows and borders
- Form layouts use CSS Grid

## Usage Examples

### Basic DocType Form
```vue
<template>
  <DoctypeForm 
    :doctype="selectedDoctype" 
    @saved="handleSaved" 
    @cancel="handleCancel" 
  />
</template>
```

### DocType List with Actions
```vue
<template>
  <DoctypeList 
    @create="showCreateForm" 
    @edit="showEditForm" 
    @delete="handleDelete" 
  />
</template>
```

### Dynamic Form Generation
```vue
<template>
  <GeneratedForm 
    :doctype-name="'user_profile'" 
    :record-id="userId" 
    @saved="handleFormSaved" 
  />
</template>
```

## TypeScript Support

All components are fully typed with:
- Interface definitions for props and emits
- Type-safe data handling
- Proper type imports

## Best Practices

1. **Consistent Styling**: Always use shadcn-vue design tokens
2. **Accessibility**: Include proper labels and ARIA attributes
3. **Performance**: Use v-memo for expensive computations
4. **Error Handling**: Always provide user feedback
5. **Validation**: Use both client and server-side validation

## Troubleshooting

### TypeScript Errors
If you see "Cannot find module" errors, ensure:
- Vue.js is properly installed
- @heroicons/vue is installed
- TypeScript is configured correctly

### Styling Issues
If styles don't apply:
- Check Tailwind CSS is loaded
- Verify shadcn-vue components are available
- Ensure CSS variables are defined

### Runtime Errors
Common issues:
- Missing props or incorrect types
- Undefined reactive references
- Async data loading race conditions

## Development Notes

The components are designed to work in a Laravel environment with:
- Vite for bundling
- TypeScript for type safety
- Tailwind CSS v4 for styling
- shadcn-vue for UI components

For optimal development experience, use VS Code with:
- Vue Language Features (Volar)
- TypeScript Vue Plugin
- Tailwind CSS IntelliSense
