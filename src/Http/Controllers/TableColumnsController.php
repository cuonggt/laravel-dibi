<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

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
        return Dibi::service()->columns($table);
    }
}
