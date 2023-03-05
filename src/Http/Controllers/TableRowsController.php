<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TableRowsController extends Controller
{
    /**
     * Get list of top rows for the given table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $table)
    {
        return response()->json(Dibi::databaseRepository()->rows($table, $request));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request, $table)
    {
        return response()->json(Dibi::databaseRepository()->rows($table, $request));
    }
}
