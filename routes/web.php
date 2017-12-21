<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

$prefix_admin = 'admin';

Route::get($prefix_admin, function () use ($prefix_admin) {
    return redirect("$prefix_admin/login");
});

Route::get("$prefix_admin/login",'Backend\LoginController@getLogin');
Route::post("$prefix_admin/login",'Backend\LoginController@postLogin');
Route::get("$prefix_admin/logout",'Backend\LoginController@getLogout');

Route::group(['middleware'=>'admin','prefix'=>$prefix_admin],function(){
    Route::get('profile','Backend\LoginController@getProfile');

    Route::resource('tournament','Backend\TournamentController');
});