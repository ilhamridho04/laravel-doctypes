<?php

namespace Ngodingskuyy\Doctypes\Services;

use Ngodingskuyy\Doctypes\Models\Doctype;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DoctypeGeneratorService
{
    protected $stubs = [
        'model' => 'DoctypeModel.stub',
        'controller' => 'DoctypeController.stub',
        'request' => 'DoctypeRequest.stub',
        'resource' => 'DoctypeResource.stub',
        'migration' => 'DoctypeMigration.stub',
    ];

    public function generateFromDoctype(Doctype $doctype): array
    {
        $results = [];

        foreach ($this->stubs as $type => $stubFile) {
            try {
                $content = $this->generateFileContent($type, $doctype);
                $results[$type] = $content;
            } catch (\Exception $e) {
                $results[$type] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Generate multiple files based on types
     */
    public function generateFiles(Doctype $doctype, array $types, bool $force = false): array
    {
        $results = [];

        foreach ($types as $type) {
            try {
                $result = $this->generateFile($type, $doctype, $force);
                $results[$type] = $result;
            } catch (\Exception $e) {
                $results[$type] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Get generation preview without creating files
     */
    public function getGenerationPreview(Doctype $doctype, string $type): array
    {
        $modelName = Str::studly($doctype->name);
        $fileName = $this->getFileName($type, $modelName);
        $directory = $this->getOutputDirectory($type);

        return [
            'path' => $directory . '/' . $fileName,
            'full_path' => app_path(str_replace('app/', '', $directory)) . '/' . $fileName,
            'type' => $type,
            'exists' => file_exists(app_path(str_replace('app/', '', $directory)) . '/' . $fileName)
        ];
    }

    /**
     * Generate a single file with force option
     */
    protected function generateFile(string $type, Doctype $doctype, bool $force = false): array
    {
        if (!isset($this->stubs[$type])) {
            throw new \Exception("Unknown generation type: {$type}");
        }

        $modelName = Str::studly($doctype->name);
        $fileName = $this->getFileName($type, $modelName);
        $directory = $this->getOutputDirectory($type);
        $fullPath = app_path(str_replace('app/', '', $directory)) . '/' . $fileName;

        // Check if file exists and force is not set
        if (file_exists($fullPath) && !$force) {
            throw new \Exception("File already exists: {$fullPath}. Use --force to overwrite.");
        }

        // Create directory if it doesn't exist
        if (!File::exists(dirname($fullPath))) {
            File::makeDirectory(dirname($fullPath), 0755, true);
        }

        // Generate content
        $content = $this->generateFileContent($type, $doctype);

        // Write file
        File::put($fullPath, $content);

        return [
            'path' => $directory . '/' . $fileName,
            'full_path' => $fullPath,
            'type' => $type
        ];
    }

    /**
     * Get file name for type
     */
    protected function getFileName(string $type, string $modelName): string
    {
        switch ($type) {
            case 'model':
                return $modelName . '.php';
            case 'controller':
                return $modelName . 'Controller.php';
            case 'request':
                return $modelName . 'Request.php';
            case 'resource':
                return $modelName . 'Resource.php';
            case 'migration':
                $timestamp = date('Y_m_d_His');
                $tableName = Str::snake(Str::plural($modelName));
                return "{$timestamp}_create_{$tableName}_table.php";
            default:
                return $modelName . '.php';
        }
    }

    /**
     * Get output directory for type
     */
    protected function getOutputDirectory(string $type): string
    {
        switch ($type) {
            case 'model':
                return 'app/Models';
            case 'controller':
                return 'app/Http/Controllers';
            case 'request':
                return 'app/Http/Requests';
            case 'resource':
                return 'app/Http/Resources';
            case 'migration':
                return 'database/migrations';
            default:
                return 'app';
        }
    }

    /**
     * Generate file content
     */
    protected function generateFileContent(string $type, Doctype $doctype): string
    {
        $stubPath = __DIR__ . '/../stubs/' . $this->stubs[$type];

        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found: {$stubPath}");
        }

        $content = File::get($stubPath);
        $replacements = $this->getReplacements($type, $doctype);

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    protected function getReplacements(string $type, Doctype $doctype): array
    {
        $modelName = Str::studly($doctype->name);
        $tableName = Str::snake(Str::plural($doctype->name));
        $modelVariable = Str::camel($doctype->name);

        $baseReplacements = [
            '{{modelName}}' => $modelName,
            '{{tableName}}' => $tableName,
            '{{modelVariable}}' => $modelVariable,
            '{{controllerName}}' => $modelName . 'Controller',
            '{{requestName}}' => $modelName . 'Request',
            '{{resourceName}}' => $modelName . 'Resource',
        ];

        switch ($type) {
            case 'model':
                return array_merge($baseReplacements, [
                    '{{fillableFields}}' => $this->generateFillableFields($doctype),
                    '{{castFields}}' => $this->generateCastFields($doctype),
                    '{{relationships}}' => $this->generateRelationships($doctype),
                    '{{scopes}}' => $this->generateScopes($doctype),
                ]);

            case 'controller':
                return array_merge($baseReplacements, [
                    '{{filterLogic}}' => $this->generateFilterLogic($doctype),
                ]);

            case 'request':
                return array_merge($baseReplacements, [
                    '{{validationRules}}' => $this->generateValidationRules($doctype),
                    '{{validationMessages}}' => $this->generateValidationMessages($doctype),
                ]);

            case 'resource':
                return array_merge($baseReplacements, [
                    '{{resourceFields}}' => $this->generateResourceFields($doctype),
                ]);

            case 'migration':
                return array_merge($baseReplacements, [
                    '{{fields}}' => $this->generateMigrationFields($doctype),
                ]);

            default:
                return $baseReplacements;
        }
    }

    protected function generateFillableFields(Doctype $doctype): string
    {
        $fields = $doctype->doctypeFields->pluck('fieldname')->toArray();
        $fillable = array_map(function ($field) {
            return "'{$field}'";
        }, $fields);

        return implode(",\n        ", $fillable);
    }

    protected function generateCastFields(Doctype $doctype): string
    {
        $casts = [];

        foreach ($doctype->doctypeFields as $field) {
            switch ($field->fieldtype) {
                case 'checkbox':
                    $casts[] = "'{$field->fieldname}' => 'boolean'";
                    break;
                case 'number':
                    $casts[] = "'{$field->fieldname}' => 'float'";
                    break;
                case 'date':
                    $casts[] = "'{$field->fieldname}' => 'date'";
                    break;
                case 'datetime':
                    $casts[] = "'{$field->fieldname}' => 'datetime'";
                    break;
                case 'json':
                    $casts[] = "'{$field->fieldname}' => 'array'";
                    break;
            }
        }

        return empty($casts) ? '' : implode(",\n        ", $casts);
    }

    protected function generateRelationships(Doctype $doctype): string
    {
        // For now, return empty string. Can be extended for foreign key relationships
        return '';
    }

    protected function generateScopes(Doctype $doctype): string
    {
        $scopes = [];

        // Generate scopes for filterable fields
        foreach ($doctype->doctypeFields as $field) {
            if ($field->in_standard_filter) {
                $scopeName = 'scopeBy' . Str::studly($field->fieldname);
                $scopes[] = "public function {$scopeName}(\$query, \$value)\n    {\n        return \$query->where('{$field->fieldname}', \$value);\n    }";
            }
        }

        return implode("\n\n    ", $scopes);
    }

    protected function generateFilterLogic(Doctype $doctype): string
    {
        $filters = [];

        foreach ($doctype->doctypeFields as $field) {
            if ($field->in_standard_filter) {
                $camelField = Str::camel($field->fieldname);
                $filters[] = "if (\$request->has('{$camelField}')) {\n            \$query->where('{$field->fieldname}', \$request->get('{$camelField}'));\n        }";
            }
        }

        return implode("\n\n        ", $filters);
    }

    protected function generateValidationRules(Doctype $doctype): string
    {
        $rules = [];

        foreach ($doctype->doctypeFields as $field) {
            $fieldRules = [];

            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field->fieldtype) {
                case 'text':
                case 'textarea':
                    $fieldRules[] = 'string';
                    if ($field->options && isset($field->options['maxLength'])) {
                        $fieldRules[] = 'max:' . $field->options['maxLength'];
                    }
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    if ($field->options && isset($field->options['min'])) {
                        $fieldRules[] = 'min:' . $field->options['min'];
                    }
                    if ($field->options && isset($field->options['max'])) {
                        $fieldRules[] = 'max:' . $field->options['max'];
                    }
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;
                case 'select':
                    if ($field->options && isset($field->options['options'])) {
                        $options = implode(',', $field->options['options']);
                        $fieldRules[] = "in:{$options}";
                    }
                    break;
                case 'json':
                    $fieldRules[] = 'array';
                    break;
            }

            if ($field->unique) {
                $fieldRules[] = 'unique:' . Str::snake(Str::plural($doctype->name)) . ',' . $field->fieldname;
            }

            $rules[] = "'{$field->fieldname}' => '" . implode('|', $fieldRules) . "'";
        }

        return implode(",\n            ", $rules);
    }

    protected function generateValidationMessages(Doctype $doctype): string
    {
        $messages = [];

        foreach ($doctype->doctypeFields as $field) {
            if ($field->required) {
                $messages[] = "'{$field->fieldname}.required' => 'The {$field->label} field is required.'";
            }

            if ($field->unique) {
                $messages[] = "'{$field->fieldname}.unique' => 'The {$field->label} has already been taken.'";
            }
        }

        return implode(",\n            ", $messages);
    }

    protected function generateResourceFields(Doctype $doctype): string
    {
        $fields = ["'id' => \$this->id"];

        foreach ($doctype->doctypeFields as $field) {
            $fields[] = "'{$field->fieldname}' => \$this->{$field->fieldname}";
        }

        $fields[] = "'created_at' => \$this->created_at";
        $fields[] = "'updated_at' => \$this->updated_at";

        return implode(",\n            ", $fields);
    }

    protected function generateMigrationFields(Doctype $doctype): string
    {
        $fields = [];

        foreach ($doctype->doctypeFields as $field) {
            $fieldDefinition = $this->getMigrationFieldDefinition($field);
            $fields[] = $fieldDefinition;
        }

        return implode("\n            ", $fields);
    }

    protected function getMigrationFieldDefinition($field): string
    {
        $definition = '';

        switch ($field->fieldtype) {
            case 'text':
            case 'email':
            case 'password':
                $definition = "\$table->string('{$field->fieldname}')";
                break;
            case 'textarea':
                $definition = "\$table->text('{$field->fieldname}')";
                break;
            case 'number':
                $definition = "\$table->decimal('{$field->fieldname}', 8, 2)";
                break;
            case 'date':
                $definition = "\$table->date('{$field->fieldname}')";
                break;
            case 'datetime':
                $definition = "\$table->datetime('{$field->fieldname}')";
                break;
            case 'time':
                $definition = "\$table->time('{$field->fieldname}')";
                break;
            case 'checkbox':
                $definition = "\$table->boolean('{$field->fieldname}')";
                break;
            case 'json':
                $definition = "\$table->json('{$field->fieldname}')";
                break;
            default:
                $definition = "\$table->string('{$field->fieldname}')";
        }

        if (!$field->required) {
            $definition .= '->nullable()';
        }

        if ($field->unique) {
            $definition .= '->unique()';
        }

        if ($field->default_value !== null) {
            $definition .= "->default('{$field->default_value}')";
        }

        $definition .= ';';

        return $definition;
    }

    /**
     * Generate form schema for frontend from Doctype
     */
    public function generateFormSchema(Doctype $doctype): array
    {
        $schema = [];

        foreach ($doctype->doctypeFields as $field) {
            $fieldSchema = [
                'name' => $field->fieldname,
                'label' => $field->label ?: Str::title(str_replace('_', ' ', $field->fieldname)),
                'type' => $this->mapFieldTypeToFormType($field->fieldtype),
                'required' => (bool) $field->reqd,
                'description' => $field->description,
                'placeholder' => $field->options['placeholder'] ?? '',
            ];

            // Add field-specific options
            $fieldSchema = $this->addFieldSpecificOptions($fieldSchema, $field);

            // Add validation rules
            $fieldSchema['validation'] = $this->generateFieldValidation($field);

            $schema[] = $fieldSchema;
        }

        return $schema;
    }

    /**
     * Map backend field types to frontend form types
     */
    protected function mapFieldTypeToFormType(string $fieldType): string
    {
        $mapping = [
            'Data' => 'text',
            'Text' => 'textarea',
            'Small Text' => 'text',
            'Long Text' => 'textarea',
            'Int' => 'number',
            'Float' => 'number',
            'Currency' => 'number',
            'Check' => 'checkbox',
            'Select' => 'select',
            'Link' => 'select',
            'Date' => 'date',
            'Datetime' => 'datetime',
            'Time' => 'time',
            'Attach' => 'file',
            'Attach Image' => 'image',
            'Password' => 'password',
            'Code' => 'textarea',
            'Text Editor' => 'textarea',
            'HTML Editor' => 'textarea',
            'JSON' => 'json',
        ];

        return $mapping[$fieldType] ?? 'text';
    }

    /**
     * Add field-specific options to schema
     */
    protected function addFieldSpecificOptions(array $schema, $field): array
    {
        $options = is_string($field->options) ? json_decode($field->options, true) : $field->options;

        if (!$options) {
            $options = [];
        }

        switch ($schema['type']) {
            case 'select':
                $schema['options'] = $options['options'] ?? [];
                break;

            case 'number':
                if (isset($options['min']))
                    $schema['min'] = $options['min'];
                if (isset($options['max']))
                    $schema['max'] = $options['max'];
                if (isset($options['step']))
                    $schema['step'] = $options['step'];
                break;

            case 'textarea':
                $schema['rows'] = $options['rows'] ?? 3;
                break;

            case 'file':
            case 'image':
                if (isset($options['accept']))
                    $schema['accept'] = $options['accept'];
                if (isset($options['multiple']))
                    $schema['multiple'] = $options['multiple'];
                break;
        }

        // Add any custom options
        if (isset($options['class']))
            $schema['class'] = $options['class'];
        if (isset($options['style']))
            $schema['style'] = $options['style'];

        return $schema;
    }

    /**
     * Generate field validation rules for frontend
     */
    protected function generateFieldValidation($field): array
    {
        $validation = [];

        if ($field->reqd) {
            $validation['required'] = true;
        }

        $options = is_string($field->options) ? json_decode($field->options, true) : $field->options;

        if ($options) {
            if (isset($options['min_length'])) {
                $validation['minLength'] = $options['min_length'];
            }

            if (isset($options['max_length'])) {
                $validation['maxLength'] = $options['max_length'];
            }

            if (isset($options['pattern'])) {
                $validation['pattern'] = $options['pattern'];
            }

            if (isset($options['min'])) {
                $validation['min'] = $options['min'];
            }

            if (isset($options['max'])) {
                $validation['max'] = $options['max'];
            }
        }

        // Add type-specific validation
        switch ($this->mapFieldTypeToFormType($field->fieldtype)) {
            case 'email':
                $validation['type'] = 'email';
                break;
            case 'url':
                $validation['type'] = 'url';
                break;
            case 'number':
                $validation['type'] = 'number';
                break;
        }

        return $validation;
    }
}
