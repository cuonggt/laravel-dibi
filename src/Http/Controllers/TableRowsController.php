<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class TableRowsController extends Controller
{
    /**
     * Get list of top rows for the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Add row to the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function store($table)
    {
        return Dibi::service()->addRow(
            $table,
            request('row')
        );
    }

    /**
     * Update the table's row.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function update($table)
    {
        return Dibi::service()->updateRow(
            $table,
            request('row'),
            request('column'),
            request('value')
        );
    }

    /**
     * Delete row from the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy($table)
    {
        return Dibi::service()->deleteRow(
            $table,
            request('row')
        );
    }
}
