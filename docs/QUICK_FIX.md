# Quick Fix for Service Provider Error

If you're getting the error:
```
In ProviderRepository.php line 205:
Class "Doctypes\Providers\DoctypeServiceProvider" not found
```

**📍 IMPORTANT: Make sure you're running these commands in your LARAVEL PROJECT directory, not in the package directory!**

## 🔍 Pre-Check: Verify Package Structure

First, run this in the package directory to ensure everything is ready:
```bash
# In packages/doctypes/ directory
php check-package.php
```

You should see "Package structure is COMPLETE! ✅"

## Step 1: Verify Laravel Project Setup

Your Laravel project structure should be:
```
your-laravel-project/
├── app/
├── config/
├── packages/
│   └── doctypes/          # Your package here
├── vendor/
├── composer.json          # This is where you configure the package
└── ...
```

## Step 2: Check composer.json in Your Laravel Project

In your **Laravel project's** `composer.json` (NOT the package's composer.json):

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

## Step 3: Install and Clear Cache

Run these commands in your Laravel project root:

```bash
# Install the package
composer install

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Regenerate autoload
composer dump-autoload
```

## Step 4: Verify Installation

Check if the package is properly installed:

```bash
composer show ngodingskuyy/doctypes
```

## Step 5: Manual Registration (If Auto-discovery Fails)

If the service provider is still not found, manually register it in `config/app.php`:

```php
'providers' => [
    // Other providers...
    
    /*
     * Package Service Providers...
     */
    Doctypes\Providers\DoctypeServiceProvider::class,
],
```

> For detailed service provider configuration, see [SERVICE_PROVIDER.md](SERVICE_PROVIDER.md)

## Step 6: Run Package Installation

After the service provider is recognized, run:

```bash
php artisan doctype:install
```

This will:
- Publish the configuration file
- Publish the database migrations  
- Publish the frontend components
- Run the database migrations
- Seed sample data (if requested)

## Step 7: Verify Frontend Components

Check if the frontend components were published:

```bash
# Check if directory exists
ls -la resources/js/features/doctypes/

# Or on Windows
dir resources\js\features\doctypes\
```

If the directory doesn't exist, manually publish frontend assets:

```bash
php artisan vendor:publish --tag="doctypes-views" --force
```

## Alternative: Symlink Installation

If you're still having issues, try using symlink option:

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

Then run:
```bash
composer update ngodingskuyy/doctypes
```

## Verification

To verify everything is working:

1. Check if service provider is loaded:
```bash
php artisan list | grep doctype
```

2. Check if config is published:
```bash
php artisan config:show doctypes
```

3. Check if routes are registered:
```bash
php artisan route:list | grep doctype
```

If you still encounter issues, see the full [Troubleshooting Guide](TROUBLESHOOTING.md) for more solutions.
