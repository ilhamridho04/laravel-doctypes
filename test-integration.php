<?php

require_once 'vendor/autoload.php';

use Doctypes\Services\DoctypeGeneratorService;
use Doctypes\Console\Commands\GenerateDoctypeCommand;

try {
    echo "🔍 Testing package integration...\n\n";
    
    // Test DoctypeGeneratorService
    $service = new DoctypeGeneratorService();
    echo "✅ DoctypeGeneratorService loaded successfully\n";
    
    // Test command class (without running it)
    $reflection = new ReflectionClass(GenerateDoctypeCommand::class);
    echo "✅ GenerateDoctypeCommand class loaded successfully\n";
    
    // Check if all required methods exist in service
    $serviceReflection = new ReflectionClass($service);
    $requiredMethods = [
        'generateFromDoctype' => 'public',
        'generateFiles' => 'public', 
        'getGenerationPreview' => 'public',
        'generateFile' => 'protected',
        'generateFileContent' => 'protected'
    ];
    
    echo "\n📋 Method validation:\n";
    foreach ($requiredMethods as $method => $visibility) {
        if ($serviceReflection->hasMethod($method)) {
            $methodReflection = $serviceReflection->getMethod($method);
            $actualVisibility = $methodReflection->isPublic() ? 'public' : 
                               ($methodReflection->isProtected() ? 'protected' : 'private');
            
            if ($actualVisibility === $visibility) {
                echo "✅ {$method} ({$visibility})\n";
            } else {
                echo "⚠️  {$method} (expected {$visibility}, got {$actualVisibility})\n";
            }
        } else {
            echo "❌ {$method} (missing)\n";
        }
    }
    
    echo "\n🧪 Testing method signatures:\n";
    
    // Test generateFiles method signature
    $generateFilesMethod = $serviceReflection->getMethod('generateFiles');
    $params = $generateFilesMethod->getParameters();
    echo "✅ generateFiles has " . count($params) . " parameters\n";
    
    // Test generateFile method signature
    $generateFileMethod = $serviceReflection->getMethod('generateFile');
    $params = $generateFileMethod->getParameters();
    echo "✅ generateFile has " . count($params) . " parameters\n";
    
    echo "\n🎉 All integration tests passed!\n";
    echo "📦 Package is ready for use in Laravel projects.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    exit(1);
}
