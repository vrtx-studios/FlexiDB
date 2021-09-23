<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlexiDBController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post( '/v1/login', [AuthController::class, 'login'] );

Route::middleware('auth:sanctum')->group( function() {

    $sApiBase = Config( 'flexidb.API_URI_BASE' );

    #region User-management
    Route::post( $sApiBase . 'register', [AuthController::class, 'createUser'] );
    Route::post( $sApiBase . 'logout', [AuthController::class, 'logout'] );
    Route::get( $sApiBase . 'tables', [AuthController::class, 'userTables'] );
    #endregion

    Route::post( $sApiBase . 'data', [FlexiDBController::class, 'parseRequest'] );

} );
