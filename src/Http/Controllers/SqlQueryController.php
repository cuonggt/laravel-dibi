<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SqlQueryController extends Controller
{
    public function run(Request $request)
    {
        return [
            'results' => collect(explode(';', $request->sql_query))->filter()->map(function ($query) {
                return Dibi::databaseRepository()->runSqlQuery($query);
            }),
        ];
    }
}
