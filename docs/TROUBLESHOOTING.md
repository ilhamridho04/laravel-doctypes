# Troubleshooting Guide

Common issues and solutions when using the DocTypes package.

## Installation Issues

### Issue: Package not found
**Error:** `Package ngodingskuyy/doctypes not found`

**Solution:**
1. Make sure you've added the repository to your `composer.json`:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/doctypes"
        }
    ]
}
```

2. Or if installing from Git:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ilhamridho04/laravel-doctypes"
        }
    ]
}
```

### Issue: Service Provider not found
**Error:** `Class "Doctypes\Providers\DoctypeServiceProvider" not found`

**Solution:**
1. Make sure the package is properly installed:
```bash
composer require ngodingskuyy/doctypes
```

2. Clear and regenerate autoload files:
```bash
composer dump-autoload
```

3. Clear Laravel caches:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

4. If using local development, ensure the package path is correct:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/doctypes",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

5. Verify the service provider is auto-discovered by checking your `vendor/composer/installed.json` file or manually register it in `config/app.php`:
```php
'providers' => [
    // ...
    Doctypes\Providers\DoctypeServiceProvider::class,
],
```

### Issue: ProviderRepository.php line 205 error
**Error:** `In ProviderRepository.php line 205: Class "Doctypes\Providers\DoctypeServiceProvider" not found`

**Solution:**
This error occurs when Laravel cannot find the service provider class. Follow these steps:

1. **Check composer.json configuration:**
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

2. **Install the package:**
```bash
composer install
```

3. **Clear all caches:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

4. **Dump autoload:**
```bash
composer dump-autoload
```

5. **Check if package is properly linked:**
```bash
composer show ngodingskuyy/doctypes
```

6. **If still not working, try removing and reinstalling:**
```bash
composer remove ngodingskuyy/doctypes
composer install
```

### Issue: Service Provider not registered
**Error:** `Class 'Doctypes\Providers\DoctypeServiceProvider' not found`

**Solution:**
1. Check if the service provider is registered in `config/app.php`:
```php
'providers' => [
    // ...
    Doctypes\Providers\DoctypeServiceProvider::class,
],
```

2. Or run the install command:
```bash
php artisan doctype:install
```

## Database Issues

### Issue: Migration files not found
**Error:** `Migration files not found`

**Solution:**
1. Publish the migrations:
```bash
php artisan vendor:publish --tag="doctypes-migrations"
```

2. Or run the install command:
```bash
php artisan doctype:install
```

### Issue: Table already exists
**Error:** `Table 'doctypes' already exists`

**Solution:**
1. Drop the existing tables:
```sql
DROP TABLE IF EXISTS doctype_fields;
DROP TABLE IF EXISTS doctypes;
```

2. Or modify the migration to handle existing tables:
```php
if (!Schema::hasTable('doctypes')) {
    Schema::create('doctypes', function (Blueprint $table) {
        // ...
    });
}
```

## API Issues

### Issue: Routes not found
**Error:** `Route [api/doctypes] not defined`

**Solution:**
1. Check if routes are loaded in `RouteServiceProvider`:
```php
public function boot()
{
    $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
}
```

2. Clear route cache:
```bash
php artisan route:clear
```

### Issue: CORS errors
**Error:** `CORS policy: No 'Access-Control-Allow-Origin' header`

**Solution:**
1. Install Laravel Sanctum or configure CORS:
```bash
php artisan install:api
```

2. Or add CORS headers to your API routes:
```php
Route::middleware(['api', 'cors'])->group(function () {
    // Your routes
});
```

## Frontend Issues

### Issue: Vue components not rendering
**Error:** `Component not found` or blank screen

**Solution:**
1. Make sure Vue.js is properly installed:
```bash
npm install vue@next
```

2. Check if components are properly imported:
```javascript
import { DoctypeList } from './components/DoctypeList.vue';
```

3. Verify the component registration in your Vue app:
```javascript
app.component('DoctypeList', DoctypeList);
```

### Issue: TypeScript errors
**Error:** `Cannot find module` or type errors

**Solution:**
1. Install TypeScript and types:
```bash
npm install -D typescript @types/node
```

2. Configure `tsconfig.json`:
```json
{
    "compilerOptions": {
        "target": "ES2020",
        "module": "ESNext",
        "moduleResolution": "node",
        "types": ["node"]
    }
}
```

## Code Generation Issues

### Issue: Generated files not found
**Error:** Generated model/controller files are missing

**Solution:**
1. Check if the generator service is working:
```bash
php artisan doctype:generate YourDoctype --dry-run
```

2. Verify stub files exist:
```bash
ls -la vendor/ngodingskuyy/doctypes/src/stubs/
```

### Issue: Generated code has errors
**Error:** Syntax errors in generated files

**Solution:**
1. Check the stub files for any syntax issues
2. Verify the DocType configuration is valid JSON
3. Run the generator with debug output:
```bash
php artisan doctype:generate YourDoctype --verbose
```

## Permission Issues

### Issue: Access denied to DocType operations
**Error:** `403 Forbidden` when accessing DocType endpoints

**Solution:**
1. Check if authentication is required:
```php
Route::middleware(['auth:sanctum'])->group(function () {
    // Protected routes
});
```

2. Verify user permissions in your policy:
```php
public function view(User $user, Doctype $doctype)
{
    return $user->can('view-doctype', $doctype);
}
```

## Performance Issues

### Issue: Slow DocType loading
**Problem:** DocTypes with many fields load slowly

**Solution:**
1. Add database indexes:
```php
Schema::table('doctype_fields', function (Blueprint $table) {
    $table->index('doctype_id');
    $table->index('field_type');
});
```

2. Use eager loading:
```php
$doctype = Doctype::with('fields')->find($id);
```

3. Implement caching:
```php
$doctype = Cache::remember("doctype.{$id}", 3600, function () use ($id) {
    return Doctype::with('fields')->find($id);
});
```

## Validation Issues

### Issue: Custom validation rules not working
**Error:** Validation passes when it shouldn't

**Solution:**
1. Check if validation rules are properly formatted:
```php
'validation_rules' => [
    'required',
    'string',
    'max:255'
]
```

2. Verify custom rules are registered:
```php
Validator::extend('custom_rule', function ($attribute, $value, $parameters, $validator) {
    // Custom validation logic
});
```

## Debugging Tips

### Enable Debug Mode
Add this to your `.env` file:
```
APP_DEBUG=true
LOG_LEVEL=debug
```

### Check Logs
Monitor Laravel logs for errors:
```bash
tail -f storage/logs/laravel.log
```

### Use Tinker for Testing
Test your DocTypes in Tinker:
```bash
php artisan tinker
```

```php
$doctype = Doctypes\Models\Doctype::first();
dd($doctype->fields);
```

## Getting Help

If you're still having issues:

1. **Check the documentation** - Review all docs in the `docs/` folder
2. **Search existing issues** - Check GitHub issues for similar problems
3. **Create a new issue** - Use the bug report template
4. **Provide details** - Include Laravel version, PHP version, and error logs

## Common Configuration

### Example working configuration:

**.env file:**
```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

**composer.json:**
```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.0",
        "ngodingskuyy/doctypes": "dev-main"
    }
}
```

This troubleshooting guide should help resolve most common issues. For additional help, check the [API documentation](API.md) or create an issue on GitHub.
