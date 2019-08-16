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

	/* 
	*	Users and Permissions 
	*/
	Route::group(['middleware' => 'role_or_permission:admin|view users' ], function () {
		Route::get( '/users', 'UsersController@index' );
		Route::get( '/roles', 'RolesController@index' );
		Route::get( '/permissions', 'PermissionsController@index' );
	});

	Route::group(['middleware' => 'role_or_permission:admin|edit users' ], function () {
		// users
		Route::get( '/users/form/{user?}', 'UsersController@form' );
		Route::patch( '/users/{user?}', 'UsersController@update' );
		Route::post( '/users', 'UsersController@store' );
		Route::delete( '/users/{user}', 'UsersController@destroy' );

		// roles
		Route::get( '/roles/form/{role?}', 'RolesController@form' );
		Route::get( '/roles/show/{role?}', 'RolesController@show' );
		Route::patch( '/roles/{role?}', 'RolesController@update' );
		Route::post( '/roles', 'RolesController@store' );
		Route::delete( '/roles/{role}', 'RolesController@destroy' );
		Route::post( '/roles/{role?}/permissions', 'RolesController@updatePermissions' );

		// permissions
		Route::get( '/permissions/form/{perm?}', 'PermissionsController@form' );
		Route::patch( '/permissions/{perm?}', 'PermissionsController@update' );
		Route::post( '/permissions', 'PermissionsController@store' );
		Route::delete( '/permissions/{perm}', 'PermissionsController@destroy' );

	});

	/* 
	*	Apple Classroom
	*/
	Route::group(['middleware' => 'role_or_permission:admin|edit appleclassroom' ], function () {
		Route::get( '/appleclassroom', 'AppleClassroomController@index' );
		Route::post( '/appleclassroom/upload', 'AppleClassroomController@upload' );
		Route::post( '/appleclassroom/update', 'AppleClassroomController@update' );
	});

	Route::group(['middleware' => 'role_or_permission:admin|view appleclassroom' ], function () {
		Route::get( '/appleclassroom/archives', 'AppleClassroomController@archives' );
		Route::get( '/appleclassroom/download/{archive}', 'AppleClassroomController@download' );
	});

	/* 
	*	Cisco
	*/
	Route::group(['middleware' => 'role_or_permission:admin|view cisco' ], function () {
		Route::get( '/cisco/search', 'CiscoController@searchIndex' );
		Route::post( '/cisco/search', 'CiscoController@search' );
		Route::get( '/cisco/wipe', 'CiscoController@wipeIndex' );
		Route::post( '/cisco/wipe', 'CiscoController@wipe' );
	});
});

