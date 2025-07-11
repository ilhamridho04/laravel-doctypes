<?php

namespace Ngodingskuyy\Doctypes\Services;

use Ngodingskuyy\Doctypes\Models\Doctype;
use Ngodingskuyy\Doctypes\Models\DoctypeDocument;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DoctypeService
{
    /**
     * Get all doctypes with optional filtering
     */
    public function getAllDoctypes(array $filters = []): Collection
    {
        $cacheKey = 'doctypes_all_' . md5(serialize($filters));

        return Cache::remember($cacheKey, 3600, function () use ($filters) {
            $query = Doctype::with('doctypeFields');

            if (isset($filters['active'])) {
                $query->where('is_active', $filters['active']);
            }

            if (isset($filters['system'])) {
                $query->where('is_system', $filters['system']);
            }

            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('label', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('label')->get();
        });
    }

    /**
     * Get paginated doctypes
     */
    public function getPaginatedDoctypes(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Doctype::with('doctypeFields');

        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        if (isset($filters['system'])) {
            $query->where('is_system', $filters['system']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('label')->paginate($perPage);
    }

    /**
     * Create new doctype with fields
     */
    public function createDoctype(array $data): Doctype
    {
        return DB::transaction(function () use ($data) {
            $doctype = Doctype::create([
                'name' => $data['name'],
                'label' => $data['label'],
                'description' => $data['description'] ?? null,
                'schema' => $data['schema'] ?? [],
                'config' => $data['config'] ?? [],
            ]);

            // Add fields if provided
            if (isset($data['fields']) && is_array($data['fields'])) {
                foreach ($data['fields'] as $fieldData) {
                    $doctype->addField($fieldData);
                }
            }

            $this->clearCache();

            return $doctype->load('doctypeFields');
        });
    }

    /**
     * Update doctype
     */
    public function updateDoctype(Doctype $doctype, array $data): Doctype
    {
        return DB::transaction(function () use ($doctype, $data) {
            $doctype->update([
                'name' => $data['name'] ?? $doctype->name,
                'label' => $data['label'] ?? $doctype->label,
                'description' => $data['description'] ?? $doctype->description,
                'schema' => $data['schema'] ?? $doctype->schema,
                'config' => $data['config'] ?? $doctype->config,
            ]);

            // Update fields if provided
            if (isset($data['fields']) && is_array($data['fields'])) {
                // Remove existing fields
                $doctype->doctypeFields()->delete();

                // Add new fields
                foreach ($data['fields'] as $fieldData) {
                    $doctype->addField($fieldData);
                }
            }

            $this->clearCache();

            return $doctype->load('doctypeFields');
        });
    }

    /**
     * Delete doctype
     */
    public function deleteDoctype(Doctype $doctype): bool
    {
        if ($doctype->isSystemDoctype()) {
            throw new \Exception('Cannot delete system doctype');
        }

        // Check if doctype has documents
        if ($doctype->documents()->count() > 0) {
            throw new \Exception('Cannot delete doctype with existing documents');
        }

        $result = $doctype->delete();
        $this->clearCache();

        return $result;
    }

    /**
     * Get documents for a specific doctype
     */
    public function getDocuments(string $doctypeName, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $doctype = Doctype::where('name', $doctypeName)->firstOrFail();

        $query = DoctypeDocument::where('doctype_id', $doctype->id)
            ->with(['doctype', 'creator', 'updater']);

        // Apply search filter
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply date filters
        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        // Apply field filters
        if (isset($filters['fields']) && is_array($filters['fields'])) {
            foreach ($filters['fields'] as $field => $value) {
                $query->whereJsonContains('data->' . $field, $value);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create new document
     */
    public function createDocument(string $doctypeName, array $data, ?int $userId = null): DoctypeDocument
    {
        $doctype = Doctype::where('name', $doctypeName)->firstOrFail();

        return DB::transaction(function () use ($doctype, $data, $userId) {
            $document = new DoctypeDocument([
                'doctype_id' => $doctype->id,
                'data' => $data,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $document->saveDocument();

            return $document->load(['doctype', 'creator', 'updater']);
        });
    }

    /**
     * Update document
     */
    public function updateDocument(int $documentId, array $data, ?int $userId = null): DoctypeDocument
    {
        $document = DoctypeDocument::findOrFail($documentId);

        return DB::transaction(function () use ($document, $data, $userId) {
            $document->updated_by = $userId;
            $document->saveDocument($data);

            return $document->load(['doctype', 'creator', 'updater']);
        });
    }

    /**
     * Delete document
     */
    public function deleteDocument(int $documentId): bool
    {
        $document = DoctypeDocument::findOrFail($documentId);
        return $document->delete();
    }

    /**
     * Get document by ID
     */
    public function getDocument(int $documentId): DoctypeDocument
    {
        return DoctypeDocument::with(['doctype', 'creator', 'updater'])->findOrFail($documentId);
    }

    /**
     * Get form schema for a doctype
     */
    public function getFormSchema(string $doctypeName): array
    {
        $cacheKey = "doctype_schema_{$doctypeName}";

        return Cache::remember($cacheKey, 3600, function () use ($doctypeName) {
            $doctype = Doctype::where('name', $doctypeName)
                ->with('doctypeFields')
                ->firstOrFail();

            return $doctype->generateFormSchema();
        });
    }

    /**
     * Validate document data against doctype schema
     */
    public function validateDocumentData(string $doctypeName, array $data): bool
    {
        $doctype = Doctype::where('name', $doctypeName)->firstOrFail();
        $rules = $doctype->getValidationRules();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Get list view configuration for a doctype
     */
    public function getListViewConfig(string $doctypeName): array
    {
        $doctype = Doctype::where('name', $doctypeName)
            ->with('doctypeFields')
            ->firstOrFail();

        $listFields = $doctype->getListViewFields();
        $filterFields = $doctype->getFilterFields();

        $fieldDefinitions = [];
        foreach ($doctype->doctypeFields as $field) {
            if (in_array($field->fieldname, $listFields) || in_array($field->fieldname, $filterFields)) {
                $fieldDefinitions[$field->fieldname] = [
                    'label' => $field->label,
                    'type' => $field->fieldtype,
                    'in_list' => in_array($field->fieldname, $listFields),
                    'in_filter' => in_array($field->fieldname, $filterFields),
                ];
            }
        }

        return [
            'doctype' => $doctypeName,
            'title' => $doctype->label,
            'fields' => $fieldDefinitions,
            'list_fields' => $listFields,
            'filter_fields' => $filterFields,
        ];
    }

    /**
     * Get statistics for a doctype
     */
    public function getDoctypeStats(string $doctypeName): array
    {
        $doctype = Doctype::where('name', $doctypeName)->firstOrFail();

        $totalDocuments = $doctype->documents()->count();
        $todayDocuments = $doctype->documents()->whereDate('created_at', today())->count();
        $thisWeekDocuments = $doctype->documents()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthDocuments = $doctype->documents()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return [
            'total' => $totalDocuments,
            'today' => $todayDocuments,
            'this_week' => $thisWeekDocuments,
            'this_month' => $thisMonthDocuments,
        ];
    }

    /**
     * Clear cache
     */
    protected function clearCache(): void
    {
        Cache::tags(['doctypes'])->flush();
    }
}
