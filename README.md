# Laravel Doctypes Package

![Laravel](https://img.shields.io/badge/Laravel-10.x%2B-red)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)
![Vue](https://img.shields.io/badge/Vue.js-3.x-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

A dynamic DocType system for Laravel, inspired by Frappe Framework. Create dynamic forms, models, and APIs with JSON-based field definitions.

## 🚀 Features

- 🔧 **Dynamic DocType Generator** - Create models, controllers, requests, resources, and migrations automatically
- 🚀 **Vue 3 + TypeScript Frontend** - Modern, type-safe components for form building and rendering
- 🎨 **Tailwind v4 + shadcn-vue Compatible** - Modern design system with full Tailwind v4 support
- 📝 **Dynamic Form Generation** - Generate forms on-the-fly based on DocType field definitions
- 🎯 **Field Types Support** - Text, textarea, number, email, password, select, checkbox, date, datetime, time, file, image, JSON
- 🔍 **Validation & Error Handling** - Built-in validation with custom error messages
- 📊 **JSON Field Metadata** - Store complex field configurations in JSON format
- 🔒 **Laravel Integration** - Seamless integration with Laravel's service container and routing
- 📚 **Comprehensive Documentation** - Full API docs, guides, and troubleshooting resources

## 📚 Documentation

All documentation is available in the `docs/` folder:

- **[Installation Guide](docs/INSTALL.md)** - Complete installation instructions
- **[Quick Start](docs/QUICKSTART.md)** - Get started in 5 minutes
- **[API Reference](docs/API.md)** - Complete API documentation
- **[Full Documentation](docs/README.md)** - Comprehensive guide

## 🏃‍♂️ Quick Start

**📍 Important: Run these commands in your Laravel project directory, not in the package directory!**

1. **Setup package repository in your Laravel project**:
   
   Add to your Laravel project's `composer.json`:
   ```json
   {
       "repositories": [
           {
               "type": "path",
               "url": "./packages/doctypes"
           }
       ],
       "require": {
           "ngodingskuyy/doctypes": "dev-main"
       }
   }
   ```

2. **Install the package**:
   ```bash
   composer install
   ```

3. **Install and configure**:
   ```bash
   php artisan doctype:install
   ```

4. **Publish frontend components** (if not auto-published):
   ```bash
   php artisan vendor:publish --tag="doctypes-views" --force
   ```
   
   > **Note**: If you see duplicate path like `resources/js/features/doctypes/features/doctypes/`, check [FIX_FRONTEND_PATH.md](FIX_FRONTEND_PATH.md) for the solution.

5. **Import in your Vue app**:
   ```javascript
   import { useDoctypes } from '@/features/doctypes/services/useDoctypes'
   import DoctypeList from '@/features/doctypes/pages/DoctypeList.vue'
   ```

6. **Create your first doctype**:
   ```php
   $doctype = Doctype::create([
       'name' => 'Customer',
       'fields' => [
           ['name' => 'customer_name', 'type' => 'text', 'required' => true],
           ['name' => 'email', 'type' => 'email', 'required' => true],
           ['name' => 'phone', 'type' => 'tel', 'required' => false],
       ]
   ]);
   ```

> **Need code generation help?** Check [GENERATOR_GUIDE.md](GENERATOR_GUIDE.md) for complete generator usage guide.
> **Need frontend setup help?** Check [FRONTEND_SETUP.md](FRONTEND_SETUP.md) for Vue.js components guide.
> **Having installation issues?** Check [QUICK_FIX.md](QUICK_FIX.md) or [INSTALLATION_CHECK.md](INSTALLATION_CHECK.md) for solutions.

7. **Generate Laravel files**:
   ```bash
   # Generate all files (Model, Controller, Request, Resource, Migration)
   php artisan doctype:generate Customer --all
   
   # Or generate specific files
   php artisan doctype:generate Customer --controller --model
   ```

8. **Run migrations and add routes**:
   ```bash
   php artisan migrate
   
   # Add to routes/api.php
   Route::apiResource('customers', CustomerController::class);
   ```

## 🚀 New: Form Schema Generation

The package now includes **comprehensive form schema generation** from DocType configurations:

### Key Features:
- **🎯 Dynamic Form Generation** - Convert DocType fields to Vue form schemas automatically
- **📝 Smart Field Mapping** - Backend field types map to appropriate HTML input types
- **✅ Built-in Validation** - Frontend and backend validation based on field configuration
- **🔄 Real-time CRUD** - Generated forms connect directly to API endpoints
- **🛠️ File Generation** - Auto-generate Laravel models, controllers, and migrations
- **📱 Demo Interface** - Complete demo page showing the full workflow

### Quick Example:
```vue
<!-- Simple usage -->
<GeneratedForm 
    doctype-name="product" 
    @submit="handleSubmit" 
/>

<!-- With editing -->
<GeneratedForm 
    doctype-name="product"
    :record-id="editingId"
    @save="handleSave" 
/>
```

### API Integration:
```javascript
// Get form schema
const response = await fetchDoctypeSchema('product');

// Generate Laravel files
await generateDoctypeFiles('product', {
    types: ['model', 'controller', 'migration'],
    force: true
});
```

📖 **[Complete Form Schema Guide →](FORM_SCHEMA_GUIDE.md)**

## 🔧 Recent Updates & Fixes

**Latest Version Improvements:**
- ✅ **DoctypeForm.vue** - Fixed template syntax error and improved UI consistency
- ✅ **Unified Styling** - All components now use consistent shadcn-vue + Tailwind v4 patterns
- ✅ **Clean Architecture** - Removed deprecated code and improved component structure
- ✅ **Better UX** - Enhanced form layouts, buttons, and user interaction patterns
- ✅ **Component Guide** - Added comprehensive [COMPONENT_GUIDE.md](COMPONENT_GUIDE.md) for developers

> **Migration from older versions**: If you're updating from a previous version, re-publish the frontend components to get the latest fixes: `php artisan vendor:publish --tag="doctypes-views" --force`

## 🛠️ Requirements

- PHP 8.1+
- Laravel 10.x+
- Vue.js 3.x (for frontend components)
- Tailwind CSS (for styling)

## 📖 Example

Check out `example.html` for a complete working example.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Credits

Inspired by [Frappe Framework](https://frappeframework.com/)'s DocType system.

## Changelog

### v1.2.1 - Bug Fix Release
- 🐛 **Fixed duplicate method error** - Resolved "Cannot redeclare generateFile()" error in DoctypeGeneratorService
- ✅ **Improved code quality** - Consolidated duplicate method definitions and improved method signatures
- 🧪 **Added tests** - Verified package loads correctly without fatal errors

### v1.2.0 - Tailwind v4 Compatibility Update
- ✅ **Fixed Tailwind v4 compatibility** - Removed all `@apply` directives and updated CSS classes
- ✅ **Added shadcn-vue support** - Updated all components to use shadcn-vue design tokens
- ✅ **Fixed `file:mr-4` utility error** - Resolved file input styling issues
- ✅ **Modernized component styling** - Updated focus states, colors, and interactive elements
- ✅ **Enhanced accessibility** - Improved focus indicators and color contrast
- 📚 **Added migration guide** - See `TAILWIND_V4_UPDATE.md` for detailed changes

### v1.1.0 - Generator & Documentation
- ✅ **Added DocType Generator** - `php artisan doctype:generate` command with full customization options
- ✅ **Enhanced Documentation** - Complete API docs, quickstart guide, and troubleshooting
- ✅ **Fixed Frontend Publishing** - Resolved duplicate folder issues in service provider
- ✅ **Added Installation Command** - `php artisan doctype:install` for easy setup

### v1.0.0 - Initial Release

---

**Made with ❤️ by [NgodingSkuyy](https://github.com/ngodingskuyy)**