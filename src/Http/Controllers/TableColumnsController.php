<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Dibi;

class TableColumnsController extends Controller
{
    public function index($table)
    {
        return Dibi::service()->columns($table);
    }
}
