<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class TableRowsController extends Controller
{
    public function index($table)
    {
        $params = [
            'field' => request('field'),
            'operator' => strtoupper(request('operator')),
            'keyword' => request('keyword'),
        ];

        return Dibi::service()->rows($table, $params);
    }
}
