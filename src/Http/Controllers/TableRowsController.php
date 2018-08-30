<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class TableRowsController extends Controller
{
    public function index($table)
    {
        $params = [
            'field' => request('field'),
            'operator' => strtoupper(request('operator', '=')),
            'keyword' => request('keyword'),
            'sorting' => request('sorting'),
            'direction' => request('direction', 'asc'),
        ];

        return Dibi::service()->rows($table, $params);
    }

    public function update($table)
    {
        return Dibi::service()->updateRow($table, request(['row', 'column', 'value']));
    }
}
