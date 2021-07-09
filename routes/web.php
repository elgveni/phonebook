<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;

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

Route::get('/', [ HomeController::class, 'index' ]);

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/livesearch', [BookController::class, 'getBooks']);

Route::get('/remove/{id}', [BookController::class, 'removeUser']);

Route::get('add-user-form', [BookController::class, 'add_user_form'])->name('add-user-form');
Route::post('/add-user', [BookController::class, 'addUser']);
Route::post('/add-user-phone', [BookController::class, 'addUserPhone']);

Route::get('/get-user-phones', [BookController::class, 'getUserPhones']);

Auth::routes();
