<?php

namespace Cuonggt\Dibi;

use JsonSerializable;

class TableIndex extends DBObject implements JsonSerializable
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
     * @var bool
     */
    public $nonUnique;

    /**
     * @var string
     */
    public $indexSchema;

    /**
     * @var string
     */
    public $indexName;

    /**
     * @var int
     */
    public $seqInIndex;

    /**
     * @var string
     */
    public $columnName;

    /**
     * @var string
     */
    public $indexType;

    /**
     * Get the JSON serializable fields for the object.
     *
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return [
            'tableCatalog' => $this->tableCatalog,
            'tableSchema' => $this->tableSchema,
            'tableName' => $this->tableName,
            'nonUnique' => $this->nonUnique,
            'indexSchema' => $this->indexSchema,
            'indexName' => $this->indexName,
            'seqInIndex' => $this->seqInIndex,
            'columnName' => $this->columnName,
            'indexType' => $this->indexType,
        ];
    }
}
