# Installation Guide

## Installing the DocTypes Package

### Method 1: Local Development (Recommended for Development)

If you're developing this package locally, you can install it using a local path in your main Laravel project's `composer.json`:

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

Then run:
```bash
composer install
```

### Method 2: From Git Repository

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ilhamridho04/laravel-doctypes"
        }
    ],
    "require": {
        "ngodingskuyy/doctypes": "dev-main"
    }
}
```

### Method 3: Manual Installation

1. Copy the `doctypes` folder to your Laravel project's `packages` directory
2. Add the local repository to your `composer.json`
3. Run `composer install`

## After Installation

1. **Publish the configuration:**
```bash
php artisan vendor:publish --tag="doctypes-config"
```

2. **Run migrations:**
```bash
php artisan migrate
```

3. **Seed sample data (optional):**
```bash
php artisan db:seed --class=Doctypes\\Database\\Seeders\\DoctypeSeeder
```

## Troubleshooting

### Dependency Conflicts

If you encounter dependency conflicts, try:

1. **Update Composer:**
```bash
composer self-update
```

2. **Clear Composer cache:**
```bash
composer clear-cache
```

3. **Install with ignore platform requirements (if needed):**
```bash
composer install --ignore-platform-reqs
```

4. **Force reinstall:**
```bash
composer install --no-cache
```

### Laravel Version Compatibility

This package is compatible with:
- Laravel 10.x
- Laravel 11.x
- PHP 8.1+

Make sure your Laravel project meets these requirements.

### Service Provider Registration

The service provider should auto-register. If it doesn't, manually add it to `config/app.php`:

```php
'providers' => [
    // ...
    Doctypes\Providers\DoctypeServiceProvider::class,
],
```
