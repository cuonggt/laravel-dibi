<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TableRowsController extends Controller
{
    /**
     * @param  string  $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request, $table)
    {
        return response()->json(Dibi::databaseRepository()->rows($table, $request));
    }
}
