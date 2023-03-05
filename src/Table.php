<?php

namespace Cuonggt\Dibi;

use Exception;
use JsonSerializable;

class Table extends DBObject implements JsonSerializable
{
    /**
     * @var string
     */
    public $tableCatalog;

    /**
     * @var string
     */
    public $tableSchema;

    /**
     * @var string
     */
    public $tableName;

    /**
     * @var string
     */
    public $tableType;

    /**
     * @var array
     */
    public $columns;

    /**
     * Set the columns of the table.
     *
     * @param  array|null  $columns
     * @return $this|array
     */
    public function columns(array $columns = null)
    {
        if (is_null($columns)) {
            return $this->columns;
        }

        $this->columns = $columns;

        return $this;
    }

    /**
     * Get the JSON serializable fields for the object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'tableCatalog' => $this->tableCatalog,
            'tableSchema' => $this->tableSchema,
            'tableName' => $this->tableName,
            'tableType' => $this->tableType,
            'columns' => $this->columns,
        ];
    }

    /**
     * Provide dynamic access to the object's methods as properties.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (method_exists($this, $key)) {
            return $this->{$key}();
        }

        throw new Exception("No property or method [{$key}] exists on this object.");
    }
}
