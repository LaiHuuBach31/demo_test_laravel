<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PostController;
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


Route::group(['prefix' => ''], function(){
    Route::get('', [IndexController::class, 'index'])->name('index');
    Route::resource('posts', PostController::class);
    Route::get('/get-all-post-data', [PostController::class, 'getAllPostData'])->name('getAllPostData');
    
    Route::resource('categories', CategoryController::class);
});