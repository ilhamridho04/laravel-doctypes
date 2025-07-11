<?php

namespace Ngodingskuyy\Doctypes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Doctype extends Model
{
    use HasFactory;

    protected $table = 'doctypes';

    protected $fillable = [
        'name',
        'label',
        'schema',
        'config',
    ];

    protected $casts = [
        'schema' => 'array',
        'config' => 'array',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(DoctypeDocument::class, 'doctype_id');
    }

    public function getTableAttribute(): string
    {
        return 'tab' . strtolower(str_replace(' ', '_', $this->name));
    }

    /**
     * Generate form schema for frontend use
     * Returns JSON schema compatible with GeneratedForm.vue
     */
    public function generateFormSchema(): array
    {
        $fields = [];

        // If using the related DoctypeField model (recommended)
        if ($this->doctypeFields()->exists()) {
            $fields = $this->doctypeFields->map(function ($field) {
                return [
                    'name' => $field->fieldname,
                    'label' => $field->label,
                    'type' => $field->fieldtype,
                    'required' => (bool) $field->required,
                    'description' => $field->description,
                    'placeholder' => $field->label,
                    'options' => $this->parseOptions($field->options),
                    'default_value' => $field->default_value,
                    // Additional properties for frontend
                    'validation' => [
                        'required' => (bool) $field->required,
                        'unique' => (bool) $field->unique,
                    ],
                    // UI properties
                    'in_list_view' => (bool) $field->in_list_view,
                    'in_filter' => (bool) $field->in_standard_filter,
                    'sort_order' => $field->sort_order ?? 0,
                ];
            })->sortBy('sort_order')->values()->toArray();
        } else {
            // Fallback: if using the fields JSON column
            if ($this->fields && is_array($this->fields)) {
                $fields = collect($this->fields)->map(function ($field, $index) {
                    return [
                        'name' => $field['fieldname'] ?? $field['name'] ?? "field_{$index}",
                        'label' => $field['label'] ?? ucfirst($field['name'] ?? "Field {$index}"),
                        'type' => $field['fieldtype'] ?? $field['type'] ?? 'text',
                        'required' => (bool) ($field['required'] ?? false),
                        'description' => $field['description'] ?? '',
                        'placeholder' => $field['label'] ?? ucfirst($field['name'] ?? "Field {$index}"),
                        'options' => $this->parseOptions($field['options'] ?? null),
                        'default_value' => $field['default_value'] ?? null,
                        'validation' => [
                            'required' => (bool) ($field['required'] ?? false),
                            'unique' => (bool) ($field['unique'] ?? false),
                        ],
                        'sort_order' => $field['sort_order'] ?? $index,
                    ];
                })->sortBy('sort_order')->values()->toArray();
            }
        }

        return [
            'doctype' => $this->name,
            'title' => $this->label,
            'description' => $this->description,
            'fields' => $fields,
            'settings' => $this->settings ?? [],
            'metadata' => [
                'id' => $this->id,
                'is_active' => $this->is_active,
                'is_system' => $this->is_system,
                'icon' => $this->icon,
                'color' => $this->color,
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ]
        ];
    }

    /**
     * Parse options string to array for select fields
     */
    private function parseOptions($options): array
    {
        if (empty($options)) {
            return [];
        }

        if (is_array($options)) {
            return $options;
        }

        if (is_string($options)) {
            // Handle comma-separated values: "option1,option2,option3"
            return array_map('trim', explode(',', $options));
        }

        return [];
    }

    public function addField(array $fieldData)
    {
        return $this->doctypeFields()->create($fieldData);
    }

    public function updateField(string $fieldname, array $fieldData): bool
    {
        return $this->doctypeFields()
            ->where('fieldname', $fieldname)
            ->first()
                ?->update($fieldData) ?? false;
    }

    public function removeField(string $fieldname): bool
    {
        return $this->doctypeFields()
            ->where('fieldname', $fieldname)
            ->delete() > 0;
    }

    public function getListViewFields(): array
    {
        return $this->doctypeFields
            ->where('in_list_view', true)
            ->pluck('fieldname')
            ->toArray();
    }

    public function getFilterFields(): array
    {
        return $this->doctypeFields
            ->where('in_standard_filter', true)
            ->pluck('fieldname')
            ->toArray();
    }

    public function isSystemDoctype(): bool
    {
        return $this->is_system;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotSystem($query)
    {
        return $query->where('is_system', false);
    }
}
