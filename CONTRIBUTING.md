# Contributing to DocTypes Package

Thank you for considering contributing to the DocTypes package! We welcome all contributions that help improve the package.

## How to Contribute

### 1. Fork the Repository
Fork the repository on GitHub and clone it locally:

```bash
git clone https://github.com/your-username/laravel-doctypes.git
cd laravel-doctypes
```

### 2. Set Up Development Environment

1. Install dependencies:
   ```bash
   composer install
   npm install
   ```

2. Create a test Laravel project to test the package:
   ```bash
   composer create-project laravel/laravel test-app
   cd test-app
   ```

3. Add the package as a local dependency in your test app's `composer.json`:
   ```json
   {
       "repositories": [
           {
               "type": "path",
               "url": "../laravel-doctypes"
           }
       ],
       "require": {
           "ngodingskuyy/doctypes": "dev-main"
       }
   }
   ```

### 3. Make Your Changes

1. Create a new branch for your feature or bug fix:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes and test them thoroughly
3. Add or update tests if necessary
4. Update documentation if needed

### 4. Testing

Before submitting your changes, make sure to:

1. Test the package in a fresh Laravel installation
2. Test all existing functionality still works
3. Test your new feature or bug fix
4. Run any existing tests (when available)

### 5. Commit Your Changes

1. Stage your changes:
   ```bash
   git add .
   ```

2. Commit with a descriptive message:
   ```bash
   git commit -m "Add: Description of your changes"
   ```

   Use conventional commit messages:
   - `Add:` for new features
   - `Fix:` for bug fixes
   - `Update:` for updates to existing features
   - `Remove:` for removing features
   - `Docs:` for documentation changes

### 6. Push and Create Pull Request

1. Push your branch:
   ```bash
   git push origin feature/your-feature-name
   ```

2. Create a pull request on GitHub with:
   - Clear title and description
   - List of changes made
   - Screenshots (if applicable)
   - Test results

## Code Style Guidelines

### PHP Code Style
- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add proper docblocks for classes and methods
- Keep methods small and focused

### JavaScript/Vue Code Style
- Use TypeScript when possible
- Follow Vue.js 3 Composition API best practices
- Use meaningful component and variable names
- Add proper type definitions

### Database
- Use descriptive migration names
- Add proper indexes for performance
- Include rollback methods in migrations

## Areas for Contribution

We welcome contributions in these areas:

### 🐛 Bug Fixes
- Report and fix bugs
- Improve error handling
- Fix performance issues

### ✨ New Features
- New field types (file uploads, rich text, etc.)
- Advanced validation rules
- Better UI components
- Performance optimizations

### 📚 Documentation
- Improve existing documentation
- Add more examples
- Create video tutorials
- Translate documentation

### 🧪 Testing
- Add unit tests
- Add integration tests
- Improve test coverage

### 🎨 UI/UX Improvements
- Better component designs
- Accessibility improvements
- Mobile responsiveness
- Dark mode support

## Questions and Support

If you have questions about contributing:

1. Check the [documentation](docs/) first
2. Look at existing [issues](https://github.com/ilhamridho04/laravel-doctypes/issues)
3. Create a new issue for discussion
4. Contact the maintainers

## Code of Conduct

Please be respectful and considerate in all interactions. We want to maintain a welcoming environment for all contributors.

## License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.

---

Thank you for helping improve the DocTypes package! 🚀
