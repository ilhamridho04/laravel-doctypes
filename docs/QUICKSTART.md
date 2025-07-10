# Quick Start Guide

## 🚀 Resolving Composer Dependency Issues

If you're getting the error about illuminate/contracts conflicts, here are the solutions:

### Solution 1: Local Package Installation (Recommended)

1. **Copy the package to your Laravel project:**
```bash
# From your Laravel project root
mkdir -p packages
cp -r /path/to/doctypes packages/
```

2. **Add to your main composer.json:**
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
    ],
    "require": {
        "ngodingskuyy/doctypes": "@dev"
    }
}
```

3. **Install the package:**
```bash
composer require ngodingskuyy/doctypes:@dev
```

### Solution 2: Force Installation

If you want to ignore dependency conflicts temporarily:

```bash
composer require ngodingskuyy/doctypes --ignore-platform-reqs
```

### Solution 3: Update Composer Constraints

Remove the specific illuminate package versions from the package's composer.json (which we've already done):

```json
{
    "require": {
        "php": "^8.1|^8.2|^8.3"
    }
}
```

## 📦 After Installation

1. **Run the install command:**
```bash
php artisan doctype:install --seed
```

Or manually:

2. **Publish config:**
```bash
php artisan vendor:publish --tag="doctypes-config"
```

3. **Run migrations:**
```bash
php artisan migrate
```

4. **Seed sample data (optional):**
```bash
php artisan db:seed --class="Doctypes\Database\Seeders\DoctypeSeeder"
```

## 🎯 Quick Test

After installation, you can test the API endpoints:

```bash
# List doctypes
curl -X GET http://your-app.local/api/doctypes/doctypes

# Create a simple doctype
curl -X POST http://your-app.local/api/doctypes/doctypes \
  -H "Content-Type: application/json" \
  -d '{
    "name": "test_doc",
    "label": "Test Document",
    "description": "A test document type",
    "fields": [
      {
        "fieldname": "title",
        "label": "Title",
        "fieldtype": "text",
        "required": true
      }
    ]
  }'
```

## 🐛 Troubleshooting

### Issue: Class not found errors
**Solution:** Run `composer dump-autoload`

### Issue: Migration errors
**Solution:** Check if migrations have already been run with `php artisan migrate:status`

### Issue: Route not found
**Solution:** Add routes manually to your `routes/api.php`:

```php
use Doctypes\Http\Controllers\DoctypeController;

Route::prefix('api/doctypes')->group(function () {
    Route::apiResource('doctypes', DoctypeController::class);
    Route::get('doctypes/{doctype}/schema', [DoctypeController::class, 'schema']);
    Route::post('doctypes/{doctype}/generate', [DoctypeController::class, 'generate']);
    Route::post('doctypes/{doctype}/fields', [DoctypeController::class, 'addField']);
    Route::put('doctypes/{doctype}/fields/{fieldname}', [DoctypeController::class, 'updateField']);
    Route::delete('doctypes/{doctype}/fields/{fieldname}', [DoctypeController::class, 'removeField']);
});
```

## ✅ Verification

Check if everything is working:

1. **Visit the example page:** Open `packages/doctypes/example.html` in your browser
2. **Check API endpoints:** Visit `/api/doctypes/doctypes` in your browser
3. **Test Vue components:** Import and use the provided Vue components

## 📚 Next Steps

- Read the full [README.md](README.md) for detailed usage
- Check the [API Documentation](API.md) for endpoint details
- Explore the example Vue components in `/resources/js/features/doctypes/`
