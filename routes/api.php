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
    Route::group(['controller' => CategoriesController::class], function (){
        Route::post('get_main_categories', 'index');
        Route::post('get_category', 'getCategory');
        Route::post('change_category_status', 'changeStatus');
    });

    Route::group(['controller' => AuthController::class], function(){
        Route::group(['prefix' => 'admin'], function () {
            Route::get('login', 'login')->name('login');
            Route::post('login', 'login')->name('login');
            Route::post('logout', 'logout')->name('logout');
            Route::post('register', 'register')->name('register');
            Route::post('refresh', 'refresh')->name('refresh');

            // invalidate token security side

            // broken access controller user enumeration
        });

        Route::group(['prefix' => 'user'], function () {
            Route::get('login', 'login')->name('login');
            Route::post('login', 'login')->name('login');
            Route::post('logout', 'logout')->name('logout');
            Route::post('register', 'register')->name('register');
            Route::post('refresh', 'refresh')->name('refresh');

            // invalidate token security side

            // broken access controller user enumeration
        });

    });

    Route::group(['prefix' => 'user', 'middleware' => 'auth.guard:user_api'], function () {
        Route::post('profile', function () {
            return \Auth::user();
        });
    });

});

Route::group(['middleware'=>['api','checkPassword','changeLanguage', 'checkAdminToken:admin_api']], function(){
    Route::get('offers', [CategoriesController::class, 'index']);
});