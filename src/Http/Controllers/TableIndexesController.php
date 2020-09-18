<?php

namespace Cuonggt\Dibi\Http\Controllers;

class TableIndexesController extends Controller
{
    /**
     * Get list of indexes for the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function index($table)
    {
        return $this->database->indexes($table);
    }
}
