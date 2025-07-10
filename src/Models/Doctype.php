<?php

namespace Doctypes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctype extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'fields',
        'settings',
        'is_active',
        'is_system',
        'icon',
        'color',
    ];

    protected $casts = [
        'fields' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function doctypeFields(): HasMany
    {
        return $this->hasMany(DoctypeField::class)->orderBy('sort_order');
    }

    public function generateFormSchema(): array
    {
        $schema = [];

        // If using the fields JSON column
        if ($this->fields && is_array($this->fields)) {
            $schema = $this->fields;
        } else {
            // If using the related DoctypeField model
            $schema = $this->doctypeFields->map(function ($field) {
                return [
                    'name' => $field->fieldname,
                    'label' => $field->label,
                    'type' => $field->fieldtype,
                    'required' => $field->required,
                    'unique' => $field->unique,
                    'options' => $field->options,
                    'description' => $field->description,
                    'default_value' => $field->default_value,
                    'in_list_view' => $field->in_list_view,
                    'in_standard_filter' => $field->in_standard_filter,
                ];
            })->toArray();
        }

        return $schema;
    }

    public function addField(array $fieldData): DoctypeField
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
