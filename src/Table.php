<?php

namespace Cuonggt\Dibi;

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
     * @var array
     */
    public $indexes;

    /**
     * Set the columns of the table.
     *
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
     * Set the indexes of the table.
     *
     * @return $this|array
     */
    public function indexes(array $indexes = null)
    {
        if (is_null($indexes)) {
            return $this->indexes;
        }

        $this->indexes = $indexes;

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
            'indexes' => $this->indexes,
        ];
    }
}
