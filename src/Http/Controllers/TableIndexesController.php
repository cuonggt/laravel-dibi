<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

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
        return Dibi::service()->indexes($table);
    }
}
