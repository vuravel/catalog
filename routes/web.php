<?php

Route::namespace('Vuravel\Catalog\Http\Controllers')
	->middleware('web')->as('vuravel-catalog.')
	->group(function(){

	Route::post('vuravel/catalog/browse', 'CatalogController@browse')->name('browse');

	Route::post('vuravel/catalog/order', 'CatalogController@setOrder')->name('order');

	Route::post('vuravel/catalog/db/delete/{id}', 'CatalogController@deleteRecord')->name('db.delete');

});