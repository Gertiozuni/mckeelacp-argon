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
	});

	Route::group(['middleware' => 'role_or_permission:admin|view cisco' ], function () {
		Route::get( '/cisco/wipe', 'CiscoController@wipeIndex' );
		Route::post( '/cisco/wipe', 'CiscoController@wipe' );
	});

	/*
	*	Campuses
	*/
	Route::get( '/campuses', 'CampusController@index' )->middleware( 'role_or_permission:admin|view campus' );

	Route::group(['middleware' => 'role_or_permission:admin|edit campus' ], function () {
		Route::get( '/campuses/form/{campus?}', 'CampusController@form' );
		Route::post( '/campuses', 'CampusController@store' );
		Route::patch( '/campuses/{campus}', 'CampusController@update' );
		Route::delete( '/campuses/{campus}', 'CampusController@destroy' );
	});

	/*
	*	Network
	*/
	Route::prefix( 'network' )->group( function () {
		Route::group(['middleware' => 'role_or_permission:admin|view network' ], function () {
			Route::get( 'vlans', 'VlanController@index' );
			
			Route::get( 'switches', 'SwitchesController@index' );
			Route::get( 'switch/{switch}', 'SwitchController@index' );
			Route::get( 'switch/{switch}/logs', 'SwitchController@logs');

			Route::get( 'port/{port}/logs', 'PortController@logs');

		});

		Route::group(['middleware' => 'role_or_permission:admin|edit network' ], function () {
			
			/* Vlans */
			Route::get( 'vlans/form/{vlan?}', 'VlanController@form' );
			Route::post( 'vlans', 'VlanController@store' );
			Route::patch( 'vlans/{vlan?}', 'VlanController@update' );
            Route::delete( 'vlans/{vlan?}', 'VlanController@destroy' );

			/* Switches */
            Route::get( 'switches/form/{switch?}', 'SwitchesController@form' );
            Route::post( 'switches', 'SwitchesController@store' );
			Route::patch('switches/{switch}', 'SwitchesController@update');
			Route::delete('switches/{switch?}', 'SwitchesController@destroy');

			/* Switch */
            //Route::get( 'switches/form/{switch?}', 'SwitchesController@form' );
		});

		Route::group(['middleware' => 'role_or_permission:admin|edit port' ], function () {
			Route::patch('port/{port}', 'PortController@updateDescription');
			Route::patch('port/{port}/mode', 'PortController@updateMode');
			Route::patch('port/{port}/vlans', 'PortController@updateVlans');
		});

	});

	Route::prefix( 'profile' )->group( function () {
		Route::get( '/', 'ProfileController@index' );\
		Route::patch( '/{user}', 'ProfileController@update' );
	});
});

