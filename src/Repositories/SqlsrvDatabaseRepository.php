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
     *
     * SQL Server has no INFORMATION_SCHEMA view for indexes, so this reads the
     * sys catalog views instead. Those are already scoped to the connected
     * database, which is why this query takes no binding unlike the others.
     * Column names are aliased to match the shape the other drivers return.
     *
     * Heaps are excluded, since they appear in sys.indexes with a null name.
     * Columns an index merely INCLUDEs are reported with a key_ordinal of 0,
     * which is how SQL Server distinguishes them from key columns; they are
     * listed after the key columns of the same index.
     */
    protected function rawIndexes()
    {
        return $this->db->select(
            'SELECT
                DB_NAME() AS TABLE_CATALOG,
                s.name AS TABLE_SCHEMA,
                t.name AS TABLE_NAME,
                CASE WHEN i.is_unique = 1 THEN 0 ELSE 1 END AS NON_UNIQUE,
                s.name AS INDEX_SCHEMA,
                i.name AS INDEX_NAME,
                ic.key_ordinal AS SEQ_IN_INDEX,
                c.name AS COLUMN_NAME,
                i.type_desc AS INDEX_TYPE
            FROM sys.indexes i
            INNER JOIN sys.tables t ON t.object_id = i.object_id
            INNER JOIN sys.schemas s ON s.schema_id = t.schema_id
            INNER JOIN sys.index_columns ic ON ic.object_id = i.object_id AND ic.index_id = i.index_id
            INNER JOIN sys.columns c ON c.object_id = ic.object_id AND c.column_id = ic.column_id
            WHERE i.name IS NOT NULL
            ORDER BY s.name ASC, t.name ASC, i.name ASC, ic.is_included_column ASC, ic.key_ordinal ASC'
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
            'tableName' => $rawIndex->TABLE_SCHEMA.'.'.$rawIndex->TABLE_NAME,
            'nonUnique' => (bool) $rawIndex->NON_UNIQUE,
            'indexSchema' => $rawIndex->INDEX_SCHEMA,
            'indexName' => $rawIndex->INDEX_NAME,
            'seqInIndex' => $rawIndex->SEQ_IN_INDEX,
            'columnName' => $rawIndex->COLUMN_NAME,
            'indexType' => $rawIndex->INDEX_TYPE,
        ]);
    }
}
