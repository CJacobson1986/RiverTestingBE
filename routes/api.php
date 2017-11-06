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
Route::post('store', 'UserController@store');
Route::post('logIn', 'UserController@logIn');
Route::get('getUser', 'UserController@get');
Route::get('showUser/{id}', 'UserController@show');
Route::get('getUsers', 'UserController@index');
Route::post('updateUser', 'UserController@update');
Route::post('reviewUser', 'UserController@review');
Route::get('searchUsers/{search_term}', 'UserController@search');

Route::any('{path?}', 'MainController@index')->where("path", ".+");
