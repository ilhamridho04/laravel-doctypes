# Laravel Doctypes Package

![Laravel](https://img.shields.io/badge/Laravel-10.x%2B-red)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)
![Vue](https://img.shields.io/badge/Vue.js-3.x-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

A dynamic DocType system for Laravel, inspired by Frappe Framework. Create dynamic forms, models, and APIs with JSON-based field definitions.

## 🚀 Features

- **Dynamic Models**: Create models from JSON definitions
- **Form Builder**: Vue 3 + Tailwind CSS form components
- **API Generator**: Automatic CRUD API generation
- **Field Types**: Support for various field types (text, email, select, etc.)
- **Validation**: Built-in validation rules
- **TypeScript Support**: Full TypeScript integration

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

> **Need frontend setup help?** Check [FRONTEND_SETUP.md](FRONTEND_SETUP.md) for Vue.js components guide.
> **Having installation issues?** Check [QUICK_FIX.md](QUICK_FIX.md) or [INSTALLATION_CHECK.md](INSTALLATION_CHECK.md) for solutions.

4. **Generate the model**:
   ```bash
   php artisan doctype:generate Customer
   ```

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

---

**Made with ❤️ by [NgodingSkuyy](https://github.com/ngodingskuyy)**