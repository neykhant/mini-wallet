<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/test', function(){
//     return 'hello';
// });


Route::namespace('Api')->group(function(){
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');


    Route::middleware('auth:api')->group(function(){

        Route::post('logout', 'AuthController@logout');
        Route::get('profile', 'PageController@profile');

        Route::get('transaction', 'PageController@transaction');
        Route::get('transaction/{trx_id}', 'PageController@transactionDetail');

        Route::get('notification', 'PageController@notification');
        Route::get('notification/{id}', 'PageController@notificationDetail');

        Route::get('to-account-verify', 'PageController@toAccountVerify');
        Route::get('transfer/confirm', 'PageController@transferConfirm');
    });
});

