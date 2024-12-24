<?php

use Cuonggt\Dibi\Dibi;
use Illuminate\Support\Facades\Route;

Route::namespace('Cuonggt\Dibi\Http\Controllers')
    ->prefix(Dibi::path())
    ->middleware(config('dibi.middleware', []))
    ->group(function () {
        Route::post('/api/select-connection', 'SelectConnectionController@select');
        Route::post('/api/sql-query', 'SqlQueryController@run');
        Route::post('/api/tables/{table}/rows/filter', 'TableRowsController@filter');

        Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('dibi');
    });
