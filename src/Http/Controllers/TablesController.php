<?php

namespace Cuonggt\Dibi\Http\Controllers;

class TablesController extends Controller
{
    /**
     * Get list of tables.
     *
     * @return array
     */
    public function index()
    {
        return $this->database->tables();
    }

    /**
     * Get the table for the given name.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        return response()->json($this->database->table($name));
    }
}
