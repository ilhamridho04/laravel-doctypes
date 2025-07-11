<?php

namespace Doctypes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctypeDocument extends Model
{
    protected $fillable = [
        'doctype_id',
        'name',
        'title',
        'data',
        'docstatus',
        'idx',
        'owner',
        'modified_by'
    ];

    protected $casts = [
        'data' => 'array',
        'docstatus' => 'integer',
        'idx' => 'integer'
    ];

    public function doctype(): BelongsTo
    {
        return $this->belongsTo(Doctype::class);
    }

    public function getField(string $fieldname)
    {
        return $this->data[$fieldname] ?? null;
    }

    public function setField(string $fieldname, $value): void
    {
        $data = $this->data ?? [];
        $data[$fieldname] = $value;
        $this->data = $data;
    }
}
