<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\AuthenticationException;

Route::group([
    'middleware' => 'api',
    'prefix' => 'affiliate'
], function () {
    // Rutas abiertas
    Route::patch('change_password', [App\Http\Controllers\Affiliate\AffiliateUserController::class, 'change_password']);
    Route::post('auth', [App\Http\Controllers\Affiliate\AffiliateUserController::class, 'auth']);
    // Rutas autenticadas con token
    Route::group([
        'middleware' => ['auth:sanctum']
    ], function () {
       Route::get('/credential_status/{id}', [App\Http\Controllers\Affiliate\AffiliateController::class, 'credential_status']);
       Route::apiResource('/affiliate', App\Http\Controllers\Affiliate\AffiliateController::class)->only(['index','show','update']);
       Route::get('/affiliate/{affiliate}/address', [App\Http\Controllers\Affiliate\AffiliateController::class, 'get_addresses']);
       Route::patch('/affiliate/{affiliate}/address', [App\Http\Controllers\Affiliate\AffiliateController::class, 'update_addresses']);
       Route::apiResource('/address', App\Http\Controllers\Affiliate\AddressController::class)->only(['store','update','destroy']);
       Route::apiResource('/degree', App\Http\Controllers\Affiliate\DegreeController::class)->only(['index','show']);
       Route::apiResource('/unit', App\Http\Controllers\Affiliate\UnitController::class)->only(['index','show']);
       Route::apiResource('/category',App\Http\Controllers\Affiliate\CategoryController::class)->only(['index','show']);
       Route::apiResource('/pension_entity',App\Http\Controllers\Affiliate\PensionEntityController::class)->only(['index','show']);
       Route::apiResource('/affiliate_state',App\Http\Controllers\Affiliate\AffiliateStateController::class)->only(['index','show']);
       Route::post('store', [App\Http\Controllers\Affiliate\AffiliateUserController::class, 'store']);
       Route::get('/affiliate_record/{affiliate}', [App\Http\Controllers\Affiliate\AffiliateController::class, 'get_record']);
    });
});