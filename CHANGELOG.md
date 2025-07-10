# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of DocTypes package
- Dynamic DocType system inspired by Frappe Framework
- Vue.js 3 frontend components with Tailwind CSS
- Automatic code generation for Laravel models, controllers, requests, and resources
- Support for multiple field types (text, email, select, date, checkbox, etc.)
- Built-in validation and form rendering
- API endpoints for CRUD operations
- TypeScript support with proper type definitions
- Comprehensive documentation

### Features
- **Backend**:
  - `Doctype` and `DoctypeField` models
  - Database migrations and seeders
  - API controllers with full CRUD operations
  - Request validation and API resources
  - Service provider for easy integration
  - Artisan command for package installation
  - Code generation service with stubs

- **Frontend**:
  - Vue 3 components: DoctypeList, DoctypeForm, GeneratedForm
  - FieldRenderer component for dynamic field rendering
  - TypeScript types and interfaces
  - Composable hooks for API interactions
  - Responsive design with Tailwind CSS

- **Documentation**:
  - Complete installation guide
  - Quick start tutorial
  - API reference
  - Usage examples
  - Troubleshooting guide

## [1.0.0] - 2025-01-XX

### Added
- Initial stable release
- Core functionality complete
- Documentation ready
- Production-ready package

---

**Legend:**
- `Added` for new features
- `Changed` for changes in existing functionality
- `Deprecated` for soon-to-be removed features
- `Removed` for now removed features
- `Fixed` for any bug fixes
- `Security` for security-related changes
