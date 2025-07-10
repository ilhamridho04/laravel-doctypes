# Frontend Setup Guide

## Publish Frontend Components

The DocTypes package includes Vue.js components that need to be published to your Laravel project.

## Step 1: Publish Frontend Assets

Run this command in your Laravel project to publish the frontend components:

```bash
php artisan vendor:publish --tag="doctypes-views" --force
```

This will create the following structure in your Laravel project:

```
resources/js/features/doctypes/
├── components/
│   └── FieldRenderer.vue
├── pages/
│   ├── DoctypeForm.vue
│   ├── DoctypeList.vue
│   └── GeneratedForm.vue
├── services/
│   └── useDoctypes.ts
└── types/
    └── doctype.d.ts
```

## Step 2: Verify Installation

Check if the files were published correctly:

```bash
# Check if the directory exists
ls -la resources/js/features/doctypes/

# Or on Windows
dir resources\js\features\doctypes\
```

## Step 3: Import in Your Vue Application

### Option A: Import Individual Components

```javascript
// In your Vue component or main.js
import DoctypeList from '@/features/doctypes/pages/DoctypeList.vue'
import DoctypeForm from '@/features/doctypes/pages/DoctypeForm.vue'
import GeneratedForm from '@/features/doctypes/pages/GeneratedForm.vue'
import { useDoctypes } from '@/features/doctypes/services/useDoctypes'

// Register components globally (optional)
app.component('DoctypeList', DoctypeList)
app.component('DoctypeForm', DoctypeForm)
app.component('GeneratedForm', GeneratedForm)
```

### Option B: Create an Index File

Create `resources/js/features/doctypes/index.ts`:

```typescript
// Export all components
export { default as DoctypeList } from './pages/DoctypeList.vue'
export { default as DoctypeForm } from './pages/DoctypeForm.vue'
export { default as GeneratedForm } from './pages/GeneratedForm.vue'
export { default as FieldRenderer } from './components/FieldRenderer.vue'

// Export services
export { useDoctypes } from './services/useDoctypes'

// Export types
export type * from './types/doctype.d.ts'
```

Then import everything:

```javascript
import { 
  DoctypeList, 
  DoctypeForm, 
  GeneratedForm, 
  useDoctypes 
} from '@/features/doctypes'
```

## Step 4: Configure Your Build System

### For Vite (Laravel 9+)

Make sure your `vite.config.js` can resolve the `@` alias:

```javascript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue()
    ],
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    }
})
```

### For Laravel Mix (Laravel 8 and below)

Update your `webpack.mix.js`:

```javascript
const mix = require('laravel-mix')

mix.js('resources/js/app.js', 'public/js')
   .vue()
   .webpackConfig({
       resolve: {
           alias: {
               '@': path.resolve('resources/js')
           }
       }
   })
```

## Step 5: Install Dependencies

Make sure you have the required dependencies:

```bash
# Install Vue 3 and TypeScript support
npm install vue@next @vitejs/plugin-vue typescript

# Install Tailwind CSS (for styling)
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

## Step 6: Usage Example

Create a Vue component that uses the DocTypes:

```vue
<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">DocTypes Management</h1>
    
    <!-- List DocTypes -->
    <DoctypeList 
      @create="showCreateForm"
      @edit="showEditForm"
      @generate="showGeneratedForm"
    />
    
    <!-- Create/Edit Form -->
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
      @submit="handleSubmit"
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

const showGeneratedForm = (doctypeId) => {
  selectedDoctypeId.value = doctypeId
  showGenerated.value = true
}

const handleSaved = () => {
  showForm.value = false
  // Refresh list
}

const handleSubmit = (data) => {
  console.log('Form submitted:', data)
  showGenerated.value = false
}

