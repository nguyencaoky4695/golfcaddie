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

Route::resource("$lang/tournament",'Api\TournamentController');

Route::group(['prefix'=>$lang, 'middleware' => 'jwt.auth'], function () {

});