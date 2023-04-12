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

Route::get('/pay', function () {
    return view('pay');
});

Route::get('/{locale?}', function () {
    return response('', 404);
})->name('main_domain');

Route::get('{locale?}/thanks', function () {
    return response('', 404);
})->name('main_domain_thanks');