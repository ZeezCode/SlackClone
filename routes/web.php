<?php

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'PageController@index')->name('index');

Route::get('api/getMessages', 'PageController@getMessages')->name('api.getMessages');
Route::get('api/sendMessage', 'PageController@sendMessage')->name('api.sendMessage');

Route::resource('server', 'ServerController');