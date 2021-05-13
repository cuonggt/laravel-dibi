<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Illuminate\Http\Request;

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
        return response()->json($this->database->rows($table, $request));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request, $table)
    {
        return response()->json($this->database->rows($table, $request));
    }
}
