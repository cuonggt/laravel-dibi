<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // Table Routes
    Route::get('/tables', 'TablesController@index')->name('dibi.tables.index');
    Route::get('/tables/{table}', 'TablesController@show')->name('dibi.tables.show');
    Route::get('/tables/{table}/columns', 'TableColumnsController@index')->name('dibi.tables.columns.index');
    Route::get('/tables/{table}/indexes', 'TableIndexesController@index')->name('dibi.tables.indexes.index');
    Route::get('/tables/{table}/rows', 'TableRowsController@index')->name('dibi.tables.rows.index');
    Route::post('/tables/{table}/rows', 'TableRowsController@store')->name('dibi.tables.rows.store');
    Route::put('/tables/{table}/rows', 'TableRowsController@update')->name('dibi.tables.rows.update');
    Route::delete('/tables/{table}/rows', 'TableRowsController@destroy')->name('dibi.tables.rows.destroy');
});

// Catch-all Route...
Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('dibi.index');
