<?php

namespace Ngodingskuyy\Doctypes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctypeField extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctype_id',
        'fieldname',
        'label',
        'fieldtype',
        'options',
        'required',
        'unique',
        'in_list_view',
        'in_standard_filter',
        'description',
        'default_value',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'unique' => 'boolean',
        'in_list_view' => 'boolean',
        'in_standard_filter' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function doctype(): BelongsTo
    {
        return $this->belongsTo(Doctype::class);
    }

    public function toFormField(): array
    {
        return [
            'name' => $this->fieldname,
            'label' => $this->label,
            'type' => $this->fieldtype,
            'required' => $this->required,
            'unique' => $this->unique,
            'options' => $this->options,
            'description' => $this->description,
            'default_value' => $this->default_value,
            'in_list_view' => $this->in_list_view,
            'in_standard_filter' => $this->in_standard_filter,
        ];
    }

    public function scopeRequired($query)
    {
        return $query->where('required', true);
    }

    public function scopeInListView($query)
    {
        return $query->where('in_list_view', true);
    }

    public function scopeInStandardFilter($query)
    {
        return $query->where('in_standard_filter', true);
    }

    public function scopeOrderBySortOrder($query)
    {
        return $query->orderBy('sort_order');
    }
}
