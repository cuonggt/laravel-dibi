<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class TableIndexesController extends Controller
{
    public function index($table)
    {
        return Dibi::service()->indexes($table);
    }
}
