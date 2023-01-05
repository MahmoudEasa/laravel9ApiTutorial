<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\CategoriesController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// All routes / api here must be api authenticated
Route::group(['middleware' => ['api', 'checkPassword', 'checkLanguage']], function () {
    Route::post('get_main_categories', [CategoriesController::class, 'index']);
    Route::post('get_category', [CategoriesController::class, 'getCategory']);
    Route::post('change_category_status', [CategoriesController::class, 'changeStatus']);

    Route::group(['prefix' => 'admin'], function() {
        Route::post('login', [AuthController::class, 'login']);
    });

});

Route::group(['middleware'=>['api','checkPassword','changeLanguage', 'checkAdminToken:admin_api']], function(){
    Route::get('offers', [CategoriesController::class, 'index']);
});