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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function rows($table, Request $request);
}
