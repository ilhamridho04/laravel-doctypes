# Service Provider Registration Guide

## ✅ Automatic Registration (Recommended)

The package is configured for **automatic service provider discovery**. Laravel will automatically register the service provider when the package is installed.

### Package Configuration
In `composer.json`, the service provider is configured for auto-discovery:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Doctypes\\Providers\\DoctypeServiceProvider"
            ]
        }
    }
}
```

## 🔧 Manual Registration (If Auto-discovery Fails)

If automatic registration doesn't work, manually register the service provider:

### Step 1: Add to config/app.php

Add the service provider to your Laravel application's `config/app.php`:

```php
<?php

return [
    // ... other configuration

    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        // ... other providers

        /*
         * Package Service Providers...
         */
        Doctypes\Providers\DoctypeServiceProvider::class,

        /*
         * Application Service Providers...
         */
        // ... your application providers
    ],

    // ... rest of configuration
];
```

### Step 2: Clear Configuration Cache

After manual registration, clear the configuration cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## � Step-by-Step Debugging

If the service provider is still not recognized, follow these debugging steps:

### Step 1: Verify Package Installation

Check if the package is properly installed in your Laravel project:

```bash
# In your Laravel project root
composer show ngodingskuyy/doctypes
```

### Step 2: Check Auto-discovery

Verify Laravel can see the package's service providers:

```bash
# In your Laravel project
php artisan package:discover
```

### Step 3: Check Composer Installed Packages

Look for your package in the installed packages file:

```bash
# Check if package is in vendor/composer/installed.json
cat vendor/composer/installed.json | grep -A 5 -B 5 "ngodingskuyy/doctypes"
```

### Step 4: Manual Service Provider Registration

If auto-discovery fails, manually add to `config/app.php`:

```php
<?php

return [
    // ... other configuration

    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        // ... other Laravel providers

        /*
         * Package Service Providers...
         */
        Doctypes\Providers\DoctypeServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        // ... your application providers
    ],

    // ... rest of configuration
];
```

### Step 5: Force Clear Everything

```bash
# Clear all possible caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
composer dump-autoload --optimize
```

## �🚀 Installation Commands

After the service provider is registered (automatically or manually), run:

```bash
# Install the package
php artisan doctype:install

# Or step by step:
php artisan vendor:publish --tag="doctypes-config"
php artisan vendor:publish --tag="doctypes-migrations"
php artisan migrate
```

## ✨ Verification

### Check if Service Provider is Loaded

1. **Check available commands:**
```bash
php artisan list | grep doctype
```
Expected output:
```
doctype:install    Install the DocType package
```

2. **Check if routes are registered:**
```bash
php artisan route:list | grep doctype
```

3. **Check configuration:**
```bash
php artisan config:show doctypes
```

### Troubleshooting

If the service provider is not recognized:

1. **Clear all caches:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

2. **Check package installation:**
```bash
composer show ngodingskuyy/doctypes
```

3. **Verify autoload:**
```bash
composer dump-autoload --optimize
```

## 📁 Service Provider Features

The `DoctypeServiceProvider` automatically:

- ✅ Registers routes (`/api/doctypes/*`)
- ✅ Loads database migrations
- ✅ Registers artisan commands
- ✅ Publishes configuration files
- ✅ Publishes Vue.js components
- ✅ Registers generator service

## 🎯 Expected Behavior

Once properly registered, you should be able to:

1. Run `php artisan doctype:install`
2. Access API endpoints at `/api/doctypes/doctypes`
3. Use the Vue.js components
4. Generate code from DocTypes

If any of these don't work, the service provider may not be properly registered.
