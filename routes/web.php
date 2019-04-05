<?php

Route::get('/mockups/order', function () {
    return view('mockups.order.show');
});

Route::get('/concerts/{id}', 'ConcertController@show');
Route::post('/concerts/{id}/orders', 'ConcertOrderController@store');
Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Route::post('/login', 'Auth\LoginController@login');