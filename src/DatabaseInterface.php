<?php

namespace Cuonggt\Dibi;

interface DatabaseInterface
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
     * Get list of top rows for the given table.
     *
     * @param  string  $table
     * @param  array  $params
     * @return array
     */
    public function rows($table, $params);

    /**
     * Update a table's row.
     *
     * @param  string  $table
     * @param  array  $row
     * @param  array  $column
     * @param  mixed|null  $value
     * @return int
     */
    public function updateRow($table, $row, $column, $value = null);
}
