<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/category/store', 'CategoryController@store');
Route::get('/categories', 'CategoryController@index');
Route::get('/category/{id}', 'CategoryController@category');
Route::delete('/category/delete/{id}', 'CategoryController@delete');
Route::put('/category/edit/{id}', 'CategoryController@update');

Route::post('/product/store', 'ProductController@store');
Route::get('/products', 'ProductController@index');
Route::get('/product/{id}', 'ProductController@product');
Route::delete('/product/delete/{id}', 'ProductController@delete');
Route::put('/product/edit/{id}', 'ProductController@update');

Route::post('/product/search/options', 'ProductController@search');




//
