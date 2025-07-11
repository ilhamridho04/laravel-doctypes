# DocTypes Frontend Components Import Guide

## Fixed Import Issue

✅ **The import issue has been resolved!**

## Correct Import Syntax

### Option 1: Named Imports (Recommended)
```javascript
import { DoctypeList, DoctypeForm, GeneratedForm } from '@/features/doctypes'
```

### Option 2: Individual Imports (Alternative)
```javascript
import DoctypeList from '@/features/doctypes/pages/DoctypeList.vue'
import DoctypeForm from '@/features/doctypes/pages/DoctypeForm.vue'
import GeneratedForm from '@/features/doctypes/pages/GeneratedForm.vue'
```

### Option 3: Service Imports (Working)
```javascript
import { useDoctypes } from '@/features/doctypes/services/useDoctypes'
```

## Available Exports

### Vue Components
- `DoctypeList` - Browse and manage DocTypes
- `DoctypeForm` - Create/edit DocType definitions  
- `DoctypeDemo` - Interactive demo and API docs
- `GeneratedForm` - Dynamic form generation
- `DocumentList` - Manage documents of specific DocTypes
- `FieldRenderer` - Dynamic field rendering component

### Services/Composables
- `useDoctypes` - DocType API management composable

### Types (TypeScript)
- All TypeScript interfaces and types from `./types/doctype`

## Usage Examples

### Basic Component Import
```vue
<template>
  <div>
    <DoctypeList @select="handleSelect" />
    <DoctypeForm v-if="showForm" @save="handleSave" />
  </div>
</template>

<script setup>
import { DoctypeList, DoctypeForm } from '@/features/doctypes'
import { ref } from 'vue'

const showForm = ref(false)

const handleSelect = (doctype) => {
  console.log('Selected:', doctype)
}

const handleSave = (data) => {
  console.log('Saved:', data)
  showForm.value = false
}
</script>
```

### Using with Composables
```vue
<script setup>
import { GeneratedForm, useDoctypes } from '@/features/doctypes'
import { ref, onMounted } from 'vue'

const { doctypes, fetchDoctypes } = useDoctypes()
const selectedDoctype = ref(null)

onMounted(() => {
  fetchDoctypes()
})
</script>
```

## Path Aliases Setup

Make sure your `vite.config.js` or `webpack.config.js` has the correct path alias:

### Vite (Laravel Mix/Vite)
```javascript
// vite.config.js
export default {
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    }
  }
}
```

### Laravel Mix (if using)
```javascript
// webpack.mix.js
mix.webpackConfig({
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    }
  }
})
```

## Troubleshooting

### If imports still don't work:

1. **Check your alias configuration** - Make sure `@` points to `resources/js`
2. **Restart your development server** - After publishing new files
3. **Clear cache** - `npm run build` or restart Vite dev server
4. **Use full paths** - As fallback, use individual component imports

### TypeScript Issues
If using TypeScript, ensure your `tsconfig.json` includes:
```json
{
  "compilerOptions": {
    "baseUrl": ".",
    "paths": {
      "@/*": ["resources/js/*"]
    }
  }
}
```

## File Structure
```
resources/js/features/doctypes/
├── index.ts              # Main export file (fixed)
├── index.js              # JavaScript version  
├── package.json          # Module configuration
├── components/
│   └── FieldRenderer.vue
├── pages/
│   ├── DoctypeList.vue
│   ├── DoctypeForm.vue
│   ├── GeneratedForm.vue
│   ├── DocumentList.vue
│   └── DoctypeDemo.vue
├── services/
│   └── useDoctypes.ts
└── types/
    └── doctype.d.ts
```

The import issue is now resolved and all components should be importable using the standard `@/features/doctypes` syntax! 🎉
