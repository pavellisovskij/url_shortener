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

Route::get('/', 'HomeController@index')->name('home');

Route::get('/test', 'HomeController@test');

Route::post('/shortening', 'HomeController@shortening');

Route::get('/404', 'HomeController@error404');

Route::get('/data1', 'HomeController@data1');

Route::get('/data2', 'HomeController@data2');

Route::get('/d/{path}', 'HomeController@dashboard');

Route::get('/{path}', 'HomeController@redirector');