<?php

namespace Cuonggt\Dibi;

use JsonSerializable;

class TableColumn extends DBObject implements JsonSerializable
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
    public $columnName;

    /**
     * @var int
     */
    public $ordinalPosition;

    /**
     * @var mixed
     */
    public $columnDefault;

    /**
     * @var bool
     */
    public $isNullable;

    /**
     * @var string
     */
    public $dataType;

    /**
     * @var int
     */
    public $characterMaximumLength;

    /**
     * @var int
     */
    public $characterOctetLength;

    /**
     * @var int
     */
    public $numericPrecision;

    /**
     * @var int
     */
    public $numericScale;

    /**
     * @var int
     */
    public $datetimePrecision;

    /**
     * @var string
     */
    public $characterSetName;

    /**
     * @var string
     */
    public $collationName;

    /**
     * @var bool
     */
    public $isStringDataType;

    /**
     * @var bool
     */
    public $shouldHideValue = false;

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
            'columnName' => $this->columnName,
            'ordinalPosition' => $this->ordinalPosition,
            'columnDefault' => $this->columnDefault,
            'isNullable' => $this->isNullable,
            'dataType' => $this->dataType,
            'characterMaximumLength' => $this->characterMaximumLength,
            'characterOctetLength' => $this->characterOctetLength,
            'numericPrecision' => $this->numericPrecision,
            'numericScale' => $this->numericScale,
            'datetimePrecision' => $this->datetimePrecision,
            'characterSetName' => $this->characterSetName,
            'collationName' => $this->collationName,
            'isStringDataType' => $this->isStringDataType,
            'shouldHideValue' => $this->isStringDataType && $this->characterMaximumLength > 255,
        ];
    }
}
