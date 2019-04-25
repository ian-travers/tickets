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

Route::post('/register', 'Auth\RegisterController@register')->name('auth.register');

Route::get('/invitations/{code}', 'InvitationsController@show')->name('invitations.show');

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
    Route::post('/published-concerts', 'PublishedConcertController@store')->name('backstage.published-concert.store');
    Route::get('/published-concerts/{id}/orders', 'PublishedConcertOrdersController@index')->name('backstage.published-concert-orders.index');

    Route::get('/concerts/{id}/messages/new', 'ConcertMessagesController@create')->name('backstage.concert-messages.new');
    Route::post('/concerts/{id}/messages', 'ConcertMessagesController@store')->name('backstage.concert-messages.store');
});


