<?php
/**
 * Simple integration test to validate DocTypes functionality
 * Run this file after setting up the package in a Laravel app
 */

require_once 'vendor/autoload.php';

echo "🧪 DocTypes Integration Test\n";
echo "==========================\n\n";

// Test 1: Check if models can be loaded
echo "1. Testing model loading...\n";
try {
    if (class_exists('Doctypes\Models\Doctype')) {
        echo "   ✓ Doctype model found\n";
    } else {
        echo "   ✗ Doctype model not found\n";
    }
    
    if (class_exists('Doctypes\Models\DoctypeField')) {
        echo "   ✓ DoctypeField model found\n";
    } else {
        echo "   ✗ DoctypeField model not found\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error loading models: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Check if controllers exist
echo "2. Testing controller availability...\n";
if (class_exists('Doctypes\Http\Controllers\DoctypeController')) {
    echo "   ✓ DoctypeController found\n";
} else {
    echo "   ✗ DoctypeController not found\n";
}

if (class_exists('Doctypes\Http\Controllers\DynamicModelController')) {
    echo "   ✓ DynamicModelController found\n";
} else {
    echo "   ✗ DynamicModelController not found\n";
}
echo "\n";

// Test 3: Check if services exist
echo "3. Testing services...\n";
if (class_exists('Doctypes\Services\DoctypeGeneratorService')) {
    echo "   ✓ DoctypeGeneratorService found\n";
} else {
    echo "   ✗ DoctypeGeneratorService not found\n";
}
echo "\n";

// Test 4: Check if commands exist
echo "4. Testing commands...\n";
if (class_exists('NgodingSkuyy\Doctypes\Console\Commands\DoctypeDemoCommand')) {
    echo "   ✓ DoctypeDemoCommand found\n";
} else {
    echo "   ✗ DoctypeDemoCommand not found\n";
}

if (class_exists('NgodingSkuyy\Doctypes\Console\Commands\DoctypeGenerateCommand')) {
    echo "   ✓ DoctypeGenerateCommand found\n";
} else {
    echo "   ✗ DoctypeGenerateCommand not found\n";
}
echo "\n";

// Test 5: Check migrations
echo "5. Testing migrations...\n";
$migrationsPath = __DIR__ . '/database/migrations';
if (is_dir($migrationsPath)) {
    $migrations = glob($migrationsPath . '/*.php');
    echo "   ✓ Found " . count($migrations) . " migration file(s)\n";
    foreach ($migrations as $migration) {
        echo "     - " . basename($migration) . "\n";
    }
} else {
    echo "   ✗ Migrations directory not found\n";
}
echo "\n";

// Test 6: Check seeders
echo "6. Testing seeders...\n";
$seedersPath = __DIR__ . '/database/seeders';
if (is_dir($seedersPath)) {
    $seeders = glob($seedersPath . '/*.php');
    echo "   ✓ Found " . count($seeders) . " seeder file(s)\n";
    foreach ($seeders as $seeder) {
        echo "     - " . basename($seeder) . "\n";
    }
} else {
    echo "   ✗ Seeders directory not found\n";
}
echo "\n";

// Test 7: Check frontend files
echo "7. Testing frontend files...\n";
$frontendPath = __DIR__ . '/resource/js/features/doctypes';
if (is_dir($frontendPath)) {
    echo "   ✓ Frontend directory found\n";
    
    // Check Vue components
    $components = glob($frontendPath . '/components/*.vue');
    echo "   ✓ Found " . count($components) . " Vue component(s)\n";
    
    // Check pages
    $pages = glob($frontendPath . '/pages/*.vue');
    echo "   ✓ Found " . count($pages) . " Vue page(s)\n";
    
    // Check services
    $services = glob($frontendPath . '/services/*.ts');
    echo "   ✓ Found " . count($services) . " service file(s)\n";
    
    // Check types
    $types = glob($frontendPath . '/types/*.d.ts');
    echo "   ✓ Found " . count($types) . " TypeScript definition file(s)\n";
} else {
    echo "   ✗ Frontend directory not found\n";
}
echo "\n";

echo "✅ Integration test completed!\n";
echo "\n📋 Next Steps:\n";
echo "1. Install package in Laravel app: composer require ngodingskuyy/doctypes\n";
echo "2. Publish assets: php artisan vendor:publish --tag=doctypes\n";
echo "3. Run migrations: php artisan migrate\n";
echo "4. Seed demo data: php artisan doctype:demo\n";
echo "5. Generate files: php artisan doctype:demo --generate\n";
