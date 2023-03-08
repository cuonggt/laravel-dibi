<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SqlQueryController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function run(Request $request)
    {
        return Dibi::databaseRepository()->runSqlQuery($request->sql_query);
    }
}
