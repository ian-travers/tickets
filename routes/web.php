<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mockups/order', function () {
    return view('mockups.order.show');
});

Route::get('/concerts/{id}', 'ConcertController@show')->name('concerts.show');
Route::post('/concerts/{id}/orders', 'ConcertOrderController@store');
Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('auth.show-login');
Route::post('/login', 'Auth\LoginController@login')->name('auth.login');
Route::post('/logout', 'Auth\LoginController@logout')->name('auth.logout');

Route::group([
    'middleware' => 'auth',
    'prefix' => 'backstage',
    'namespace' => 'Backstage'
], function () {
    Route::get('/concerts', 'ConcertController@index')->name('backstage.concert.index');
    Route::get('/concerts/new', 'ConcertController@create')->name('backstage.concert.new');
    Route::post('/concerts', 'ConcertController@store');
    Route::get('/concerts/{id}/edit', 'ConcertController@edit')->name('backstage.concert.edit');
    Route::patch('/concerts/{id}', 'ConcertController@update')->name('backstage.concert.update');
});


