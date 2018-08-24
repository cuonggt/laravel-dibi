<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class TableColumnsController extends Controller
{
    public function index($table)
    {
        return Dibi::service()->columns($table);
    }
}
