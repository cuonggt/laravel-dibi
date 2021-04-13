<?php

namespace Cuonggt\Dibi\Contracts;

use Illuminate\Http\Request;

interface DatabaseRepository
{
    /**
     * Get the database name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get list of Dibi Table instances.
     *
     * @return \Illuminate\Support\Collection
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
     * Get list of Dibi TableColumn instances for the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Support\Collection
     */
    public function columns($table);

    /**
     * Get list of Dibi TableIndex instances for the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Support\Collection
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
}
