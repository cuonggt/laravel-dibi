<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Cuonggt\Dibi\Http\Controllers')
    ->prefix(config('dibi.path', 'dibi'))
    ->middleware(config('dibi.middleware', []))
    ->group(function () {
        Route::get('/api/tables', 'TablesController@index');
        Route::get('/api/tables/{table}', 'TablesController@show');
        Route::get('/api/tables/{table}/rows', 'TableRowsController@index');
        Route::post('/api/tables/{table}/rows/filter', 'TableRowsController@filter');

        Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('dibi');
    });
