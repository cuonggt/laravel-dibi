<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Table;
use Cuonggt\Dibi\TableColumn;
use Cuonggt\Dibi\TableIndex;

class MysqlDatabaseRepository extends AbstractDatabaseRepository
{
    /**
     * @return array
     */
    protected function stringDataTypes()
    {
        return [
            'char',
            'varchar',
            'binary',
            'varbinary',
            'tinyblob',
            'tinytext',
            'text',
            'blob',
            'mediumtext',
            'mediumblob',
            'longtext',
            'longblob',
            'enum',
            'set',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function rawTables()
    {
        return $this->db->select(
            'SELECT * FROM information_schema.tables WHERE table_schema = ? ORDER BY table_name ASC',
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
            'tableName' => $rawTable->TABLE_NAME,
            'tableType' => $rawTable->TABLE_TYPE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawColumns()
    {
        return $this->db->select(
            'SELECT * FROM information_schema.columns WHERE table_schema = ? ORDER BY table_name ASC, ordinal_position ASC',
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
            'tableName' => $rawColumn->TABLE_NAME,
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
        return $this->db->select(
            'SELECT * FROM information_schema.statistics WHERE table_schema = ? ORDER BY table_name ASC',
            [$this->getName()]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawIndexToObject($rawIndex)
    {
        return (new TableIndex)->setRaw((array) $rawIndex)->map([
            'tableCatalog' => $rawIndex->TABLE_CATALOG,
            'tableSchema' => $rawIndex->TABLE_SCHEMA,
            'tableName' => $rawIndex->TABLE_NAME,
            'nonUnique' => (bool) $rawIndex->NON_UNIQUE,
            'indexSchema' => $rawIndex->INDEX_SCHEMA,
            'indexName' => $rawIndex->INDEX_NAME,
            'seqInIndex' => $rawIndex->SEQ_IN_INDEX,
            'columnName' => $rawIndex->COLUMN_NAME,
            'indexType' => $rawIndex->INDEX_TYPE,
        ]);
    }
}
