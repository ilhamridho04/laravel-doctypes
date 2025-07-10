<?php

// Test script to verify package loading
require_once 'vendor/autoload.php';

// Check if class exists
if (class_exists('Doctypes\\Providers\\DoctypeServiceProvider')) {
    echo "✓ DoctypeServiceProvider class found\n";
} else {
    echo "✗ DoctypeServiceProvider class not found\n";
}

// Check if models exist
if (class_exists('Doctypes\\Models\\Doctype')) {
    echo "✓ Doctype model class found\n";
} else {
    echo "✗ Doctype model class not found\n";
}

if (class_exists('Doctypes\\Models\\DoctypeField')) {
    echo "✓ DoctypeField model class found\n";
} else {
    echo "✗ DoctypeField model class not found\n";
}

// Check if service exists
if (class_exists('Doctypes\\Services\\DoctypeGeneratorService')) {
    echo "✓ DoctypeGeneratorService class found\n";
} else {
    echo "✗ DoctypeGeneratorService class not found\n";
}

// Check if command exists
if (class_exists('Doctypes\\Console\\Commands\\InstallDoctypeCommand')) {
    echo "✓ InstallDoctypeCommand class found\n";
} else {
    echo "✗ InstallDoctypeCommand class not found\n";
}

echo "\nPackage structure check complete.\n";
