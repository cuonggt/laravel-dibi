<?php

namespace Cuonggt\Dibi;

class TableIndex extends DBObject
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
}
