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

	/* Users and Permissions */
	Route::group(['middleware' => 'role_or_permission:admin|view users' ], function () {
		Route::get( '/users', 'UsersController@index' );
		Route::get( '/roles', 'RolesController@index' );
		Route::get( '/permissions', 'PermissionsController@index' );
	});

	Route::group(['middleware' => 'role_or_permission:admin|edit users' ], function () {
		Route::get( '/users/form/{user?}', 'UsersController@form' );
		Route::patch( '/users/{user?}', 'UsersController@update' );
		Route::post( '/users', 'UsersController@store' );
		Route::delete( '/users/{user?}', 'UsersController@destroy' );

		Route::get( '/roles/form/{role?}', 'RolesController@form' );
		Route::patch( '/roles/{role?}', 'RolesController@update' );
		Route::post( '/roles', 'RolesController@store' );
		Route::delete( '/roles/{role?}', 'RolesController@destroy' );
	});

});

