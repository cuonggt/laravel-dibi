<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // Table Routes
    Route::get('/tables', 'TablesController@index')->name('dibi.tables.index');
    Route::get('/tables/{table}', 'TablesController@show')->name('dibi.tables.show');
    Route::get('/tables/{table}/columns', 'TableColumnsController@index')->name('dibi.tables.columns.index');
    Route::get('/tables/{table}/rows', 'TableRowsController@index')->name('dibi.tables.rows.index');
});

// Catch-all Route...
Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('dibi.index');
