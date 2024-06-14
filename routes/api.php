<?php

use App\Http\Middleware\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/employees',  [EmployeesController::class, 'index']);
Route::get('/{idp}/employees',  [EmployeesController::class, 'index']);
Route::post('/{idp}/employees',  [EmployeesController::class, 'create']);
Route::put('/{idp}/employees/{id}',  [EmployeesController::class, 'update'])->middleware(JsonResponse::class);
Route::get('/regions',  [EmployeesController::class, 'getRegions']);
