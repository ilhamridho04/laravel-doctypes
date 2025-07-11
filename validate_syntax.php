<?php
/**
 * Syntax validation test for DocTypes package
 * This test checks if all PHP files have valid syntax
 */

function checkPhpSyntax($file)
{
    $output = [];
    $returnVar = 0;
    exec("php -l \"$file\" 2>&1", $output, $returnVar);
    return $returnVar === 0;
}

function scanDirectory($dir, $extension = '.php')
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === ltrim($extension, '.')) {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

echo "🔍 DocTypes Syntax Validation\n";
echo "=============================\n\n";

$baseDir = __DIR__;
$srcDir = $baseDir . '/src';
$dbDir = $baseDir . '/database';

$allValid = true;
$totalFiles = 0;

// Check src directory
if (is_dir($srcDir)) {
    echo "📂 Checking src/ directory...\n";
    $phpFiles = scanDirectory($srcDir, '.php');

    foreach ($phpFiles as $file) {
        $totalFiles++;
        $relativePath = str_replace($baseDir . '/', '', $file);

        if (checkPhpSyntax($file)) {
            echo "   ✓ $relativePath\n";
        } else {
            echo "   ✗ $relativePath - SYNTAX ERROR\n";
            $allValid = false;
        }
    }
    echo "\n";
}

// Check database directory
if (is_dir($dbDir)) {
    echo "📂 Checking database/ directory...\n";
    $phpFiles = scanDirectory($dbDir, '.php');

    foreach ($phpFiles as $file) {
        $totalFiles++;
        $relativePath = str_replace($baseDir . '/', '', $file);

        if (checkPhpSyntax($file)) {
            echo "   ✓ $relativePath\n";
        } else {
            echo "   ✗ $relativePath - SYNTAX ERROR\n";
            $allValid = false;
        }
    }
    echo "\n";
}

// Summary
echo "📋 Summary:\n";
echo "Total files checked: $totalFiles\n";

if ($allValid) {
    echo "✅ All PHP files have valid syntax!\n";
} else {
    echo "❌ Some files have syntax errors that need to be fixed.\n";
}

echo "\n🔧 Package Structure Check:\n";

// Check key directories
$keyDirs = [
    'src/Models',
    'src/Http/Controllers',
    'src/Http/Requests',
    'src/Http/Resources',
    'src/Services',
    'src/Console/Commands',
    'database/migrations',
    'database/seeders',
    'resource/js/features/doctypes/pages',
    'resource/js/features/doctypes/components',
    'resource/js/features/doctypes/services',
    'resource/js/features/doctypes/types',
];

foreach ($keyDirs as $dir) {
    $fullPath = $baseDir . '/' . $dir;
    if (is_dir($fullPath)) {
        $fileCount = count(glob($fullPath . '/*'));
        echo "   ✓ $dir ($fileCount files)\n";
    } else {
        echo "   ✗ $dir (missing)\n";
    }
}

echo "\n🎯 Key Files Check:\n";

$keyFiles = [
    'composer.json',
    'README.md',
    'src/Models/Doctype.php',
    'src/Models/DoctypeField.php',
    'src/Http/Controllers/DoctypeController.php',
    'src/Http/Controllers/DynamicModelController.php',
    'src/Services/DoctypeGeneratorService.php',
    'resource/js/features/doctypes/pages/DoctypeList.vue',
    'resource/js/features/doctypes/pages/DoctypeForm.vue',
    'resource/js/features/doctypes/pages/GeneratedForm.vue',
    'resource/js/features/doctypes/components/FieldRenderer.vue',
    'resource/js/features/doctypes/services/useDoctypes.ts',
    'resource/js/features/doctypes/types/doctype.d.ts',
];

foreach ($keyFiles as $file) {
    $fullPath = $baseDir . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file (missing)\n";
    }
}

echo "\n🚀 Ready for installation!\n";
echo "\nNext steps:\n";
echo "1. Add to Laravel app: composer require ngodingskuyy/doctypes\n";
echo "2. Publish package: php artisan vendor:publish --provider='NgodingSkuyy\\Doctypes\\Providers\\DoctypeServiceProvider'\n";
echo "3. Run migrations: php artisan migrate\n";
echo "4. Seed demo data: php artisan doctype:demo\n";
