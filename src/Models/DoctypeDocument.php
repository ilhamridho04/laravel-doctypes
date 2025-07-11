<?php

namespace Ngodingskuyy\Doctypes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DoctypeDocument extends Model
{
    use HasFactory;

    protected $table = 'doctype_documents';

    protected $fillable = [
        'doctype_id',
        'data',
        'name',
        'title',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $appends = ['display_name'];

    public function doctype(): BelongsTo
    {
        return $this->belongsTo(Doctype::class, 'doctype_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    /**
     * Get display name for the document (similar to Frappe's naming)
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->title) {
            return $this->title;
        }

        if ($this->name) {
            return $this->name;
        }

        // Try to get title from data based on doctype schema
        $titleField = $this->doctype?->getTitleField();
        if ($titleField && isset($this->data[$titleField])) {
            return $this->data[$titleField];
        }

        return "Document #{$this->id}";
    }

    /**
     * Get a specific field value from the data JSON
     */
    public function getFieldValue(string $fieldname, $default = null)
    {
        return $this->data[$fieldname] ?? $default;
    }

    /**
     * Set a specific field value in the data JSON
     */
    public function setFieldValue(string $fieldname, $value): self
    {
        $data = $this->data ?? [];
        $data[$fieldname] = $value;
        $this->data = $data;

        return $this;
    }

    /**
     * Validate document data against doctype schema
     */
    public function validateData(): bool
    {
        if (!$this->doctype) {
            throw new ValidationException('Doctype not found');
        }

        $rules = $this->doctype->getValidationRules();

        $validator = Validator::make($this->data ?? [], $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Get formatted data for display
     */
    public function getFormattedData(): array
    {
        $formatted = [];
        $data = $this->data ?? [];

        if (!$this->doctype) {
            return $data;
        }

        foreach ($this->doctype->doctypeFields as $field) {
            $value = $data[$field->fieldname] ?? null;

            $formatted[$field->fieldname] = [
                'label' => $field->label,
                'value' => $this->formatFieldValue($field, $value),
                'raw_value' => $value,
                'type' => $field->fieldtype,
            ];
        }

        return $formatted;
    }

    /**
     * Format field value based on field type
     */
    protected function formatFieldValue($field, $value)
    {
        if ($value === null) {
            return null;
        }

        switch ($field->fieldtype) {
            case 'date':
                return $value ? Carbon::parse($value)->format('Y-m-d') : null;
            case 'datetime':
                return $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
            case 'checkbox':
                return $value ? 'Yes' : 'No';
            case 'select':
                return $value;
            case 'currency':
                return number_format((float) $value, 2);
            case 'number':
                return is_numeric($value) ? (float) $value : $value;
            default:
                return $value;
        }
    }

    /**
     * Get list view data
     */
    public function getListViewData(): array
    {
        $listFields = $this->doctype?->getListViewFields() ?? [];
        $data = [];

        foreach ($listFields as $fieldname) {
            $data[$fieldname] = $this->getFieldValue($fieldname);
        }

        return array_merge($data, [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ]);
    }

    /**
     * Save document with validation
     */
    public function saveDocument(array $data = null): bool
    {
        if ($data !== null) {
            $this->data = array_merge($this->data ?? [], $data);
        }

        // Validate before saving
        $this->validateData();

        // Auto-generate name if not provided
        if (!$this->name) {
            $this->name = $this->generateName();
        }

        // Set title from data if available
        if (!$this->title) {
            $titleField = $this->doctype?->getTitleField();
            if ($titleField && isset($this->data[$titleField])) {
                $this->title = $this->data[$titleField];
            }
        }

        return $this->save();
    }

    /**
     * Generate unique name for document
     */
    protected function generateName(): string
    {
        $prefix = strtoupper(substr($this->doctype->name, 0, 3));
        $timestamp = now()->format('Ymd');
        $sequence = $this->getNextSequence();

        return "{$prefix}-{$timestamp}-{$sequence}";
    }

    /**
     * Get next sequence number for naming
     */
    protected function getNextSequence(): string
    {
        $today = now()->format('Y-m-d');

        $lastDocument = static::where('doctype_id', $this->doctype_id)
            ->whereDate('created_at', $today)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastDocument ? $lastDocument->id + 1 : 1;

        return str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for filtering by doctype
     */
    public function scopeOfDoctype($query, $doctypeName)
    {
        return $query->whereHas('doctype', function ($q) use ($doctypeName) {
            $q->where('name', $doctypeName);
        });
    }

    /**
     * Scope for searching in document data
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhereRaw("JSON_SEARCH(data, 'all', ?) IS NOT NULL", ["%{$search}%"]);
        });
    }
}
