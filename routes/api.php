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

Route::post("$lang/golfer/register", 'Api\GolferController@register');
Route::post("$lang/auth/login", 'Auth\LoginController@login');
Route::post("$lang/auth/logout", 'Auth\LoginController@logout');

Route::group(['prefix'=>$lang, 'middleware' => 'jwt.auth'], function () {
    Route::resource("tournament",'Api\TournamentController');
    Route::resource("booking",'Api\BookingController');
    Route::post('booking/{id}/pay','Api\BookingController@payBooking');
    Route::post('booking/{id}/cancel','Api\BookingController@cancelBooking');

    Route::group(['prefix'=>'golfer'],function (){
        Route::post('update-profile','Api\GolferController@updateProfile');
    });
	Route::post("auth/change-password", 'Auth\LoginController@ChangePassword');
    Route::post("accept-booking",'Api\CaddieController@acceptbooking');
    Route::post("finish-booking",'Api\CaddieController@finishbooking');
    Route::post("change-notification",'Api\CaddieController@ChangeNotification');
    Route::get("config",'Api\CaddieController@Config');


});