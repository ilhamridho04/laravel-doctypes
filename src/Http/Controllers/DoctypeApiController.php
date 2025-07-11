<?php

namespace Ngodingskuyy\Doctypes\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ngodingskuyy\Doctypes\Http\Requests\DoctypeRequest;
use Ngodingskuyy\Doctypes\Http\Resources\DoctypeResource;
use Ngodingskuyy\Doctypes\Models\Doctype;
use Ngodingskuyy\Doctypes\Services\DoctypeService;

class DoctypeApiController extends Controller
{
    public function __construct(
        private DoctypeService $doctypeService
    ) {
    }

    /**
     * Get list of doctypes
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'active' => $request->boolean('active'),
            'system' => $request->boolean('system'),
            'search' => $request->get('search'),
        ];

        $doctypes = $this->doctypeService->getPaginatedDoctypes(
            array_filter($filters),
            $request->get('per_page', 15)
        );

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

    /**
     * Create new doctype
     */
    public function store(DoctypeRequest $request): JsonResponse
    {
        try {
            $doctype = $this->doctypeService->createDoctype($request->validated());

            return response()->json([
                'message' => 'Doctype created successfully',
                'data' => new DoctypeResource($doctype)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create doctype',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show specific doctype
     */
    public function show(Doctype $doctype): JsonResponse
    {
        return response()->json([
            'data' => new DoctypeResource($doctype->load('doctypeFields'))
        ]);
    }

    /**
     * Update doctype
     */
    public function update(DoctypeRequest $request, Doctype $doctype): JsonResponse
    {
        try {
            $updatedDoctype = $this->doctypeService->updateDoctype($doctype, $request->validated());

            return response()->json([
                'message' => 'Doctype updated successfully',
                'data' => new DoctypeResource($updatedDoctype)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update doctype',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete doctype
     */
    public function destroy(Doctype $doctype): JsonResponse
    {
        try {
            $this->doctypeService->deleteDoctype($doctype);

            return response()->json([
                'message' => 'Doctype deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete doctype',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get form schema for a doctype
     */
    public function getFormSchema(string $doctype): JsonResponse
    {
        try {
            $schema = $this->doctypeService->getFormSchema($doctype);

            return response()->json([
                'data' => $schema
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get form schema',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get list view configuration for a doctype
     */
    public function getListConfig(string $doctype): JsonResponse
    {
        try {
            $config = $this->doctypeService->getListViewConfig($doctype);

            return response()->json([
                'data' => $config
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get list configuration',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get statistics for a doctype
     */
    public function getStats(string $doctype): JsonResponse
    {
        try {
            $stats = $this->doctypeService->getDoctypeStats($doctype);

            return response()->json([
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
