<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/tables', 'TablesController@index');
    Route::get('/tables/{table}', 'TablesController@show');
    Route::get('/tables/{table}/columns', 'TableColumnsController@index');
    Route::get('/tables/{table}/indexes', 'TableIndexesController@index');
    Route::get('/tables/{table}/rows', 'TableRowsController@index');
    Route::post('/tables/{table}/rows', 'TableRowsController@store');
    Route::put('/tables/{table}/rows', 'TableRowsController@update');
    Route::delete('/tables/{table}/rows', 'TableRowsController@destroy');
});

// Catch-all Route...
Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('dibi');
