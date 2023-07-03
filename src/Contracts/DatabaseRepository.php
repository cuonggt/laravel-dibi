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
     * Run given raw SQL query.
     *
     * @param  string  $sqlQuery
     * @return mixed
     */
    public function runSqlQuery($sqlQuery);

    /**
     * Get the Dibi InformationSchema instance.
     *
     * @return \Cuonggt\Dibi\InformationSchema
     */
    public function informationSchema();

    /**
     * Get list of Dibi Table instances.
     *
     * @return array
     */
    public function tables();

    /**
     * Get list of Dibi TableColumn instances.
     *
     * @return \Illuminate\Support\Collection
     */
    public function columns();

    /**
     * Get list of Dibi TableIndex instances.
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexes();

    /**
     * Get list of records for the given table.
     *
     * @param  string  $table
     * @return array
     */
    public function rows($table, Request $request);
}
