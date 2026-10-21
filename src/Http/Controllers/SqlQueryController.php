<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SqlQueryController extends Controller
{
    public function run(Request $request)
    {
        $repository = Dibi::databaseRepository();

        return [
            'results' => collect($repository->splitSqlQuery($request->sql_query))
                ->map(function ($query) use ($repository) {
                    return $repository->runSqlQuery($query);
                }),
        ];
    }
}
