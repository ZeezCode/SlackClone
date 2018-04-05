<?php

Auth::routes();

Route::get('/', 'PageController@index')->name('index');

Route::get('api/getMessages', 'PageController@getMessages')->name('api.getMessages');
Route::get('api/sendMessage', 'PageController@sendMessage')->name('api.sendMessage');

Route::resource('server', 'ServerController');

Route::get('invite/{invite_id}', 'ServerController@invite')->name('server.invite');
Route::post('invite/{id}', 'ServerController@join')->name('server.join');

Route::get('channel', function() {
   return redirect('server')->withErrors(['GET requests are not accepted to the /channel address.']);
});
Route::post('channel', 'ChannelController@store')->name('channel.store');
Route::put('channel/{id}', 'ChannelController@update')->name('channel.update');
Route::delete('channel/{id}', 'ChannelController@destroy')->name('channel.destroy');
