<?php
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(
	[
		'prefix' => '/api/surelog'
	],
	function ($router) {
		$router->get('version/{platform}', 'ApplicationController@version');
		
		$router->group(
			[
				'middleware' => 'auth'
			],
			function ($router) {
				$router->post('login', 'UsersController@login');
				$router->get('/test', function () {
					return 'Hello, world';
				}
			);
		}
	);

	$router->group(
		[
			'middleware' => 'jwt_auth'
		],
		function ($router) {
			$router->get('me', 'UsersController@me');

			$router->post('logout', 'UsersController@logout');

			$router->post('password/change', 'UsersController@changePassword');

			$router->group(
				[
					'middleware' => 'role_checker:employees',
					'prefix' => 'employees'
				], 
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'UsersController@index']);
					$router->post('/store', ['as' => 'add', 'uses' => 'UsersController@store']);
					$router->get('/{id}', ['as' => 'read', 'uses' => 'UsersController@show']);
					$router->post('/delete/{id}', ['as' => 'delete', 'uses' => 'UsersController@destroy']);
					$router->post('/update/{id}', ['as' => 'edit', 'uses' => 'UsersController@update']);
				}
			);

			$router->group(
				[
					'middleware' => 'role_checker:hr-data',
					'prefix' => 'hr-data'
				],
				function ($router) {
					$router->get('/index', ['as' => 'browse', 'uses' => 'HrDataController@index']);
					$router->post('/update', ['as' => 'edit', 'uses' => 'HrDataController@update']);
				}
			);

			$router->get('hr-data', 'HrDataController@getHrData');

			$router->group(
				[
					'middleware' => 'role_checker:holidays',
					'prefix' => 'holidays'
				],
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'HolidaysController@index']);
					$router->post('/', ['as' => 'add', 'uses' => 'HolidaysController@store']);
					$router->post('/{id}', ['as' => 'edit', 'uses' => 'HolidaysController@update']);
					$router->post('/delete/{id}', ['as' => 'delete', 'uses' => 'HolidaysController@destroy']);
				}
			);

			$router->group(
				[
					'middleware' => 'role_checker:audit-trails',
					'prefix' => 'audit-trails'
				],
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'AuditTrailsController@index']);
				}
			);

			$router->group(
				[
					'middleware' => 'role_checker:roles',
					'prefix' => 'roles'
				],
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'RolesController@index']);
					$router->post('/store', ['as' => 'add', 'uses' => 'RolesController@store']);
					$router->get('/{id}', ['as' => 'read', 'uses' => 'RolesController@show']);
					$router->post('/delete/{id}', ['as' => 'delete', 'uses' => 'RolesController@destroy']);
					$router->post('/update/{id}', ['as' => 'edit', 'uses' => 'RolesController@update']);
				}
			);

			$router->group(
				[
					'middleware' => 'role_checker:projects',
					'prefix' => 'projects'
				],
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'ProjectsController@index']);
					$router->post('/store', ['as' => 'add', 'uses' => 'ProjectsController@store']);
					$router->get('/{id}', ['as' => 'read', 'uses' => 'ProjectsController@show']);
					$router->post('/delete/{id}', ['as' => 'delete', 'uses' => 'ProjectsController@destroy']);
					$router->post('/update/{id}', ['as' => 'edit', 'uses' => 'ProjectsController@update']);
					$router->get('/authorities/{id}', ['as' => 'read', 'uses' => 'ProjectsController@getProjectAuthorities']);
					$router->post('/location/store', ['as' => 'add', 'uses' => 'ProjectsController@saveLocationRequest']);
				}
			);

			$router->group(
				[
					'middleware' => 'role_checker:accounting',
					'prefix' => 'accounting'
				],
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'AccountingController@index']);
					$router->post('/', ['as' => 'edit', 'uses' => 'AccountingController@updatePayroll']);
					$router->post('/bulk', ['as' => 'edit', 'uses' => 'AccountingController@bulkUpdatePayroll']);
				}
			);

			$router->group(
				[
					'middleware' => 'role_checker:approval',
					'prefix' => 'approval'
				],
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'ApprovalController@index']);
					$router->post('/update/{id}/{status}', ['as' => 'edit', 'uses' => 'ApprovalController@status']);
				}
			);

			$router->group(
				[
					// 'middleware' => 'role_checker:projects',
					'prefix' => 'posts'
				],
				function ($router) {
					$router->get('/', ['as' => 'browse', 'uses' => 'PostsController@index']);
					$router->post('/store', ['as' => 'add', 'uses' => 'PostsController@store']);
					$router->get('/{id}', ['as' => 'read', 'uses' => 'PostsController@show']);
					$router->post('/delete/{id}', ['as' => 'delete', 'uses' => 'PostsController@destroy']);
					$router->post('/update/{id}', ['as' => 'edit', 'uses' => 'PostsController@update']);
				}
			);

			$router->post('application/upload-image/{folder}', 'ApplicationController@storeImage');
			$router->post('attendance/send', 'AttendanceController@create');
			$router->get('modules', 'RolesController@getModules');
			$router->get('zipcodes', 'ZipcodesController@index');
			$router->get('my-projects', 'ProjectsController@myProjects');
		}
	);
});
