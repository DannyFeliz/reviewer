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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect("/home");
    }
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/save', 'HomeController@save');
Route::get("/notifier", "NotifierController@noAllowed");
Route::post("/notifier", "NotifierController@index");

Auth::routes();
