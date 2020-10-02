<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'PagesController@index');
Route::get('/better_release_radar', 'PagesController@better_release_radar');
Route::get('/about', 'PagesController@about');
Route::get('/credits', 'PagesController@credits');
Route::get('/contact', 'PagesController@contact');

//Spotify Authorization Routes
Route::get('/authorize_access', 'AuthorizationController@authorize_access');
Route::get('/get_access', 'AuthorizationController@get_access');
Route::get('/refresh_access', 'AuthorizationController@refresh_access');

//Mail Route
Route::post('/contact', 'ContactController@contact');

//Spotify APIs
Route::prefix('api/spotify')->group(function(){
    Route::get('/get_artists', 'SpotifyController@get_artists');
    Route::post('/create_playlist', 'SpotifyController@create_playlist');
    Route::post('/inspect_artist', 'SpotifyController@inspect_artist');
});
