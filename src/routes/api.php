<?php

use Illuminate\Support\Facades\Route;
use Doctypes\Http\Controllers\DoctypeController;
use Doctypes\Http\Controllers\DynamicModelController;

Route::prefix('api/doctypes')->middleware(config('doctypes.middleware', ['api']))->group(function () {
    // Main CRUD routes
    Route::apiResource('doctypes', DoctypeController::class);

    // Generator route
    Route::post('doctypes/{doctype}/generate', [DoctypeController::class, 'generate']);

    // Additional routes for specific doctype operations
    Route::get('doctypes/{doctype}/schema', [DoctypeController::class, 'schema']);
    Route::post('doctypes/{doctype}/fields', [DoctypeController::class, 'addField']);
    Route::put('doctypes/{doctype}/fields/{fieldname}', [DoctypeController::class, 'updateField']);
    Route::delete('doctypes/{doctype}/fields/{fieldname}', [DoctypeController::class, 'removeField']);
});

// Dynamic model routes for generated DocTypes
Route::prefix('api')->middleware(config('doctypes.middleware', ['api']))->group(function () {
    Route::get('{doctype}', [DynamicModelController::class, 'index']);
    Route::post('{doctype}', [DynamicModelController::class, 'store']);
    Route::get('{doctype}/{id}', [DynamicModelController::class, 'show']);
    Route::put('{doctype}/{id}', [DynamicModelController::class, 'update']);
    Route::delete('{doctype}/{id}', [DynamicModelController::class, 'destroy']);
});
