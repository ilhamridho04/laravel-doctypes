# Tailwind v4 + shadcn-vue Compatibility Update

This document outlines the changes made to ensure compatibility with Tailwind v4 and shadcn-vue design system.

## Changes Made

### 1. FieldRenderer.vue
- **Removed @apply directives** that are not supported in Tailwind v4
- **Updated CSS classes** to use shadcn-vue design tokens:
  - `text-gray-700` → `text-foreground`
  - `text-gray-500` → `text-muted-foreground`
  - `text-red-500` → `text-destructive`
  - `border-gray-300` → `border-input`
  - `bg-white` → `bg-background`
  - `focus:border-indigo-500` → `focus-visible:ring-ring`
  - `focus:ring-indigo-500` → `focus-visible:ring-2`

- **Fixed file input styling** - removed problematic `file:mr-4` utility that was causing the error
- **Updated computed properties** for proper shadcn-vue styling:
  - `inputClasses` - Modern input styling with ring focus
  - `textareaClasses` - Textarea specific styling
  - `selectClasses` - Select dropdown styling
  - `checkboxClasses` - Checkbox styling with data attributes
  - `fileInputClasses` - File input with proper file: utilities

### 2. DoctypeList.vue
- **Updated color scheme** to shadcn-vue tokens:
  - `text-gray-900` → `text-foreground`
  - `text-gray-700` → `text-muted-foreground`
  - `bg-indigo-600` → `bg-primary`
  - `text-white` → `text-primary-foreground`
  - `hover:bg-indigo-700` → `hover:bg-primary/90`
  - `border-gray-300` → `border-input`
  - `bg-white` → `bg-card`

- **Updated status indicators**:
  - Error states: `bg-red-50` → `bg-destructive/10`
  - Loading spinner: `border-indigo-600` → `border-primary`

### 3. DoctypeForm.vue
- **Converted form styling** to shadcn-vue:
  - Form cards: `bg-white` → `bg-card`
  - Text colors: `text-gray-900` → `text-foreground`
  - Input styling: Updated to modern focus states
  - Button styling: `bg-indigo-100` → `bg-primary/10`

### 4. GeneratedForm.vue
- **Updated form layout** with shadcn-vue cards
- **Modernized button styling** with proper hover states
- **Fixed debug section** styling with muted backgrounds

## Shadcn-vue Design Tokens Used

### Colors
- `text-foreground` - Primary text color
- `text-muted-foreground` - Secondary text color
- `text-destructive` - Error/danger text
- `bg-background` - Main background
- `bg-card` - Card backgrounds
- `bg-muted` - Muted backgrounds
- `bg-primary` - Primary color
- `bg-destructive` - Error backgrounds
- `border-input` - Input borders
- `border-border` - General borders

### Interactive States
- `focus-visible:outline-none` - Remove default outline
- `focus-visible:ring-2` - Add focus ring
- `focus-visible:ring-ring` - Use theme ring color
- `focus-visible:ring-offset-2` - Ring offset
- `hover:bg-primary/90` - Hover states with opacity
- `disabled:opacity-50` - Disabled state
- `disabled:cursor-not-allowed` - Disabled cursor

### Components
- Modern input styling with consistent heights (h-10)
- Proper placeholder styling
- Ring-based focus instead of border changes
- Consistent spacing and typography

## Breaking Changes Fixed

1. **@apply directive removal** - All @apply uses removed for v4 compatibility
2. **file:mr-4 utility error** - Fixed by moving file input styling to computed classes
3. **Color token migration** - All gray/indigo hardcoded colors replaced with semantic tokens
4. **Focus state modernization** - Updated from border-based to ring-based focus

## Migration Benefits

- **Future-proof** - Compatible with Tailwind v4 and beyond
- **Design system consistency** - Uses shadcn-vue tokens throughout
- **Better accessibility** - Improved focus states and color contrast
- **Modern styling** - Contemporary component appearance
- **Maintainability** - Semantic color names instead of hardcoded values

## Testing Recommendations

1. Test all form components in light/dark modes
2. Verify focus states work properly
3. Check color contrast ratios
4. Test file input functionality
5. Verify responsive design at different breakpoints

## Usage in Projects

When publishing this package to Laravel projects:

1. Ensure Tailwind v4 is installed
2. Import shadcn-vue base styles
3. Configure CSS variables for design tokens
4. Test components in your theme context

```css
/* Example CSS variables needed */
:root {
  --background: 0 0% 100%;
  --foreground: 222.2 84% 4.9%;
  --card: 0 0% 100%;
  --muted: 210 40% 98%;
  --muted-foreground: 215.4 16.3% 46.9%;
  --primary: 222.2 47.4% 11.2%;
  --destructive: 0 84.2% 60.2%;
  --border: 214.3 31.8% 91.4%;
  --input: 214.3 31.8% 91.4%;
  --ring: 222.2 84% 4.9%;
}
```
