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

1. **Install the package**:
   ```bash
   composer require ngodingskuyy/doctypes
   ```

2. **Install and configure**:
   ```bash
   php artisan doctype:install
   ```

3. **Create your first doctype**:
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

> **Having installation issues?** Check [QUICK_FIX.md](QUICK_FIX.md) for common solutions.

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