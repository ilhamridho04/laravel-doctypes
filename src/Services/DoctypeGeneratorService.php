<?php

namespace Doctypes\Services;

use Doctypes\Models\Doctype;
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
                $content = $this->generateFile($type, $doctype);
                $results[$type] = $content;
            } catch (\Exception $e) {
                $results[$type] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    public function generateFile(string $type, Doctype $doctype): string
    {
        $stubPath = __DIR__ . '/../stubs/' . $this->stubs[$type];

        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found: {$stubPath}");
        }

        $stub = File::get($stubPath);
        $replacements = $this->getReplacements($type, $doctype);

        foreach ($replacements as $placeholder => $replacement) {
            $stub = str_replace($placeholder, $replacement, $stub);
        }

        return $stub;
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
}
