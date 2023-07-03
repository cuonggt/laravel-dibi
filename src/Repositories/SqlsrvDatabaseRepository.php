<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Table;
use Cuonggt\Dibi\TableColumn;
use Cuonggt\Dibi\TableIndex;

class SqlsrvDatabaseRepository extends AbstractDatabaseRepository
{
    /**
     * @return array
     */
    protected function stringDataTypes()
    {
        return [
            'char',
            'varchar',
            'text',
            'nchar',
            'nvarchar',
            'ntext',
            'binary',
            'varbinary',
            'image',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function rawTables()
    {
        return $this->db->select(
            'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_CATALOG = ? ORDER BY TABLE_SCHEMA ASC, TABLE_TYPE ASC, TABLE_NAME ASC',
            [$this->getName()]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawTableToObject($rawTable)
    {
        return (new Table)->setRaw((array) $rawTable)->map([
            'tableCatalog' => $rawTable->TABLE_CATALOG,
            'tableSchema' => $rawTable->TABLE_SCHEMA,
            'tableName' => $rawTable->TABLE_SCHEMA.'.'.$rawTable->TABLE_NAME,
            'tableType' => $rawTable->TABLE_TYPE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawColumns()
    {
        return $this->db->select(
            'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_CATALOG = ? ORDER BY TABLE_SCHEMA ASC, ORDINAL_POSITION ASC',
            [$this->getName()]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawColumnToObject($rawColumn)
    {
        return (new TableColumn)->setRaw((array) $rawColumn)->map([
            'tableCatalog' => $rawColumn->TABLE_CATALOG,
            'tableSchema' => $rawColumn->TABLE_SCHEMA,
            'tableName' => $rawColumn->TABLE_SCHEMA.'.'.$rawColumn->TABLE_NAME,
            'columnName' => $rawColumn->COLUMN_NAME,
            'ordinalPosition' => $rawColumn->ORDINAL_POSITION,
            'columnDefault' => $rawColumn->COLUMN_DEFAULT,
            'isNullable' => $rawColumn->IS_NULLABLE == 'YES',
            'dataType' => $rawColumn->DATA_TYPE,
            'characterMaximumLength' => $rawColumn->CHARACTER_MAXIMUM_LENGTH,
            'characterOctetLength' => $rawColumn->CHARACTER_OCTET_LENGTH,
            'numericPrecision' => $rawColumn->NUMERIC_PRECISION,
            'numericScale' => $rawColumn->NUMERIC_SCALE,
            'datetimePrecision' => $rawColumn->DATETIME_PRECISION,
            'characterSetName' => $rawColumn->CHARACTER_SET_NAME,
            'collationName' => $rawColumn->COLLATION_NAME,
            'isStringDataType' => in_array($rawColumn->DATA_TYPE, $this->stringDataTypes()),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawIndexes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawIndexToObject($rawIndex)
    {
        return (new TableIndex)->setRaw((array) $rawIndex)->map([]);
    }
}
