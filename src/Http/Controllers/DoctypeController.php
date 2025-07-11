<?php

namespace Ngodingskuyy\Doctypes\Http\Controllers;

use Illuminate\Routing\Controller;
use Ngodingskuyy\Doctypes\Http\Requests\DoctypeRequest;
use Ngodingskuyy\Doctypes\Http\Resources\DoctypeResource;
use Ngodingskuyy\Doctypes\Models\Doctype;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DoctypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Doctype::query();

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by system status
        if ($request->has('system')) {
            $query->where('is_system', $request->boolean('system'));
        }

        // Search by name or label
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%");
            });
        }

        $doctypes = $query->with('doctypeFields')
            ->orderBy('label')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => DoctypeResource::collection($doctypes->items()),
            'meta' => [
                'current_page' => $doctypes->currentPage(),
                'last_page' => $doctypes->lastPage(),
                'per_page' => $doctypes->perPage(),
                'total' => $doctypes->total(),
            ]
        ]);
    }

    public function store(DoctypeRequest $request): JsonResponse
    {
        $doctype = Doctype::create($request->validated());

        // Add fields if provided
        if ($request->has('fields') && is_array($request->get('fields'))) {
            foreach ($request->get('fields') as $fieldData) {
                $doctype->addField($fieldData);
            }
        }

        return response()->json([
            'message' => 'Doctype created successfully',
            'data' => new DoctypeResource($doctype->load('doctypeFields'))
        ], 201);
    }

    public function show(Doctype $doctype): JsonResponse
    {
        return response()->json([
            'data' => new DoctypeResource($doctype->load('doctypeFields'))
        ]);
    }

    public function update(DoctypeRequest $request, Doctype $doctype): JsonResponse
    {
        $doctype->update($request->validated());

        // Update fields if provided
        if ($request->has('fields') && is_array($request->get('fields'))) {
            // Remove existing fields
            $doctype->doctypeFields()->delete();

            // Add new fields
            foreach ($request->get('fields') as $fieldData) {
                $doctype->addField($fieldData);
            }
        }

        return response()->json([
            'message' => 'Doctype updated successfully',
            'data' => new DoctypeResource($doctype->load('doctypeFields'))
        ]);
    }

    public function destroy(Doctype $doctype): JsonResponse
    {
        if ($doctype->isSystemDoctype()) {
            return response()->json([
                'message' => 'Cannot delete system doctype'
            ], 403);
        }

        $doctype->delete();

        return response()->json([
            'message' => 'Doctype deleted successfully'
        ]);
    }

    /**
     * Get form schema for a specific doctype
     */
    public function schema(string $doctypeName): JsonResponse
    {
        $doctype = Doctype::where('name', $doctypeName)
            ->with('doctypeFields')
            ->first();

        if (!$doctype) {
            return response()->json([
                'message' => 'DocType not found'
            ], 404);
        }

        // Use the model's generateFormSchema method
        $schema = $doctype->generateFormSchema();

        return response()->json([
            'data' => $schema,
            'message' => 'Form schema generated successfully'
        ]);
    }

    /**
     * Generate files from doctype
     */
    public function generate(Request $request, string $doctypeName): JsonResponse
    {
        $doctype = Doctype::where('name', $doctypeName)
            ->with('doctypeFields')
            ->first();

        if (!$doctype) {
            return response()->json([
                'message' => 'DocType not found'
            ], 404);
        }

        $types = $request->get('types', ['model', 'controller', 'request', 'resource', 'migration']);
        $force = $request->boolean('force', false);

        $generatorService = app(\Ngodingskuyy\Doctypes\Services\DoctypeGeneratorService::class);

        if ($request->boolean('preview', false)) {
            $results = [];
            foreach ($types as $type) {
                $results[$type] = $generatorService->getGenerationPreview($doctype, $type);
            }
        } else {
            $results = $generatorService->generateFiles($doctype, $types, $force);
        }

        return response()->json([
            'doctype' => $doctypeName,
            'generated' => $results
        ]);
    }

    public function addField(Request $request, Doctype $doctype): JsonResponse
    {
        $request->validate([
            'fieldname' => 'required|string|regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
            'label' => 'required|string|max:255',
            'fieldtype' => 'required|string|in:text,textarea,number,email,password,select,checkbox,date,datetime,time,file,image,json',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'unique' => 'boolean',
            'in_list_view' => 'boolean',
            'in_standard_filter' => 'boolean',
            'description' => 'nullable|string',
            'default_value' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        $field = $doctype->addField($request->validated());

        return response()->json([
            'message' => 'Field added successfully',
            'field' => $field->toFormField()
        ], 201);
    }

    public function updateField(Request $request, Doctype $doctype, string $fieldname): JsonResponse
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'fieldtype' => 'required|string|in:text,textarea,number,email,password,select,checkbox,date,datetime,time,file,image,json',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'unique' => 'boolean',
            'in_list_view' => 'boolean',
            'in_standard_filter' => 'boolean',
            'description' => 'nullable|string',
            'default_value' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        $updated = $doctype->updateField($fieldname, $request->validated());

        if (!$updated) {
            return response()->json([
                'message' => 'Field not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Field updated successfully'
        ]);
    }

    public function removeField(Doctype $doctype, string $fieldname): JsonResponse
    {
        $removed = $doctype->removeField($fieldname);

        if (!$removed) {
            return response()->json([
                'message' => 'Field not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Field removed successfully'
        ]);
    }
}
