<?php

namespace Ngodingskuyy\Doctypes\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DynamicModelController extends Controller
{
    /**
     * Handle dynamic model operations based on DocType
     */
    public function index(Request $request, string $doctypeName): JsonResponse
    {
        $tableName = Str::snake(Str::plural($doctypeName));

        if (!Schema::hasTable($tableName)) {
            return response()->json([
                'message' => "Table {$tableName} does not exist. Please generate the model first."
            ], 404);
        }

        $query = DB::table($tableName);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->get('search');
            // Add search logic based on searchable fields
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'data' => $results,
            'meta' => [
                'current_page' => (int) $page,
                'last_page' => ceil($total / $perPage),
                'per_page' => (int) $perPage,
                'total' => $total,
            ]
        ]);
    }

    /**
     * Store a new record
     */
    public function store(Request $request, string $doctypeName): JsonResponse
    {
        $tableName = Str::snake(Str::plural($doctypeName));

        if (!Schema::hasTable($tableName)) {
            return response()->json([
                'message' => "Table {$tableName} does not exist. Please generate the model first."
            ], 404);
        }

        $data = $request->all();

        // Add timestamps if columns exist
        if (Schema::hasColumn($tableName, 'created_at')) {
            $data['created_at'] = now();
        }
        if (Schema::hasColumn($tableName, 'updated_at')) {
            $data['updated_at'] = now();
        }

        $id = DB::table($tableName)->insertGetId($data);
        $record = DB::table($tableName)->find($id);

        return response()->json([
            'message' => 'Record created successfully',
            'data' => $record
        ], 201);
    }

    /**
     * Show a specific record
     */
    public function show(string $doctypeName, string $id): JsonResponse
    {
        $tableName = Str::snake(Str::plural($doctypeName));

        if (!Schema::hasTable($tableName)) {
            return response()->json([
                'message' => "Table {$tableName} does not exist."
            ], 404);
        }

        // Convert string ID to integer for database query
        $recordId = (int) $id;
        $record = DB::table($tableName)->find($recordId);

        if (!$record) {
            return response()->json([
                'message' => 'Record not found'
            ], 404);
        }

        return response()->json([
            'data' => $record
        ]);
    }

    /**
     * Update a record
     */
    public function update(Request $request, string $doctypeName, string $id): JsonResponse
    {
        $tableName = Str::snake(Str::plural($doctypeName));

        if (!Schema::hasTable($tableName)) {
            return response()->json([
                'message' => "Table {$tableName} does not exist."
            ], 404);
        }

        $data = $request->all();
        $recordId = (int) $id;

        // Add updated timestamp if column exists
        if (Schema::hasColumn($tableName, 'updated_at')) {
            $data['updated_at'] = now();
        }

        $updated = DB::table($tableName)
            ->where('id', $recordId)
            ->update($data);

        if (!$updated) {
            return response()->json([
                'message' => 'Record not found'
            ], 404);
        }

        $record = DB::table($tableName)->find($recordId);

        return response()->json([
            'message' => 'Record updated successfully',
            'data' => $record
        ]);
    }

    /**
     * Delete a record
     */
    public function destroy(string $doctypeName, string $id): JsonResponse
    {
        $tableName = Str::snake(Str::plural($doctypeName));

        if (!Schema::hasTable($tableName)) {
            return response()->json([
                'message' => "Table {$tableName} does not exist."
            ], 404);
        }

        $recordId = (int) $id;
        $deleted = DB::table($tableName)
            ->where('id', $recordId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Record not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Record deleted successfully'
        ]);
    }
}
