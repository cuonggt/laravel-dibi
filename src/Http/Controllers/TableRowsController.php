<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Illuminate\Http\Request;

class TableRowsController extends Controller
{
    /**
     * Get list of top rows for the given table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $table)
    {
        return $this->database->rows($table, $request);
    }

    /**
     * Add row to the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $table)
    {
        return $this->database->addRow(
            $table,
            $request->row
        );
    }

    /**
     * Update the table's row.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $table)
    {
        return $this->database->updateRow(
            $table,
            $request->row,
            $request->column,
            $request->value
        );
    }

    /**
     * Delete row from the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $table)
    {
        return $this->database->deleteRow(
            $table,
            $request->row
        );
    }
}
