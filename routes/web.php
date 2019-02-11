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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/projects', 'PageController@getProjects')->name('projects');

Route::get('/projects/google_sheets', 'GoogleSheetsController@getGoogleSheets');

Route::get('/projects/google_sheets/refreshSheetValues', 'GoogleSheetsController@refreshSheetValues');
