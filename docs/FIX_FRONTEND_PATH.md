# Fix Frontend Publishing Path

## Problem
The frontend assets were published to the wrong path: `resources/js/features/doctypes/features/doctypes/`

## Solution

### Step 1: Clean up the wrong path

Remove the incorrectly published files:

```bash
# Remove the wrong directory structure
rm -rf resources/js/features/doctypes/features/

# Or on Windows
rmdir /s resources\js\features\doctypes\features\
```

### Step 2: Re-publish with correct path

Re-publish the frontend components:

```bash
php artisan vendor:publish --tag="doctypes-views" --force
```

### Step 3: Verify correct structure

Check that the structure is now correct:

```bash
# Should show the correct structure
ls -la resources/js/features/doctypes/

# Or on Windows  
dir resources\js\features\doctypes\
```

Expected structure:
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

### Step 4: Test import

Now you can import correctly:

```javascript
import { useDoctypes } from '@/features/doctypes/services/useDoctypes'
import DoctypeList from '@/features/doctypes/pages/DoctypeList.vue'
```

## Quick Fix Command

Run this one-liner to fix everything:

```bash
# Clean and re-publish (Linux/Mac)
rm -rf resources/js/features/doctypes/features/ && php artisan vendor:publish --tag="doctypes-views" --force

# Clean and re-publish (Windows)
rmdir /s /q resources\js\features\doctypes\features\ && php artisan vendor:publish --tag="doctypes-views" --force
```

The path issue has been fixed in the service provider, so future installations will work correctly.
