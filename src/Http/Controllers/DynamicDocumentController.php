<?php

namespace Ngodingskuyy\Doctypes\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ngodingskuyy\Doctypes\Models\DoctypeDocument;
use Ngodingskuyy\Doctypes\Services\DoctypeService;
use Illuminate\Support\Facades\Auth;

class DynamicDocumentController extends Controller
{
    public function __construct(
        private DoctypeService $doctypeService
    ) {
    }

    /**
     * Get list of documents for a specific doctype
     */
    public function index(Request $request, string $doctype): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'from_date' => $request->get('from_date'),
                'to_date' => $request->get('to_date'),
                'fields' => $request->get('filters', []),
            ];

            $documents = $this->doctypeService->getDocuments(
                $doctype,
                array_filter($filters),
                $request->get('per_page', 15)
            );

            return response()->json([
                'data' => $documents->items()->map(function ($doc) {
                    return $doc->getListViewData();
                }),
                'meta' => [
                    'current_page' => $documents->currentPage(),
                    'last_page' => $documents->lastPage(),
                    'per_page' => $documents->perPage(),
                    'total' => $documents->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get documents',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create new document
     */
    public function store(Request $request, string $doctype): JsonResponse
    {
        try {
            // Validate data against doctype schema
            $this->doctypeService->validateDocumentData($doctype, $request->all());

            $document = $this->doctypeService->createDocument(
                $doctype,
                $request->all(),
                Auth::id()
            );

            return response()->json([
                'message' => 'Document created successfully',
                'data' => $document->getFormattedData()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create document',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show specific document
     */
    public function show(string $doctype, int $id): JsonResponse
    {
        try {
            $document = $this->doctypeService->getDocument($id);

            // Verify document belongs to the correct doctype
            if ($document->doctype->name !== $doctype) {
                return response()->json([
                    'message' => 'Document not found for this doctype'
                ], 404);
            }

            return response()->json([
                'data' => $document->getFormattedData(),
                'meta' => [
                    'id' => $document->id,
                    'name' => $document->name,
                    'title' => $document->title,
                    'display_name' => $document->display_name,
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at,
                    'creator' => $document->creator ? [
                        'id' => $document->creator->id,
                        'name' => $document->creator->name,
                    ] : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get document',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update document
     */
    public function update(Request $request, string $doctype, int $id): JsonResponse
    {
        try {
            // Validate data against doctype schema
            $this->doctypeService->validateDocumentData($doctype, $request->all());

            $document = $this->doctypeService->updateDocument(
                $id,
                $request->all(),
                Auth::id()
            );

            // Verify document belongs to the correct doctype
            if ($document->doctype->name !== $doctype) {
                return response()->json([
                    'message' => 'Document not found for this doctype'
                ], 404);
            }

            return response()->json([
                'message' => 'Document updated successfully',
                'data' => $document->getFormattedData()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update document',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete document
     */
    public function destroy(string $doctype, int $id): JsonResponse
    {
        try {
            $document = $this->doctypeService->getDocument($id);

            // Verify document belongs to the correct doctype
            if ($document->doctype->name !== $doctype) {
                return response()->json([
                    'message' => 'Document not found for this doctype'
                ], 404);
            }

            $this->doctypeService->deleteDocument($id);

            return response()->json([
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete document',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get raw document data for editing
     */
    public function edit(string $doctype, int $id): JsonResponse
    {
        try {
            $document = $this->doctypeService->getDocument($id);

            // Verify document belongs to the correct doctype
            if ($document->doctype->name !== $doctype) {
                return response()->json([
                    'message' => 'Document not found for this doctype'
                ], 404);
            }

            return response()->json([
                'data' => $document->data ?? [],
                'meta' => [
                    'id' => $document->id,
                    'name' => $document->name,
                    'title' => $document->title,
                    'doctype' => $document->doctype->name,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get document data',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Duplicate document
     */
    public function duplicate(string $doctype, int $id): JsonResponse
    {
        try {
            $originalDocument = $this->doctypeService->getDocument($id);

            // Verify document belongs to the correct doctype
            if ($originalDocument->doctype->name !== $doctype) {
                return response()->json([
                    'message' => 'Document not found for this doctype'
                ], 404);
            }

            // Create new document with same data
            $newDocument = $this->doctypeService->createDocument(
                $doctype,
                $originalDocument->data ?? [],
                Auth::id()
            );

            return response()->json([
                'message' => 'Document duplicated successfully',
                'data' => $newDocument->getFormattedData()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to duplicate document',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
