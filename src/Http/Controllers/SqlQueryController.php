<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SqlQueryController extends Controller
{
    public function run(Request $request)
    {
        $queries = explode(';', $request->sql_query);

        return [
            'results' => collect($queries)->filter()->map(function ($query) {
                return Dibi::databaseRepository()->runSqlQuery($query);
            }),
        ];
    }
}
