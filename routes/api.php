<?php

use App\Http\Controllers\DemoController;
use App\Services\JourneyStatsService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\UserAuthController;

Route::post( 'register', [ UserAuthController::class, 'register' ] );
Route::post( 'login', [ UserAuthController::class, 'login' ] );

Route::middleware( 'auth:sanctum' )->group( function () {
    Route::post( 'logout', [ UserAuthController::class, 'logout' ] );
    Route::get( 'user', [ UserAuthController::class, 'getUser' ] );
    Route::put( 'user', [ UserAuthController::class, 'updateUser' ] );

    Route::get( 'create-demo', [ DemoController::class, 'createDemo' ] );
    Route::get( 'calculate-demo', [ DemoController::class, 'calculateDemo' ] );
    Route::delete( 'remove-demo', [ DemoController::class, 'removeDemo' ] );

    Route::get( 'journey-stats', function () {
        // Assuming the user is authenticated through token or session
        $user = auth()->user();

        // Get the statistics from the JourneyStatsService
        $stats = ( new JourneyStatsService() )->getStats( $user );

        // Return the statistics as JSON response
        return response()->json( [ 'stats' => $stats ] );
    } );
} );
