<?php

namespace Cuonggt\Dibi\Http\Controllers;

class TableColumnsController extends Controller
{
    /**
     * Get list of columns for the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function index($table)
    {
        return $this->database->columns($table);
    }
}
