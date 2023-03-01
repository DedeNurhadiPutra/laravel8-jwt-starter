<?php

use App\Http\Controllers\AuthController;
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


Route::group(['prefix' => '/'], function ($router) {
    //route Auth
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::get('refresh-token', [AuthController::class, 'refreshToken']);
    });

    Route::group(['middleware' => 'api'], function ($router) {
        // route dashboard
        // Route::group(['prefix' => 'products'], function ($router) {
        //     Route::get('get-products', [ProductsController::class, 'getProducts']);
        //     Route::get('get-detail-product/{uuid}', [ProductsController::class, 'detProduct']);
        //     Route::post('add-product', [ProductsController::class, 'addProduct']);
        //     Route::put('edit-product/{uuid}', [ProductsController::class, 'editProduct']);
        //     Route::delete('del-product/{uuid}', [ProductsController::class, 'delProduct']);
        // });
    });
});
