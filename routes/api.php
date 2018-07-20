<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api') -> group (function() {
    Route::apiResource('/v1/lots', 'Api\v1\LotController')->except(['destroy','update']);
    Route::apiResource('/v1/trades','Api\v1\TradeController')->only(['store']);   
});