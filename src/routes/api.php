<?php

use Illuminate\Support\Facades\Route;
use Ngodingskuyy\Doctypes\Http\Controllers\DoctypeApiController;
use Ngodingskuyy\Doctypes\Http\Controllers\DynamicDocumentController;
use Ngodingskuyy\Doctypes\Http\Controllers\DoctypeController;

// DocTypes API Routes
Route::prefix('api/doctypes')->middleware(config('doctypes.middleware', ['api']))->group(function () {

    // Main DocType CRUD routes
    Route::apiResource('doctypes', DoctypeApiController::class);

    // Additional DocType endpoints
    Route::get('doctypes/{doctype}/schema', [DoctypeApiController::class, 'getFormSchema']);
    Route::get('doctypes/{doctype}/list-config', [DoctypeApiController::class, 'getListConfig']);
    Route::get('doctypes/{doctype}/stats', [DoctypeApiController::class, 'getStats']);

    // File generation routes
    Route::post('doctypes/{doctype}/generate', [DoctypeController::class, 'generate']);
    Route::get('doctypes/{doctype}/generate/preview', [DoctypeController::class, 'getGenerationPreview']);

    // Field management routes
    Route::post('doctypes/{doctype}/fields', [DoctypeController::class, 'addField']);
    Route::put('doctypes/{doctype}/fields/{fieldname}', [DoctypeController::class, 'updateField']);
    Route::delete('doctypes/{doctype}/fields/{fieldname}', [DoctypeController::class, 'removeField']);
});

// Dynamic Document Management Routes
Route::prefix('api/doctypes/documents')->middleware(config('doctypes.middleware', ['api']))->group(function () {

    // Document CRUD for specific doctype
    Route::get('{doctype}', [DynamicDocumentController::class, 'index']);
    Route::post('{doctype}', [DynamicDocumentController::class, 'store']);
    Route::get('{doctype}/{id}', [DynamicDocumentController::class, 'show']);
    Route::get('{doctype}/{id}/edit', [DynamicDocumentController::class, 'edit']);
    Route::put('{doctype}/{id}', [DynamicDocumentController::class, 'update']);
    Route::delete('{doctype}/{id}', [DynamicDocumentController::class, 'destroy']);

    // Additional document operations
    Route::post('{doctype}/{id}/duplicate', [DynamicDocumentController::class, 'duplicate']);
});

// Legacy routes for backward compatibility (if needed)
Route::prefix('api')->middleware(config('doctypes.middleware', ['api']))->group(function () {
    // These routes can be kept for backward compatibility with existing implementations
    Route::get('{doctype}', [DynamicDocumentController::class, 'index']);
    Route::post('{doctype}', [DynamicDocumentController::class, 'store']);
    Route::get('{doctype}/{id}', [DynamicDocumentController::class, 'show']);
    Route::put('{doctype}/{id}', [DynamicDocumentController::class, 'update']);
    Route::delete('{doctype}/{id}', [DynamicDocumentController::class, 'destroy']);
});
