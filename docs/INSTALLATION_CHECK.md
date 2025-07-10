# Complete Installation Check

Follow these steps to ensure the DocTypes package is properly installed and the service provider is registered.

## Prerequisites

1. You should be in your **Laravel project** directory (not the package directory)
2. The package should be located at `./packages/doctypes/` relative to your Laravel project

## Step 1: Verify Laravel Project Structure

Your Laravel project should look like this:
```
your-laravel-project/
├── app/
├── config/
├── packages/
│   └── doctypes/          # Your package here
│       ├── src/
│       ├── composer.json
│       └── ...
├── vendor/
├── composer.json          # Main Laravel project composer.json
└── ...
```

## Step 2: Configure Composer Repository

In your **Laravel project's** `composer.json` (not the package's), add:

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

## Step 3: Install the Package

Run in your Laravel project root:

```bash
composer install
```

## Step 4: Verify Installation

Check if package is installed:

```bash
composer show ngodingskuyy/doctypes
```

Expected output:
```
name     : ngodingskuyy/doctypes
descrip. : Dynamic DocType system for Laravel - Create Frappe-like dynamic document types
keywords : laravel, doctype, dynamic-forms, frappe, vue
versions : dev-main
type     : library
license  : MIT License (MIT)
source   : [path] ./packages/doctypes
```

## Step 5: Check Service Provider Auto-discovery

Run package discovery:

```bash
php artisan package:discover
```

Look for output mentioning doctypes.

## Step 6: Manual Registration (If Auto-discovery Fails)

Edit `config/app.php` and add to the providers array:

```php
'providers' => [
    // ... existing providers
    
    /*
     * Package Service Providers...
     */
    Doctypes\Providers\DoctypeServiceProvider::class,
    
    // ... application providers
],
```

## Step 7: Clear All Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

## Step 8: Test Service Provider

Check if doctype commands are available:

```bash
php artisan list | grep doctype
```

You should see:
```
doctype:install    Install the DocType package
```

## Step 9: Install Package Resources

If the command is available, run:

```bash
php artisan doctype:install
```

## Troubleshooting

### Issue: Package not found
```bash
# Remove and reinstall
composer remove ngodingskuyy/doctypes
composer install
```

### Issue: Service provider not registered
```bash
# Check if class exists
php artisan tinker
>>> class_exists('Doctypes\Providers\DoctypeServiceProvider')
```

### Issue: Commands not available
```bash
# Check if routes are registered
php artisan route:list | grep doctype
```

## Final Verification

After successful installation, you should have:

1. ✅ `config/doctypes.php` file published
2. ✅ Database migrations in `database/migrations/`
3. ✅ API routes at `/api/doctypes/doctypes`
4. ✅ Artisan command `doctype:install` available

## Getting Help

If you're still having issues:

1. Check that you're running commands in your **Laravel project** (not package directory)
2. Ensure PHP and Laravel versions meet requirements
3. Try a fresh Laravel installation to test the package
4. Check the [Troubleshooting Guide](docs/TROUBLESHOOTING.md) for more solutions

## Testing in Fresh Laravel

To test if the package works, create a fresh Laravel project:

```bash
# Create fresh Laravel project
composer create-project laravel/laravel test-doctypes
cd test-doctypes

# Create packages directory and copy your package
mkdir packages
cp -r /path/to/your/doctypes packages/

# Configure composer.json and install
# (follow steps 2-9 above)
```
