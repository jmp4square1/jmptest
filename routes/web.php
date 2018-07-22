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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index');

/* Private routes */
Route::get('/favourites', 'FavouriteController@index')->name('favourites');
Route::post('/favourites/ajax_add', 'FavouriteController@ajax_add')->name('ajax_add');
Route::post('/favourites/ajax_remove', 'FavouriteController@ajax_remove')->name('ajax_remove');
Route::get('/showfavourites/{key}', 'FavouriteController@show')->name('showfavourites')->where('key', '[A-Za-z0-9]+');





