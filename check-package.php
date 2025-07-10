<?php

/**
 * Test script untuk memverifikasi struktur package
 * Jalankan dari root directory package
 */

echo "🔍 DocTypes Package Structure Check\n";
echo "=====================================\n\n";

// Check composer.json
if (file_exists('composer.json')) {
    echo "✅ composer.json found\n";
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    if (isset($composer['extra']['laravel']['providers'])) {
        echo "✅ Service provider configured in composer.json\n";
        echo "   Provider: " . $composer['extra']['laravel']['providers'][0] . "\n";
    } else {
        echo "❌ Service provider not configured in composer.json\n";
    }
} else {
    echo "❌ composer.json not found\n";
}

// Check service provider file
$providerFile = 'src/Providers/DoctypeServiceProvider.php';
if (file_exists($providerFile)) {
    echo "✅ Service provider file found\n";
    
    $content = file_get_contents($providerFile);
    if (strpos($content, 'class DoctypeServiceProvider') !== false) {
        echo "✅ Service provider class defined\n";
    } else {
        echo "❌ Service provider class not properly defined\n";
    }
    
    if (strpos($content, 'namespace Doctypes\Providers') !== false) {
        echo "✅ Namespace correct\n";
    } else {
        echo "❌ Namespace incorrect\n";
    }
} else {
    echo "❌ Service provider file not found\n";
}

// Check config file
if (file_exists('config/doctypes.php')) {
    echo "✅ Config file found\n";
} else {
    echo "❌ Config file not found\n";
}

// Check models
$models = ['src/Models/Doctype.php', 'src/Models/DoctypeField.php'];
foreach ($models as $model) {
    if (file_exists($model)) {
        echo "✅ " . basename($model) . " found\n";
    } else {
        echo "❌ " . basename($model) . " not found\n";
    }
}

// Check migrations
$migrationDir = 'database/migrations';
if (is_dir($migrationDir)) {
    $migrations = glob($migrationDir . '/*.php');
    echo "✅ Migration directory found with " . count($migrations) . " migrations\n";
} else {
    echo "❌ Migration directory not found\n";
}

// Check routes
if (file_exists('src/routes/api.php')) {
    echo "✅ API routes file found\n";
} else {
    echo "❌ API routes file not found\n";
}

// Check commands
if (file_exists('src/Console/Commands/InstallDoctypeCommand.php')) {
    echo "✅ Install command found\n";
} else {
    echo "❌ Install command not found\n";
}

echo "\n📋 Package Ready Check:\n";
echo "======================\n";

$readyChecks = [
    'composer.json exists' => file_exists('composer.json'),
    'Service provider exists' => file_exists('src/Providers/DoctypeServiceProvider.php'),
    'Config exists' => file_exists('config/doctypes.php'),
    'Models exist' => file_exists('src/Models/Doctype.php') && file_exists('src/Models/DoctypeField.php'),
    'Migrations exist' => is_dir('database/migrations'),
    'Routes exist' => file_exists('src/routes/api.php'),
    'Commands exist' => file_exists('src/Console/Commands/InstallDoctypeCommand.php'),
];

$allReady = true;
foreach ($readyChecks as $check => $result) {
    echo ($result ? "✅" : "❌") . " $check\n";
    if (!$result) $allReady = false;
}

echo "\n🎯 Result: ";
if ($allReady) {
    echo "Package structure is COMPLETE! ✅\n";
    echo "\nNext steps:\n";
    echo "1. Copy this package to your Laravel project's packages/ directory\n";
    echo "2. Add repository configuration to your Laravel project's composer.json\n";
    echo "3. Run 'composer install' in your Laravel project\n";
    echo "4. Run 'php artisan doctype:install' in your Laravel project\n";
} else {
    echo "Package structure has ISSUES! ❌\n";
    echo "Please fix the missing files before using the package.\n";
}

echo "\n";
