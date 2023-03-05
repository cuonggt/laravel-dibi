<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Routing\Controller;

class TablesController extends Controller
{
    /**
     * Get list of tables.
     *
     * @return \Illuminate\Http\JsonResponse.
     */
    public function index()
    {
        return response()->json(Dibi::databaseRepository()->tables());
    }

    /**
     * Get the table for the given name.
     *
     * @param  string  $name
     * @return \Illuminate\Http\JsonResponse.
     */
    public function show($name)
    {
        return response()->json([
            'info' => Dibi::databaseRepository()->table($name),
            'columns' => Dibi::databaseRepository()->columns($name),
            'indexes' => Dibi::databaseRepository()->indexes($name),
        ]);
    }
}
