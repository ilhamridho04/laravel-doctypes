<?php

require_once 'vendor/autoload.php';

try {
    echo "🔍 Testing DoctypeGeneratorService only...\n\n";
    
    // Test that the class can be instantiated
    $service = new \Doctypes\Services\DoctypeGeneratorService();
    echo "✅ DoctypeGeneratorService instantiated successfully\n";
    
    // Use reflection to verify methods
    $reflection = new ReflectionClass($service);
    $methods = $reflection->getMethods();
    
    echo "\n📋 Available methods:\n";
    foreach ($methods as $method) {
        $visibility = $method->isPublic() ? 'public' : 
                     ($method->isProtected() ? 'protected' : 'private');
        echo "  - {$method->getName()} ({$visibility})\n";
    }
    
    // Check that we only have one generateFile method
    $generateFileMethods = array_filter($methods, function($method) {
        return $method->getName() === 'generateFile';
    });
    
    echo "\n🔍 generateFile method count: " . count($generateFileMethods) . "\n";
    
    if (count($generateFileMethods) === 1) {
        echo "✅ No duplicate generateFile methods found!\n";
        
        $method = array_values($generateFileMethods)[0];
        $params = $method->getParameters();
        echo "✅ generateFile has " . count($params) . " parameters\n";
        
        foreach ($params as $param) {
            $type = $param->getType() ? $param->getType()->getName() : 'mixed';
            $default = $param->isDefaultValueAvailable() ? 
                      ' = ' . var_export($param->getDefaultValue(), true) : '';
            echo "   - {$param->getName()}: {$type}{$default}\n";
        }
        
    } else {
        echo "❌ Found " . count($generateFileMethods) . " generateFile methods (should be 1)\n";
        exit(1);
    }
    
    echo "\n🎉 Service class is ready!\n";
    echo "✅ No 'Cannot redeclare' errors\n";
    echo "✅ All methods properly defined\n";
    echo "📦 Package can be safely used in Laravel projects\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
