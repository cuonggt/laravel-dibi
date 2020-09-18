<?php

namespace Cuonggt\Dibi\Contracts;

use Illuminate\Http\Request;

interface DatabaseRepository
{
    /**
     * Get list of Dibi Table instances.
     *
     * @return array
     */
    public function tables();

    /**
     * Get the Dibi Table instance for the given table.
     *
     * @param  string  $table
     * @return \Cuonggt\Dibi\Table
     */
    public function table($table);

    /**
     * Get list of Dibi Column instances for the given table.
     *
     * @param  string  $table
     * @return array
     */
    public function columns($table);

    /**
     * Get list of Dibi TableIndex instances for the given table.
     *
     * @param  string  $table
     * @return array
     */
    public function indexes($table);

    /**
     * Get list of records for the given table.
     *
     * @param  string  $table
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function rows($table, Request $request);

    /**
     * Add a row to the given table.
     *
     * @param  string  $table
     * @param  array  $row
     * @return int
     */
    public function addRow($table, $row);

    /**
     * Update a row of the given table.
     *
     * @param  string  $table
     * @param  array  $row
     * @param  array  $column
     * @param  mixed|null  $value
     * @return int
     */
    public function updateRow($table, $row, $column, $value = null);

    /**
     * Delete a row from the given table.
     *
     * @param  string  $table
     * @param  array  $row
     * @return int
     */
    public function deleteRow($table, $row);
}
