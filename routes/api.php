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

$lang = InitLanguage(2);

Route::post("$lang/auth/register", 'Auth\LoginController@register');
Route::post("$lang/auth/login", 'Auth\LoginController@login');
Route::post("$lang/auth/logout", 'Auth\LoginController@logout');



Route::group(['prefix'=>$lang, 'middleware' => 'jwt.auth'], function () {
	Route::post("auth/change-password", 'Auth\LoginController@ChangePassword');
	Route::post("accept-booking",'Api\CaddieController@acceptbooking');
	Route::post("finish-booking",'Api\CaddieController@finishbooking');
	Route::post("change-notification",'Api\CaddieController@ChangeNotification');
	Route::get("config",'Api\CaddieController@Config');

	
});