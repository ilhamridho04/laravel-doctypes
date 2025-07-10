# Quick Fix for Service Provider Error

If you're getting the error:
```
In ProviderRepository.php line 205:
Class "Doctypes\Providers\DoctypeServiceProvider" not found
```

Follow these steps to fix it:

## Step 1: Verify Package Structure

Make sure your package structure looks like this:
```
packages/doctypes/
├── src/
│   ├── Providers/
│   │   └── DoctypeServiceProvider.php
│   ├── Models/
│   ├── Http/
│   └── Services/
├── composer.json
└── config/
    └── doctypes.php
```

## Step 2: Check composer.json in Your Laravel Project

In your main Laravel project's `composer.json`:

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
- Run the database migrations
- Seed sample data (if requested)

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
