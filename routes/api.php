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

Route::post("$lang/auth/register", 'Api\CaddieController@register');
Route::post("$lang/auth/login", 'Api\CaddieController@login');
Route::post("$lang/auth/logout", 'Api\CaddieController@logout');