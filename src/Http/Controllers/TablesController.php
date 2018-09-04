<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class TablesController extends Controller
{
    /**
     * Get list of tables.
     *
     * @return array
     */
    public function index()
    {
        return Dibi::service()->tables();
    }

    /**
     * Get the table for the given name.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        return response()->json(Dibi::service()->table($name));
    }
}
