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
Auth::routes();

Route::group(['middleware' => 'auth'], function () {

	Route::get('/', 'HomeController@index')->name('home');

	/* Users */
	Route::get( '/users', 'UserController@index' )->middleware('role_or_permission:admin|view users');

	Route::group(['middleware' => 'role_or_permission:admin|edit users' ], function () {
		Route::get( '/users/form/{user?}', 'UserController@form' );
		Route::patch( '/users/{user?}', 'UserController@update' );
		Route::post( '/users', 'UserController@store' );
		Route::delete( '/users/{user?}', 'UserController@destroy' );
	});

});

