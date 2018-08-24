<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class TableRowsController extends Controller
{
    public function index($table)
    {
        return Dibi::service()->rows($table);
    }
}