const hideForm = () => showForm.value = false
const hideGenerated = () => showGenerated.value = false
</script>
```

## Troubleshooting

### Issue: Files not published

Run the publish command with force flag:

```bash
php artisan vendor:publish --tag="doctypes-views" --force
```

### Issue: Wrong directory structure (duplicate path)

If you see `resources/js/features/doctypes/features/doctypes/`, clean and re-publish:

```bash
# Remove wrong structure
rm -rf resources/js/features/doctypes/features/

# Or on Windows
rmdir /s /q resources\js\features\doctypes\features\

# Re-publish correctly
php artisan vendor:publish --tag="doctypes-views" --force
```

### Issue: Import path not found

Make sure your build system is configured with the correct alias:
- `@` should point to `resources/js`
- Path should be `@/features/doctypes/...`

### Issue: Vue components not working

1. Make sure Vue 3 is installed and configured
2. Check that Tailwind CSS is included for styling
3. Verify TypeScript support if using `.ts` files

### Manual Publishing

If automatic publishing fails, manually copy the files:

```bash
# Create the directory
mkdir -p resources/js/features/doctypes

# Copy from package
cp -r packages/doctypes/resource/js/* resources/js/features/doctypes/
```

## Next Steps

1. **Style Components**: Customize the Tailwind classes in components
2. **Add Routes**: Set up Vue Router for page navigation
3. **API Integration**: Configure axios or fetch for API calls
4. **Authentication**: Add authentication to API endpoints if needed

For more examples, check the `example.html` file in the package root.

## Tailwind v4 Compatibility

This package is fully compatible with **Tailwind CSS v4** and uses **shadcn-vue** design system.

### Required CSS Variables

Add these CSS variables to your project for proper theming:

```css
:root {
  --background: 0 0% 100%;
  --foreground: 222.2 84% 4.9%;
  --card: 0 0% 100%;
  --card-foreground: 222.2 84% 4.9%;
  --popover: 0 0% 100%;
  --popover-foreground: 222.2 84% 4.9%;
  --primary: 222.2 47.4% 11.2%;
  --primary-foreground: 210 40% 98%;
  --secondary: 210 40% 96%;
  --secondary-foreground: 222.2 84% 4.9%;
  --muted: 210 40% 96%;
  --muted-foreground: 215.4 16.3% 46.9%;
  --accent: 210 40% 96%;
  --accent-foreground: 222.2 84% 4.9%;
  --destructive: 0 84.2% 60.2%;
  --destructive-foreground: 210 40% 98%;
  --border: 214.3 31.8% 91.4%;
  --input: 214.3 31.8% 91.4%;
  --ring: 222.2 84% 4.9%;
  --radius: 0.5rem;
}

.dark {
  --background: 222.2 84% 4.9%;
  --foreground: 210 40% 98%;
  --card: 222.2 84% 4.9%;
  --card-foreground: 210 40% 98%;
  --popover: 222.2 84% 4.9%;
  --popover-foreground: 210 40% 98%;
  --primary: 210 40% 98%;
  --primary-foreground: 222.2 47.4% 11.2%;
  --secondary: 217.2 32.6% 17.5%;
  --secondary-foreground: 210 40% 98%;
  --muted: 217.2 32.6% 17.5%;
  --muted-foreground: 215 20.2% 65.1%;
  --accent: 217.2 32.6% 17.5%;
  --accent-foreground: 210 40% 98%;
  --destructive: 0 62.8% 30.6%;
  --destructive-foreground: 210 40% 98%;
  --border: 217.2 32.6% 17.5%;
  --input: 217.2 32.6% 17.5%;
  --ring: 212.7 26.8% 83.9%;
}
```

### Breaking Changes from Tailwind v3

If you're upgrading from a previous version that used Tailwind v3:

1. **@apply directives removed** - All components use utility classes directly
2. **Color tokens updated** - Using semantic color names instead of hardcoded values
3. **Focus states modernized** - Ring-based focus instead of border changes
4. **File input styling** - Fixed `file:mr-4` compatibility issues

See `TAILWIND_V4_UPDATE.md` for detailed migration information.
