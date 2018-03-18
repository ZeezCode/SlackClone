<?php

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', 'PageController@index')->name('index');
Route::resource('server', 'ServerController');